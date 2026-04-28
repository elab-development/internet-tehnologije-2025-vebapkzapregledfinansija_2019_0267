<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class AdminStatsController extends Controller
{
    public function users(Request $request)
    {
        $now=Carbon::now();

        $totalUsers=User::count();
        $totalUsersWithDeleted=User::withTrashed()->count();
        $deletedUsers=User::onlyTrashed()->count();

        $usersLast7Days=User::where('created_at','>=',$now->copy()->subDays(7))->count();
        $usersLast30Days=User::where('created_at','>=',$now->copy()->subDays(30))->count();
        $usersLast365Days=User::where('created_at','>=',$now->copy()->subDays(365))->count();

        $dailyUsers = User::select(
                DB::raw("DATE(created_at) as date"),
                DB::raw("count(*) as count")
        )
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date')
            ->get();

        $byRole=User::select('uloga',DB::raw('count(*) as count'))
            ->groupBy('uloga')
            ->orderByDesc('count')
            ->get();


        return response()->json([
            'total_users'=>$totalUsers,
            'total_users_with_deleted'=>$totalUsersWithDeleted,
            'deleted_users'=>$deletedUsers,
            'users_last_7_days'=>$usersLast7Days,
            'users_last_30_days'=>$usersLast30Days,
            'users_last_365_days'=>$usersLast365Days,
            'daily_users'=>$dailyUsers,
            'by_role'=>$byRole,
        ]);
    }
}
