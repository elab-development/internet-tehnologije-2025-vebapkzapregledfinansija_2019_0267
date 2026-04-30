<?php

namespace Tests\Feature;

use App\Models\Podsetnik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PodsetnikControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_reminders()
    {
        $user = User::factory()->create();
        Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Prvi', 'datum_vreme' => now(), 'status' => 1]);
        Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Drugi', 'datum_vreme' => now()->addDay(), 'status' => 0]);

        $response = $this->getJson('/api/podsetnici');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         ['idPodsetnik','idKorisnik','opis','datum_vreme','status']
                     ]
                 ]);
    }

    #[Test]
    public function can_show_single_reminder()
    {
        $user = User::factory()->create();
        $podsetnik = Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Prvi', 'datum_vreme' => now(), 'status' => 1]);

        $response = $this->getJson('/api/podsetnici/'.$podsetnik->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['opis' => 'Prvi']);
    }

    #[Test]
    public function can_list_user_reminders()
    {
        $user = User::factory()->create();
        Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Prvi', 'datum_vreme' => now(), 'status' => 1]);
        Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Drugi', 'datum_vreme' => now()->addDay(), 'status' => 0]);

        $response = $this->getJson('/api/podsetnici/korisnik/'.$user->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['opis' => 'Prvi'])
                 ->assertJsonFragment(['opis' => 'Drugi']);
    }

    #[Test]
    public function can_create_reminder()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/podsetnici', [
            'idKorisnik' => $user->id,
            'opis' => 'Novi podsetnik',
            'datum_vreme' => now()->addDay()->toDateTimeString(),
            'status' => 1,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['opis' => 'Novi podsetnik']);

        $this->assertDatabaseHas('podsetnici', ['opis' => 'Novi podsetnik']);
    }

    #[Test]
    public function can_update_reminder()
    {
        $user = User::factory()->create();
        $podsetnik = Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Stari opis', 'datum_vreme' => now(), 'status' => 1]);

        $response = $this->putJson('/api/podsetnici/'.$podsetnik->id, [
            'opis' => 'Novi opis',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['opis' => 'Novi opis']);

        $this->assertDatabaseHas('podsetnici', ['id' => $podsetnik->id, 'opis' => 'Novi opis']);
    }

    #[Test]
    public function can_delete_reminder()
    {
        $user = User::factory()->create();
        $podsetnik = Podsetnik::factory()->create(['idKorisnik' => $user->id, 'opis' => 'Za brisanje', 'datum_vreme' => now(), 'status' => 1]);

        $response = $this->deleteJson('/api/podsetnici/'.$podsetnik->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Podsetnik je obrisan']);

        $this->assertDatabaseMissing('podsetnici', ['id' => $podsetnik->id]);
    }

    //NEGATIVNI
    #[Test]
    public function cannot_create_reminder_without_required_fields()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/podsetnici', [
            'idKorisnik' => $user->id,
            // fali datum_vreme i status
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message','errors']);
    }

    #[Test]
    public function cannot_create_reminder_with_invalid_user()
    {
        $response = $this->postJson('/api/podsetnici', [
            'idKorisnik' => 999, // ne postoji
            'opis' => 'Nevalidan',
            'datum_vreme' => now()->toDateTimeString(),
            'status' => 1,
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message','errors']);
    }

    #[Test]
    public function cannot_update_reminder_with_invalid_data()
    {
        $user = User::factory()->create();
        $podsetnik = Podsetnik::factory()->create([
            'idKorisnik' => $user->id,
            'opis' => 'Stari opis',
            'datum_vreme' => now(),
            'status' => 1,
        ]);

        $response = $this->putJson('/api/podsetnici/'.$podsetnik->id, [
            'datum_vreme' => 'not-a-date',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message','errors']);
    }

    #[Test]
    public function cannot_update_nonexistent_reminder()
    {
        $response = $this->putJson('/api/podsetnici/999', [
            'opis' => 'Ne postoji',
        ]);

        $response->assertStatus(500); 
    }

    #[Test]
    public function cannot_delete_nonexistent_reminder()
    {
        $response = $this->deleteJson('/api/podsetnici/999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Podsetnik nije pronadjen']);
    }
}

