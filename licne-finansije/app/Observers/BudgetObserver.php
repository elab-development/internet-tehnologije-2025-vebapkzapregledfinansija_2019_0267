<?php

namespace App\Observers;

use App\Models\Budzet;

class BudgetObserver
{
    /**
     * Handle the Budzet "created" event.
     */
    public function created(Budzet $budzet): void
    {
        $user = $budzet->korisnik;

        if (!$user) {
            return;
        }

        $user->increment('poeni', config('gamification.points.budget'));

        $user->refresh();

        app(\App\Services\UserLevelService::class)->updateUserLevel($user);
    }

    /**
     * Handle the Budzet "updated" event.
     */
    public function updated(Budzet $budzet): void
    {
        //
    }

    /**
     * Handle the Budzet "deleted" event.
     */
    public function deleted(Budzet $budzet): void
    {
        $user = $budzet->korisnik;

        if (!$user) return;

        $user->decrement('poeni', config('gamification.points.budget'));

        $user->refresh();

        app(\App\Services\UserLevelService::class)->updateUserLevel($user);
    }

    /**
     * Handle the Budzet "restored" event.
     */
    public function restored(Budzet $budzet): void
    {
        //
    }

    /**
     * Handle the Budzet "force deleted" event.
     */
    public function forceDeleted(Budzet $budzet): void
    {
        //
    }
}
