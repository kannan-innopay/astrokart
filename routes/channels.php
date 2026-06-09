<?php

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// User receives personal notifications (consultation accepted/rejected, wallet updates)
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return $user->id === $userId;
});

// Astrologer receives consultation requests
Broadcast::channel('astrologer.{userId}', function (User $user, int $userId) {
    return $user->id === $userId && $user->isAstrologer();
});

// Consultation presence channel — both participants can join
Broadcast::channel('consultation.{consultationId}', function (User $user, int $consultationId) {
    $consultation = Consultation::find($consultationId);

    if (! $consultation) {
        return false;
    }

    if ($user->id === $consultation->user_id || $user->id === $consultation->astrologer->user_id) {
        return ['id' => $user->id, 'name' => $user->name];
    }

    return false;
});
