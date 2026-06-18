<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;
use OpenApi\Attributes as OA;

class AdminStatsController extends Controller
{
    #[OA\Get(
        path: "/api/stats/users",
        summary: "Dobijanje statistike o korisnicima",
        description: "Vraća detaljne statističke podatke o registrovanim, aktivnim i obrisanim korisnicima.",
        operationId: "getAdminUserStats",
        tags: ["Admin Statistika"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitana statistika",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "total_users", type: "integer", example: 150),
                new OA\Property(property: "total_users_with_deleted", type: "integer", example: 165),
                new OA\Property(property: "deleted_users", type: "integer", example: 15),
                new OA\Property(property: "users_last_7_days", type: "integer", example: 12),
                new OA\Property(property: "users_last_30_days", type: "integer", example: 45),
                new OA\Property(property: "users_last_365_days", type: "integer", example: 150),
                new OA\Property(
                    property: "daily_users",
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "date", type: "string", format: "date", example: "2026-06-18"),
                            new OA\Property(property: "count", type: "integer", example: 3)
                        ]
                    )
                ),
                new OA\Property(
                    property: "by_role",
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "uloga", type: "string", example: "user"),
                            new OA\Property(property: "count", type: "integer", example: 145)
                        ]
                    )
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
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
