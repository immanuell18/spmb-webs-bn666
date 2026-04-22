<?php

namespace App\Http\Controllers;

use App\Models\Pendaftar;
use App\Models\PaymentTransaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar) {
            return redirect()->route('siswa.pendaftaran')->with('error', 'Silakan lengkapi pendaftaran terlebih dahulu');
        }

        $transactions = PaymentTransaction::where('pendaftar_id', $pendaftar->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        $bankAccounts = $this->paymentService->getManualBankAccounts();

        return view('siswa.payment.index', compact('pendaftar', 'transactions', 'bankAccounts'));
    }

    public function create(Request $request)
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar) {
            return response()->json(['success' => false, 'message' => 'Pendaftar tidak ditemukan']);
        }

        if ($pendaftar->status === 'PAID') {
            return response()->json(['success' => false, 'message' => 'Pembayaran sudah lunas']);
        }

        $paymentMethod = $request->get('payment_method', 'all');
        
        $result = $this->paymentService->createPayment($pendaftar, $paymentMethod);

        return response()->json($result);
    }

    public function createManual()
    {
        $pendaftar = Pendaftar::where('user_id', Auth::id())->first();
        
        if (!$pendaftar) {
            return redirect()->back()->with('error', 'Pendaftar tidak ditemukan');
        }

        if ($pendaftar->status === 'PAID') {
            return redirect()->back()->with('error', 'Pembayaran sudah lunas');
        }

        $transaction = $this->paymentService->createManualPayment($pendaftar);

        return redirect()->route('payment.manual.show', $transaction->id)
                        ->with('success', 'Transaksi manual berhasil dibuat');
    }

    public function showManual(PaymentTransaction $transaction)
    {
        if ($transaction->pendaftar->user_id !== Auth::id()) {
            abort(403);
        }

        $bankAccounts = $this->paymentService->getManualBankAccounts();

        return view('siswa.payment.manual', compact('transaction', 'bankAccounts'));
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        
        Log::info('Payment webhook received', ['payload' => $payload]);

        $result = $this->paymentService->handleWebhook($payload);

        return response()->json($result);
    }

    public function finish(Request $request)
    {
        $orderId = $request->get('order_id');
        $statusCode = $request->get('status_code');
        $transactionStatus = $request->get('transaction_status');

        $transaction = PaymentTransaction::where('order_id', $orderId)->first();

        if ($transaction) {
            $message = match($transactionStatus) {
                'settlement', 'capture' => 'Pembayaran berhasil! Status pendaftaran Anda telah diperbarui.',
                'pending' => 'Pembayaran sedang diproses. Silakan tunggu konfirmasi.',
                'deny', 'cancel', 'expire' => 'Pembayaran gagal atau dibatalkan.',
                default => 'Status pembayaran tidak diketahui.'
            };

            return redirect()->route('payment.index')->with('info', $message);
        }

        return redirect()->route('payment.index')->with('error', 'Transaksi tidak ditemukan');
    }

    public function unfinish()
    {
        return redirect()->route('payment.index')->with('warning', 'Pembayaran belum selesai');
    }

    public function error()
    {
        return redirect()->route('payment.index')->with('error', 'Terjadi kesalahan dalam proses pembayaran');
    }

    public function status($orderId)
    {
        $result = $this->paymentService->getPaymentStatus($orderId);
        return response()->json($result);
    }

    public function cancel($orderId)
    {
        $transaction = PaymentTransaction::where('order_id', $orderId)->first();
        
        if (!$transaction || $transaction->pendaftar->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $result = $this->paymentService->cancelPayment($orderId);
        return response()->json($result);
    }

    // Admin methods
    public function adminDashboard()
    {
        // Get stats from actual PaymentTransaction table
        $totalTransactions = PaymentTransaction::count();
        $paidTransactions = PaymentTransaction::where('status', 'paid')->count();
        $pendingTransactions = PaymentTransaction::where('status', 'pending')->count();
        $totalRevenue = PaymentTransaction::where('status', 'paid')->sum('amount');

        $stats = [
            'total_transactions' => $totalTransactions,
            'paid_transactions' => $paidTransactions,
            'pending_transactions' => $pendingTransactions,
            'total_revenue' => $totalRevenue,
        ];

        // Get actual recent transactions
        $recentTransactions = PaymentTransaction::with(['pendaftar'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.payment.dashboard', compact('stats', 'recentTransactions'));
    }

    public function adminIndex(Request $request)
    {
        $query = PaymentTransaction::with('pendaftar');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.payment.index', compact('transactions'));
    }

    public function adminShow(PaymentTransaction $transaction)
    {
        $transaction->load('pendaftar');
        return view('admin.payment.show', compact('transaction'));
    }

    public function adminRefund(Request $request, PaymentTransaction $transaction)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0|max:' . $transaction->amount,
            'reason' => 'required|string|max:255'
        ]);

        if (!$transaction->canBeRefunded()) {
            return redirect()->back()->with('error', 'Transaksi tidak dapat dikembalikan');
        }

        $result = $this->paymentService->refundPayment(
            $transaction->order_id, 
            $request->amount
        );

        if ($result['success']) {
            return redirect()->back()->with('success', 'Refund berhasil diproses');
        }

        return redirect()->back()->with('error', $result['message']);
    }
}