<?php

namespace App\Policies;

use App\Models\Astrologer;
use App\Models\User;

class AstrologerPolicy
{
    public function update(User $user, Astrologer $astrologer): bool
    {
        return $user->id === $astrologer->user_id || $user->isAdmin();
    }

    public function updateStatus(User $user, Astrologer $astrologer): bool
    {
        return $user->isAdmin();
    }

    public function toggleOnline(User $user, Astrologer $astrologer): bool
    {
        return $user->id === $astrologer->user_id && $astrologer->canGoOnline();
    }
}
