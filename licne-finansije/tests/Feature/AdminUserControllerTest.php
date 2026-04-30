<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
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
    public function admin_can_list_users()
    {
        $headers = $this->actingAsAdmin();

        User::factory()->count(3)->create();

        $response = $this->withHeaders($headers)->getJson('/api/admin/users');

        $response->assertStatus(200)
                 ->assertJsonStructure(
                    [
                        'current_page',
                        'data',
                        'first_page_url',
                        'last_page',
                        'links',
                        'per_page',
                        'total',
                    ]
                 );
    }

    #[Test]
    public function admin_can_search_users_by_email()
    {
        $headers = $this->actingAsAdmin();

        User::factory()->create(['email' => 'target@example.com']);
        User::factory()->create(['email' => 'other@example.com']);

        $response = $this->withHeaders($headers)->getJson('/api/admin/users?search=target');

        $response->assertStatus(200);
        $this->assertTrue(collect($response->json('data'))->contains(fn($u) => $u['email'] === 'target@example.com'));
    }

    #[Test]
    public function admin_can_show_user()
    {
        $headers = $this->actingAsAdmin();

        $user = User::factory()->create();

        $response = $this->withHeaders($headers)->getJson('/api/admin/users/'.$user->id);

        $response->assertStatus(200)
                 ->assertJson(['id' => $user->id]);
    }

    #[Test]
    public function admin_can_create_user()
    {
        $headers = $this->actingAsAdmin();

        $response = $this->withHeaders($headers)->postJson('/api/admin/users', [
            'ime' => 'Pera',
            'prezime' => 'Peric',
            'email' => 'pera@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'uloga' => 'korisnik',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Korisnik uspešno kreiran']);

        $this->assertDatabaseHas('users', ['email' => 'pera@example.com']);
    }

    #[Test]
    public function admin_cannot_create_user_with_existing_email()
    {
        $headers = $this->actingAsAdmin();

        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->withHeaders($headers)->postJson('/api/admin/users', [
            'ime' => 'Pera',
            'prezime' => 'Peric',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    #[Test]
    public function admin_can_update_user()
    {
        $headers = $this->actingAsAdmin();

        $user = User::factory()->create();

        $response = $this->withHeaders($headers)->putJson('/api/admin/users/'.$user->id, [
            'ime' => 'NovoIme',
            'uloga' => 'premium',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Korisnik uspešno ažuriran']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'uloga' => 'premium']);
    }

    #[Test]
    public function admin_can_update_user_role()
    {
        $headers = $this->actingAsAdmin();

        $user = User::factory()->create(['uloga' => 'korisnik']);

        $response = $this->withHeaders($headers)->patchJson('/api/admin/users/'.$user->id.'/role', [
            'uloga' => 'admin',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Uloga korisnika uspešno ažurirana']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'uloga' => 'admin']);
    }

    #[Test]
    public function admin_can_soft_delete_and_restore_user()
    {
        $headers = $this->actingAsAdmin();

        $user = User::factory()->create();

        $deleteResponse = $this->withHeaders($headers)->deleteJson('/api/admin/users/'.$user->id);
        $deleteResponse->assertStatus(200)
                       ->assertJson(['message' => 'Korisnik soft-deleteovan']);

        $this->assertSoftDeleted('users', ['id' => $user->id]);

        $restoreResponse = $this->withHeaders($headers)->postJson('/api/admin/users/'.$user->id.'/restore');
        $restoreResponse->assertStatus(200)
                        ->assertJson(['message' => 'Korisnik vraćen']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'deleted_at' => null]);
    }

    #[Test]
    public function admin_can_force_delete_user()
    {
        $headers = $this->actingAsAdmin();

        $user = User::factory()->create();
        $user->delete();

        $response = $this->withHeaders($headers)->deleteJson('/api/admin/users/'.$user->id.'/force');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Korisnik trajno obrisan']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
