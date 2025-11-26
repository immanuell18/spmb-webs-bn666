<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Pendaftar;
use App\Mail\AccountActivationMail;
use App\Mail\DocumentCorrectionMail;
use App\Mail\PaymentInstructionMail;
use App\Mail\PaymentConfirmationMail;
use App\Mail\SelectionResultMail;
use Illuminate\Support\Facades\Mail;

class TestEmailNotifications extends Command
{
    protected $signature = 'test:email-notifications {email} {--type=all}';
    protected $description = 'Test email notifications';

    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');

        // Find user and pendaftar
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found");
            return;
        }

        $pendaftar = Pendaftar::where('user_id', $user->id)->first();

        try {
            switch ($type) {
                case 'activation':
                    Mail::to($email)->send(new AccountActivationMail($user));
                    $this->info('Account activation email sent');
                    break;

                case 'document-rejected':
                    if (!$pendaftar) {
                        $this->error('Pendaftar not found for this user');
                        return;
                    }
                    Mail::to($email)->send(new DocumentCorrectionMail($pendaftar, 'Berkas tidak sesuai persyaratan. Silakan upload ulang.'));
                    $this->info('Document rejection email sent');
                    break;

                case 'document-accepted':
                    if (!$pendaftar) {
                        $this->error('Pendaftar not found for this user');
                        return;
                    }
                    Mail::to($email)->send(new PaymentInstructionMail($pendaftar));
                    $this->info('Payment instruction email sent');
                    break;

                case 'payment-confirmed':
                    if (!$pendaftar) {
                        $this->error('Pendaftar not found for this user');
                        return;
                    }
                    Mail::to($email)->send(new PaymentConfirmationMail($pendaftar));
                    $this->info('Payment confirmation email sent');
                    break;

                case 'result-lulus':
                    if (!$pendaftar) {
                        $this->error('Pendaftar not found for this user');
                        return;
                    }
                    Mail::to($email)->send(new SelectionResultMail($pendaftar, 'lulus'));
                    $this->info('Selection result (lulus) email sent');
                    break;

                case 'result-tidak-lulus':
                    if (!$pendaftar) {
                        $this->error('Pendaftar not found for this user');
                        return;
                    }
                    Mail::to($email)->send(new SelectionResultMail($pendaftar, 'tidak_lulus'));
                    $this->info('Selection result (tidak lulus) email sent');
                    break;

                case 'all':
                    Mail::to($email)->send(new AccountActivationMail($user));
                    $this->info('✓ Account activation email sent');
                    
                    if ($pendaftar) {
                        Mail::to($email)->send(new DocumentCorrectionMail($pendaftar, 'Test rejection message'));
                        $this->info('✓ Document rejection email sent');
                        
                        Mail::to($email)->send(new PaymentInstructionMail($pendaftar));
                        $this->info('✓ Payment instruction email sent');
                        
                        Mail::to($email)->send(new PaymentConfirmationMail($pendaftar));
                        $this->info('✓ Payment confirmation email sent');
                        
                        Mail::to($email)->send(new SelectionResultMail($pendaftar, 'lulus'));
                        $this->info('✓ Selection result email sent');
                    }
                    break;

                default:
                    $this->error('Invalid type. Use: activation, document-rejected, document-accepted, payment-confirmed, result-lulus, result-tidak-lulus, or all');
                    return;
            }

            $this->info('Email notifications sent successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
        }
    }
}