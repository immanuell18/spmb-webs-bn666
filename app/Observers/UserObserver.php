<?php

namespace App\Observers;

use App\Models\User;
use App\Models\AuditLog;
use App\Mail\AccountActivationMail;
use Illuminate\Support\Facades\Mail;

class UserObserver
{
    public function created(User $user): void
    {
        AuditLog::logActivity([
            'user_id' => null, // Don't use auth()->id() during user creation
            'user_name' => 'System',
            'action' => 'create',
            'model_type' => User::class,
            'model_id' => $user->id,
            'new_values' => $this->filterSensitiveData($user->toArray()),
            'description' => "User baru dibuat: {$user->email}",
            'severity' => 'medium'
        ]);

        // Kirim email aktivasi akun untuk pendaftar
        if ($user->role === User::ROLE_PENDAFTAR) {
            try {
                Mail::to($user->email)->send(new AccountActivationMail($user));
            } catch (\Exception $e) {
                \Log::error('Failed to send account activation email: ' . $e->getMessage());
            }
        }
    }

    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        $original = $user->getOriginal();

        // Filter sensitive data
        $changes = $this->filterSensitiveData($changes);
        $oldValues = $this->filterSensitiveData(array_intersect_key($original, $user->getChanges()));

        AuditLog::logActivity([
            'action' => 'update',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $changes,
            'description' => "User diupdate: {$user->email}",
            'severity' => $this->determineSeverity($user->getChanges())
        ]);
    }

    public function deleted(User $user): void
    {
        AuditLog::logActivity([
            'action' => 'delete',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $this->filterSensitiveData($user->toArray()),
            'description' => "User dihapus: {$user->email}",
            'severity' => 'critical'
        ]);
    }

    private function filterSensitiveData(array $data): array
    {
        $sensitiveFields = ['password', 'remember_token'];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[FILTERED]';
            }
        }

        return $data;
    }

    private function determineSeverity(array $changes): string
    {
        $criticalFields = ['role', 'email', 'password'];
        
        foreach ($criticalFields as $field) {
            if (array_key_exists($field, $changes)) {
                return 'critical';
            }
        }

        return 'medium';
    }
}