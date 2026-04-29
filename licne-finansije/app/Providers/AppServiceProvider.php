<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Transakcija;
use App\Models\FinansijskiCilj;
use App\Models\Budzet;
use App\Models\User;
use App\Observers\TransactionObserver;
use App\Observers\FinancialGoalObserver;
use App\Observers\BudgetObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Transakcija::observe(TransactionObserver::class);
        FinansijskiCilj::observe(FinancialGoalObserver::class);
        Budzet::observe(BudgetObserver::class);
        User::observe(UserObserver::class);
    }
}
