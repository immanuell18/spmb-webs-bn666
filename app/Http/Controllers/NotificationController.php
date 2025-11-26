<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function preferences()
    {
        $user = Auth::user();
        $preferences = $this->notificationService->getUserPreferences($user->id);
        
        return view('notifications.preferences', compact('preferences'));
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'whatsapp_enabled' => 'boolean',
            'event_preferences' => 'array'
        ]);

        $user = Auth::user();
        
        $this->notificationService->updateUserPreferences($user->id, [
            'email_enabled' => $request->boolean('email_enabled'),
            'sms_enabled' => $request->boolean('sms_enabled'),
            'whatsapp_enabled' => $request->boolean('whatsapp_enabled'),
            'event_preferences' => $request->event_preferences ?? []
        ]);

        return redirect()->back()->with('success', 'Preferensi notifikasi berhasil diperbarui');
    }

    public function history()
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getNotificationHistory($user->id);
        
        return view('notifications.history', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }

    public function sendTestNotification(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,sms,whatsapp',
            'message' => 'required|string|max:500'
        ]);

        $user = Auth::user();
        $success = false;

        switch ($request->type) {
            case 'sms':
                $success = $this->notificationService->sendSMS($user->phone ?? '081234567890', $request->message, $user->id);
                break;
            case 'whatsapp':
                $success = $this->notificationService->sendWhatsApp($user->phone ?? '081234567890', $request->message, $user->id);
                break;
        }

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notifikasi test berhasil dikirim' : 'Gagal mengirim notifikasi test'
        ]);
    }

    public function adminNotifications()
    {
        $failedNotifications = $this->notificationService->getFailedNotifications();
        
        return view('admin.notifications', compact('failedNotifications'));
    }

    public function retryNotification($id)
    {
        $success = $this->notificationService->retryFailedNotification($id);
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notifikasi berhasil dikirim ulang' : 'Gagal mengirim ulang notifikasi'
        ]);
    }
}