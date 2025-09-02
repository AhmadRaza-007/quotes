<?php

namespace App\Policies;

use App\Models\User;

class AdminOnly
{
    public static function check(?User $user): bool
    {
        // Adjust according to your schema: user_type === 1 means admin in existing code
        return $user && isset($user->user_type) && (int)$user->user_type === 1;
    }
}
