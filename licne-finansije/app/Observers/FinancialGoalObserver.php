<?php

namespace App\Observers;

use App\Models\FinansijskiCilj;

class FinancialGoalObserver
{
    /**
     * Handle the FinansijskiCilj "created" event.
     */
    public function created(FinansijskiCilj $finansijskiCilj): void
    {
        $user = $finansijskiCilj->korisnik;

        if (!$user) {
            return;
        }

        $user->increment('poeni', config('gamification.points.goal'));

        $user->refresh();

        app(\App\Services\UserLevelService::class)->updateUserLevel($user);
    }

    /**
     * Handle the FinansijskiCilj "updated" event.
     */
    public function updated(FinansijskiCilj $finansijskiCilj): void
    {
        //
    }

    /**
     * Handle the FinansijskiCilj "deleted" event.
     */
    public function deleted(FinansijskiCilj $finansijskiCilj): void
    {
        $user = $finansijskiCilj->korisnik;

        if (!$user) return;

        $user->decrement('poeni', config('gamification.points.goal'));

        $user->refresh();

        app(\App\Services\UserLevelService::class)->updateUserLevel($user);
    }

    /**
     * Handle the FinansijskiCilj "restored" event.
     */
    public function restored(FinansijskiCilj $finansijskiCilj): void
    {
        //
    }

    /**
     * Handle the FinansijskiCilj "force deleted" event.
     */
    public function forceDeleted(FinansijskiCilj $finansijskiCilj): void
    {
        //
    }
}
