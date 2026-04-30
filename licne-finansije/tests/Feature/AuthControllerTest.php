<?php

namespace Tests\Feature;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function register_success()
    {
        Mail::fake();

        $response = $this->postJson('/api/register', [
            'ime' => 'Pera',
            'prezime' => 'Peric',
            'email' => 'pera@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'user']);

        $this->assertDatabaseHas('users', ['email' => 'pera@example.com']);
        Mail::assertSent(VerifyEmail::class);
    }

    #[Test]
    public function register_validation_error()
    {
        $response = $this->postJson('/api/register', [
            'ime' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    #[Test]
    public function register_email_already_taken()
    {
        User::factory()->create([
            'email' => 'pera@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'ime' => 'Pera',
            'prezime' => 'Peric',
            'email' => 'pera@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors'])
                 ->assertJsonFragment([
                     'message' => 'Validation errors',
                 ]);
    }

    #[Test]
    public function login_unverified_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Email adresa nije verifikovana.']);
    }

    #[Test]
    public function login_wrong_password()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'pogresnaLozinka',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Pogresan email ili lozinka']);
    }

    #[Test]
    public function login_success()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'user', 'token']);
    }

    #[Test]
    public function logout_success()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
                         ->postJson('/api/logout');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Uspesno odjavljivanje']);
    }

    #[Test]
    public function verify_email_success()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        $response = $this->getJson($url);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Email adresa je uspesno verifikovana.']);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function verify_email_invalid_signature()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Fake URL bez validnog potpisa
        $url = url('/api/email/verify/'.$user->id);

        $response = $this->getJson($url);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Verifikacioni link nije validan ili je istekao.']);
    }

    #[Test]
    public function verify_email_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        $response = $this->getJson($url);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Email adresa je vec verifikovana.']);
    }

    #[Test]
    public function update_profile_success()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
                         ->putJson('/api/profile', [
                             'ime' => 'NovoIme',
                             'prezime' => 'NovoPrezime',
                             'password' => 'newpassword123',
                             'password_confirmation' => 'newpassword123',
                         ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Profil uspesno azuriran']);

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    #[Test]
    public function update_profile_email_already_taken()
    {
        $existing = User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
                         ->putJson('/api/profile', [
                             'email' => 'taken@example.com',
                         ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    #[Test]
    public function update_profile_validation_error()
    {
        $user = User::factory()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
                         ->putJson('/api/profile', [
                             'email' => 'not-an-email',
                         ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }
}