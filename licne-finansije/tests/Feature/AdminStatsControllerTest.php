<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Carbon\Carbon;

class AdminStatsControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'uloga' => 'admin',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return ['Authorization' => 'Bearer '.$token];
    }

    #[Test]
    public function admin_can_access_stats_and_get_structure()
    {
        $headers = $this->actingAsAdmin();

        $response = $this->withHeaders($headers)->getJson('/api/stats/users');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'total_users',
                     'total_users_with_deleted',
                     'deleted_users',
                     'users_last_7_days',
                     'users_last_30_days',
                     'users_last_365_days',
                     'daily_users',
                     'by_role',
                 ]);
    }

    #[Test]
    public function stats_counts_deleted_users()
    {
        $headers = $this->actingAsAdmin();

        $user = User::factory()->create();
        $user->delete();

        $response = $this->withHeaders($headers)->getJson('/api/stats/users');

        $response->assertStatus(200)
                 ->assertJson([
                     'deleted_users' => 1,
                     'total_users' => 1,
                     'total_users_with_deleted' => 2,
                 ]);
    }

    #[Test]
    public function stats_counts_recent_users()
    {
        $headers = $this->actingAsAdmin();

        User::factory()->create(['created_at' => Carbon::now()->subDays(2)]);
        User::factory()->create(['created_at' => Carbon::now()->subDays(40)]);

        $response = $this->withHeaders($headers)->getJson('/api/stats/users');

        $response->assertStatus(200)
                 ->assertJson([
                     'users_last_7_days' => 2,
                     'users_last_30_days' => 2,
                     'users_last_365_days' => 3,
                 ]);
    }

    #[Test]
    public function stats_groups_by_role()
    {
        $headers = $this->actingAsAdmin();

        User::factory()->count(2)->create(['uloga' => 'korisnik']);
        User::factory()->create(['uloga' => 'admin']);

        $response = $this->withHeaders($headers)->getJson('/api/stats/users');

        $response->assertStatus(200);
        $data = $response->json('by_role');

        $this->assertTrue(collect($data)->contains(fn($item) => $item['uloga'] === 'korisnik' && $item['count'] === 2));
        $this->assertTrue(collect($data)->contains(fn($item) => $item['uloga'] === 'admin' && $item['count'] === 2));
    }

    #[Test]
    public function non_admin_user_cannot_access_stats()
    {
        $user = User::factory()->create([
            'uloga' => 'korisnik',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
                         ->getJson('/api/stats/users');

        $response->assertStatus(403);
    }
}


