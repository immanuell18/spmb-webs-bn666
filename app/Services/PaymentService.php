<?php

namespace App\Services;

use App\Models\Pendaftar;
use App\Models\PaymentTransaction;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct()
    {
        $this->configureMidtrans();
    }

    private function configureMidtrans()
    {
        Config::$serverKey = config('payment.gateways.midtrans.server_key');
        Config::$isProduction = config('payment.gateways.midtrans.is_production');
        Config::$isSanitized = config('payment.gateways.midtrans.is_sanitized');
        Config::$is3ds = config('payment.gateways.midtrans.is_3ds');
    }

    public function createPayment(Pendaftar $pendaftar, $paymentMethod = 'all')
    {
        try {
            $orderId = 'SPMB-' . $pendaftar->no_pendaftaran . '-' . time();
            
            // Create payment transaction record
            $transaction = PaymentTransaction::create([
                'pendaftar_id' => $pendaftar->id,
                'order_id' => $orderId,
                'amount' => config('payment.transaction.registration_fee'),
                'currency' => config('payment.transaction.currency'),
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'gateway' => 'midtrans',
                'expires_at' => now()->addHours(config('payment.transaction.expiry_duration'))
            ]);

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => config('payment.transaction.registration_fee'),
                ],
                'customer_details' => [
                    'first_name' => $pendaftar->nama,
                    'email' => $pendaftar->email,
                    'phone' => $pendaftar->dataSiswa->no_hp ?? '',
                ],
                'item_details' => [
                    [
                        'id' => 'registration_fee',
                        'price' => config('payment.transaction.registration_fee'),
                        'quantity' => 1,
                        'name' => 'Biaya Pendaftaran SPMB',
                        'category' => 'Education'
                    ]
                ],
                'enabled_payments' => $this->getEnabledPayments($paymentMethod),
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s O'),
                    'unit' => 'hours',
                    'duration' => config('payment.transaction.expiry_duration')
                ],
                'callbacks' => [
                    'finish' => route('payment.finish'),
                    'unfinish' => route('payment.unfinish'),
                    'error' => route('payment.error')
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            
            $transaction->update([
                'snap_token' => $snapToken,
                'payment_data' => json_encode($params)
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'transaction' => $transaction
            ];

        } catch (\Exception $e) {
            Log::error('Payment creation failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Gagal membuat pembayaran: ' . $e->getMessage()
            ];
        }
    }

    public function handleWebhook($payload)
    {
        try {
            $notification = json_decode($payload, true);
            
            $orderId = $notification['order_id'];
            $statusCode = $notification['status_code'];
            $grossAmount = $notification['gross_amount'];
            $signatureKey = $notification['signature_key'];

            // Verify signature
            $serverKey = config('payment.gateways.midtrans.server_key');
            $mySignatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
            
            if ($signatureKey !== $mySignatureKey) {
                return ['success' => false, 'message' => 'Invalid signature'];
            }

            $transaction = PaymentTransaction::where('order_id', $orderId)->first();
            
            if (!$transaction) {
                return ['success' => false, 'message' => 'Transaction not found'];
            }

            $transactionStatus = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'] ?? null;

            $this->updateTransactionStatus($transaction, $transactionStatus, $fraudStatus, $notification);

            return ['success' => true, 'message' => 'Webhook processed'];

        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function updateTransactionStatus($transaction, $transactionStatus, $fraudStatus, $notification)
    {
        $pendaftar = $transaction->pendaftar;

        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus == 'accept') {
                    $this->markAsPaid($transaction, $pendaftar, $notification);
                }
                break;
            
            case 'settlement':
                $this->markAsPaid($transaction, $pendaftar, $notification);
                break;
            
            case 'pending':
                $transaction->update([
                    'status' => 'pending',
                    'gateway_response' => json_encode($notification)
                ]);
                break;
            
            case 'deny':
            case 'expire':
            case 'cancel':
                $transaction->update([
                    'status' => 'failed',
                    'gateway_response' => json_encode($notification)
                ]);
                break;
        }
    }

    private function markAsPaid($transaction, $pendaftar, $notification)
    {
        $transaction->update([
            'status' => 'paid',
            'paid_at' => now(),
            'gateway_response' => json_encode($notification)
        ]);

        $pendaftar->update(['status' => 'PAID']);

        // Send payment confirmation email
        // event(new PaymentConfirmed($pendaftar, $transaction));
    }

    public function getPaymentStatus($orderId)
    {
        try {
            $status = Transaction::status($orderId);
            return [
                'success' => true,
                'data' => $status
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function cancelPayment($orderId)
    {
        try {
            $cancel = Transaction::cancel($orderId);
            
            $transaction = PaymentTransaction::where('order_id', $orderId)->first();
            if ($transaction) {
                $transaction->update(['status' => 'cancelled']);
            }

            return [
                'success' => true,
                'data' => $cancel
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function refundPayment($orderId, $amount = null)
    {
        try {
            $transaction = PaymentTransaction::where('order_id', $orderId)->first();
            
            if (!$transaction || $transaction->status !== 'paid') {
                return ['success' => false, 'message' => 'Transaction not eligible for refund'];
            }

            $refundAmount = $amount ?? $transaction->amount;
            
            $refund = Transaction::refund($orderId, [
                'amount' => $refundAmount,
                'reason' => 'Refund by admin'
            ]);

            $transaction->update([
                'status' => 'refunded',
                'refund_amount' => $refundAmount,
                'refunded_at' => now()
            ]);

            return [
                'success' => true,
                'data' => $refund
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function getEnabledPayments($method)
    {
        $payments = [];
        
        if ($method === 'all' || $method === 'virtual_account') {
            $payments = array_merge($payments, ['bca_va', 'bni_va', 'bri_va', 'other_va']);
        }
        
        if ($method === 'all' || $method === 'e_wallet') {
            $payments = array_merge($payments, ['gopay', 'shopeepay']);
        }
        
        if ($method === 'all' || $method === 'qris') {
            $payments[] = 'qris';
        }
        
        if ($method === 'all' || $method === 'credit_card') {
            $payments[] = 'credit_card';
        }
        
        if ($method === 'all' || $method === 'bank_transfer') {
            $payments = array_merge($payments, ['echannel', 'permata_va']);
        }

        return $payments;
    }

    public function getManualBankAccounts()
    {
        return config('payment.gateways.manual.bank_accounts');
    }

    public function createManualPayment(Pendaftar $pendaftar)
    {
        $orderId = 'MANUAL-' . $pendaftar->no_pendaftaran . '-' . time();
        
        return PaymentTransaction::create([
            'pendaftar_id' => $pendaftar->id,
            'order_id' => $orderId,
            'amount' => config('payment.transaction.registration_fee'),
            'currency' => config('payment.transaction.currency'),
            'payment_method' => 'manual_transfer',
            'status' => 'pending',
            'gateway' => 'manual',
            'expires_at' => now()->addHours(config('payment.transaction.expiry_duration'))
        ]);
    }
}