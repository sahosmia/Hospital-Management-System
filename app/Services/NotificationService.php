<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification (and simulate SMS/Email/Push) to a user.
     */
    public function send(int $userId, string $title, string $message, string $type = 'general', ?string $link = null, ?array $metadata = null): Notification
    {
        // Log the simulation of channels
        Log::info("SIMULATING SMS/EMAIL/PUSH to User {$userId}: [{$type}] {$title} - {$message}");

        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
            'link' => $link,
            'metadata' => $metadata ? (is_array($metadata) ? json_encode($metadata) : $metadata) : null,
        ]);
    }
}
