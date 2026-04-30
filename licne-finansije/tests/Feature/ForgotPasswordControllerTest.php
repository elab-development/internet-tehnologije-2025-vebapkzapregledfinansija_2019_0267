<?php

namespace Tests\Feature;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_send_reset_link()
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'pera@example.com']);

        $response = $this->postJson('/api/password/forgot', [
            'email' => $user->email,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Poslat link za resetovanje lozinke na email adresu ako postoji u sistemu']);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);

        Mail::assertSent(ResetPasswordMail::class);
    }

    #[Test]
    public function cannot_send_reset_link_with_invalid_email()
    {
        $response = $this->postJson('/api/password/forgot', [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message','errors']);
    }

    #[Test]
    public function can_reset_password_with_valid_token()
    {
        $user = User::factory()->create(['email' => 'pera@example.com']);

        // Generiši token i upiši ga u bazu
        $plainToken = 'testtoken123';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => $plainToken,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Lozinka uspesno resetovana']);

        // Proveri da je lozinka stvarno promenjena
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));

        // Proveri da je token obrisan
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    #[Test]
    public function cannot_reset_password_with_invalid_token()
    {
        $user = User::factory()->create(['email' => 'pera@example.com']);

        \DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => \Hash::make('validtoken'),
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/password/reset', [
            'email' => $user->email,
            'token' => 'wrongtoken',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Token za resetovanje lozinke nije validan']);
    }
}
