<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function createForUser(
        User $recipient,
        string $type,
        string $title,
        string $message,
        ?Model $relatedEntity = null,
    ): Notification {
        $attributes = [
            'recipient_user_id' => $recipient->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ];

        if ($relatedEntity !== null) {
            $attributes['related_entity_type'] = $relatedEntity->getMorphClass();
            $attributes['related_entity_id'] = $relatedEntity->getKey();
        }

        return Notification::firstOrCreate($attributes, [
            'isread' => false,
        ]);
    }

    public function createForEmployee(
        int $employeeId,
        string $type,
        string $title,
        string $message,
        ?Model $relatedEntity = null,
    ): ?Notification {
        $user = User::where('employee_id', $employeeId)->first();

        return $user === null
            ? null
            : $this->createForUser($user, $type, $title, $message, $relatedEntity);
    }
}
