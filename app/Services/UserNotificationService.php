<?php

namespace App\Services;

use App\Models\Scan;
use App\Models\User;
use App\Notifications\TestStatusNotification;

class UserNotificationService
{
    public function notifyStatusChange(Scan $scan): void
    {
        $usersToNotify = $this->usersToNotify($scan);
        // Ensure $usersToNotify is iterable. Wrap in a collection if it's a single model instance.
        $usersToNotify = $usersToNotify instanceof Illuminate\Database\Eloquent\Model ? collect([$usersToNotify]) : $usersToNotify;


        foreach ($usersToNotify as $user) {
            $status = $scan->status;
            $description = "Test number {$scan->test_id} status changed to {$status}.";
            $state = $this->stateBasedOnStatus($status);

            $user->notify(new TestStatusNotification($status, $description, $state));
        }
    }

    protected function usersToNotify(Scan $scan): iterable
    {
        // Initialize a collection with the primary user's ID to ensure uniqueness from the start.
        $userIds = collect([$scan->test?->laboratory?->user_id]);

        // Get IDs of superAdmins and operators.
        $superAdminIds = User::role('superAdmin')->pluck('id');
        $operatorIds = User::role('operator')->pluck('id');

        // Merge these IDs into the $userIds collection and ensure it's unique to avoid duplicates.
        $userIds = $userIds->merge($superAdminIds)->merge($operatorIds)->unique();

        // Use the unique IDs to retrieve the corresponding User models.
        return User::findMany($userIds->toArray());
    }


    protected function stateBasedOnStatus(string $status): string
    {
        return match ($status) {
            'scanning' => 'info',
            '2x-scanned' => 'warning',
            'scanned' => 'primary',
            'image-ready' => 'success',
            '2x-failed', 'failed' => 'danger',
            default => '',
        };
    }
}
