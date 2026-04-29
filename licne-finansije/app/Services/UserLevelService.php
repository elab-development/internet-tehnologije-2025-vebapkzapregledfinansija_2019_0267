<?php

namespace App\Services;

use App\Models\User;

class UserLevelService
{
    public function updateUserLevel(User $user)
    {
        if($user->uloga === 'admin') {
            return;
        }

        $points = $user->poeni;

        $levelStep = config('gamification.level_step');
        $premiumLevel = config('gamification.premium_level');

        $level = floor($points / $levelStep);

        $user->nivo = $level;

        $newRole = $level >= $premiumLevel ? 'premium' : 'korisnik';

        $user->updateQuietly([
            'nivo' => $user->nivo,
            'uloga' => $newRole,
        ]);
    }
}