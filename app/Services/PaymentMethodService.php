<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use App\Models\Pendaftar;
use Illuminate\Support\Str;

class PaymentMethodService
{
    private $bankAccounts = [
        'bca' => [
            'name' => 'Bank Central Asia (BCA)',
            'account_number' => '1234567890',
            'account_name' => 'SMK Bakti Nusantara 666'
        ],
        'mandiri' => [
            'name' => 'Bank Mandiri',
            'account_number' => '9876543210',
            'account_name' => 'SMK Bakti Nusantara 666'
        ],
        'bni' => [
            'name' => 'Bank Negara Indonesia (BNI)',
            'account_number' => '5555666677',
            'account_name' => 'SMK Bakti Nusantara 666'
        ],
        'bri' => [
            'name' => 'Bank Rakyat Indonesia (BRI)',
            'account_number' => '1111222233',
            'account_name' => 'SMK Bakti Nusantara 666'
        ]
    ];

    public function getAvailableBanks()
    {
        return $this->bankAccounts;
    }

    public function createBankTransferPayment(Pendaftar $pendaftar, string $bankCode, float $amount)
    {
        $bank = $this->bankAccounts[$bankCode] ?? null;
        if (!$bank) {
            throw new \Exception('Bank tidak tersedia');
        }

        return PaymentTransaction::create([
            'pendaftar_id' => $pendaftar->id,
            'order_id' => 'SPMB-' . $pendaftar->no_pendaftaran . '-' . time(),
            'amount' => $amount,
            'currency' => 'IDR',
            'payment_method' => 'bank_transfer',
            'payment_type' => 'bank_transfer',
            'bank_code' => $bankCode,
            'bank_name' => $bank['name'],
            'account_number' => $bank['account_number'],
            'status' => 'pending',
            'expires_at' => now()->addDays(3), // 3 hari untuk transfer
            'payment_data' => [
                'account_name' => $bank['account_name'],
                'instructions' => $this->getBankTransferInstructions($bankCode)
            ]
        ]);
    }

    public function createQRISPayment(Pendaftar $pendaftar, float $amount)
    {
        $qrData = $this->generateQRISData($pendaftar, $amount);
        
        return PaymentTransaction::create([
            'pendaftar_id' => $pendaftar->id,
            'order_id' => 'SPMB-QRIS-' . $pendaftar->no_pendaftaran . '-' . time(),
            'amount' => $amount,
            'currency' => 'IDR',
            'payment_method' => 'qris',
            'payment_type' => 'qris',
            'qr_code_url' => $qrData['qr_url'],
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30), // 30 menit untuk QRIS
            'payment_data' => [
                'qr_string' => $qrData['qr_string'],
                'merchant_name' => 'SMK Bakti Nusantara 666'
            ]
        ]);
    }

    private function generateQRISData(Pendaftar $pendaftar, float $amount)
    {
        // Generate QRIS string (simplified version)
        $merchantId = '936008000000001'; // Example merchant ID
        $terminalId = '12345678';
        $referenceNumber = 'SPMB' . $pendaftar->no_pendaftaran;
        
        // QRIS format (simplified)
        $qrString = "00020101021226580014ID.CO.QRIS.WWW0118{$merchantId}0215{$referenceNumber}5204539953033605802ID5925SMK Bakti Nusantara 6666007Jakarta61051234062070703A016304";
        
        // Generate QR code URL (using online QR generator for demo)
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrString);
        
        return [
            'qr_string' => $qrString,
            'qr_url' => $qrUrl
        ];
    }

    private function getBankTransferInstructions(string $bankCode)
    {
        $instructions = [
            'bca' => [
                'ATM BCA: Transfer > Rekening BCA > Masukkan nomor rekening',
                'Mobile Banking: m-BCA > Transfer > BCA Virtual Account',
                'Internet Banking: KlikBCA > Transfer Dana > Transfer ke BCA'
            ],
            'mandiri' => [
                'ATM Mandiri: Transfer > Sesama Mandiri > Masukkan nomor rekening',
                'Livin by Mandiri: Transfer > Sesama Mandiri',
                'Internet Banking: Mandiri Online > Transfer > Sesama Mandiri'
            ],
            'bni' => [
                'ATM BNI: Menu Lain > Transfer > Rekening BNI',
                'BNI Mobile Banking: Transfer > Antar Rekening BNI',
                'Internet Banking: BNI Internet Banking > Transfer > Antar Rekening BNI'
            ],
            'bri' => [
                'ATM BRI: Transfer > Sesama BRI > Masukkan nomor rekening',
                'BRImo: Transfer > Sesama BRI',
                'Internet Banking: BRI Internet Banking > Transfer > Sesama BRI'
            ]
        ];

        return $instructions[$bankCode] ?? [];
    }

    public function getPaymentFee()
    {
        return 350000; // Biaya pendaftaran Rp 350.000
    }
}