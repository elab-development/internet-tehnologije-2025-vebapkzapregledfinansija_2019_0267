<?php

namespace App\Observers;

use App\Models\Transakcija;

class TransactionObserver
{
    /**
     * Handle the Transakcija "created" event.
     */
    public function created(Transakcija $transakcija): void
    {
        $user = $transakcija->korisnik;

        if (!$user) {
            return;
        }

        $user->increment('poeni', config('gamification.points.transaction'));

        $user->refresh();

        app(\App\Services\UserLevelService::class)->updateUserLevel($user);
    }

    /**
     * Handle the Transakcija "updated" event.
     */
    public function updated(Transakcija $transakcija): void
    {
        //
    }

    /**
     * Handle the Transakcija "deleted" event.
     */
    public function deleted(Transakcija $transakcija): void
    {
        $user = $transakcija->korisnik;

        if (!$user) return;

        $user->decrement('poeni', config('gamification.points.transaction'));

        $user->refresh();

        app(\App\Services\UserLevelService::class)->updateUserLevel($user);
    }

    /**
     * Handle the Transakcija "restored" event.
     */
    public function restored(Transakcija $transakcija): void
    {
        //
    }

    /**
     * Handle the Transakcija "force deleted" event.
     */
    public function forceDeleted(Transakcija $transakcija): void
    {
        //
    }
}
