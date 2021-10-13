<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TelegramPolicy
{
    use HandlesAuthorization;

    public function isAdmin(User $user)
    {
        return $user->role === 'admin';
    }

    public function isManager(User $user)
    {
        return $user->role === 'manager';
    }

    public function get(User $user, Message $message)
    {
        return $user->id === $message->user_id;
    }

}
