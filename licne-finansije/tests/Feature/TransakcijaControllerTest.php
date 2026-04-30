<?php

namespace Tests\Feature;

use App\Models\Transakcija;
use App\Models\User;
use App\Models\Kategorija;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransakcijaControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_transactions()
    {
        $user = User::factory()->create();
        $kat = Kategorija::factory()->create(['idKorisnik' => $user->id]);

        Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat->id,
            'iznos' => 100,
            'datum_vreme' => now(),
            'tipTransakcije' => 'PRIHOD',
            'valuta' => 'EUR',
        ]);

        $response = $this->getJson('/api/transakcije');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         ['idTransakcija','idKorisnik','idKategorija','iznos','datum_vreme','tipTransakcije','valuta','opis']
                     ]
                 ]);
    }

    #[Test]
    public function can_show_single_transaction()
    {
        $user = User::factory()->create();
        $kat = Kategorija::factory()->create(['idKorisnik' => $user->id]);
        $transakcija = Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat->id,
            'iznos' => 200,
            'datum_vreme' => now(),
            'tipTransakcije' => 'RASHOD',
            'valuta' => 'USD',
        ]);

        $response = $this->getJson('/api/transakcije/'.$transakcija->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['iznos' => "200.00"]);
    }

    #[Test]
    public function can_create_transaction()
    {
        $user = User::factory()->create();
        $kat = Kategorija::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->postJson('/api/transakcije', [
            'idKorisnik' => $user->id,
            'idKategorija' => $kat->id,
            'iznos' => 300,
            'datum_vreme' => now()->toDateTimeString(),
            'tipTransakcije' => 'PRIHOD',
            'valuta' => 'RSD',
            'opis' => 'Plata',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['opis' => 'Plata']);

        $this->assertDatabaseHas('transakcije', ['iznos' => 300, 'valuta' => 'RSD']);
    }

    #[Test]
    public function cannot_create_transaction_with_invalid_data()
    {
        $response = $this->postJson('/api/transakcije', [
            'idKorisnik' => 999,
            'idKategorija' => 999,
            'iznos' => 'not-a-number',
            'datum_vreme' => 'not-a-date',
            'tipTransakcije' => 'INVALID',
            'valuta' => 'TOOLONG',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message','errors']);
    }

    #[Test]
    public function can_update_transaction()
    {
        $user = User::factory()->create();
        $kat = Kategorija::factory()->create(['idKorisnik' => $user->id]);
        $transakcija = Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat->id,
            'iznos' => 400,
            'datum_vreme' => now(),
            'tipTransakcije' => 'RASHOD',
            'valuta' => 'EUR',
        ]);

        $response = $this->putJson('/api/transakcije/'.$transakcija->id, [
            'iznos' => 500,
            'valuta' => 'USD',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['iznos' => "500.00", 'valuta' => 'USD']);

        $this->assertDatabaseHas('transakcije', ['id' => $transakcija->id, 'iznos' => 500]);
    }

    #[Test]
    public function can_delete_transaction()
    {
        $user = User::factory()->create();
        $kat = Kategorija::factory()->create(['idKorisnik' => $user->id]);
        $transakcija = Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat->id,
            'iznos' => 600,
            'datum_vreme' => now(),
            'tipTransakcije' => 'PRIHOD',
            'valuta' => 'EUR',
        ]);

        $response = $this->deleteJson('/api/transakcije/'.$transakcija->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Transakcija je obrisana']);

        $this->assertDatabaseMissing('transakcije', ['id' => $transakcija->id]);
    }

    #[Test]
    public function cannot_delete_nonexistent_transaction()
    {
        $response = $this->deleteJson('/api/transakcije/999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Transakcija nije pronadjena']);
    }

    #[Test]
    public function can_list_user_transactions()
    {
        $user = User::factory()->create();
        $kat1 = Kategorija::factory()->create(['idKorisnik' => $user->id]);
        $kat2 = Kategorija::factory()->create(['idKorisnik' => $user->id]);

        Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat1->id,
            'iznos' => 100,
            'datum_vreme' => now(),
            'tipTransakcije' => 'PRIHOD',
            'valuta' => 'EUR',
        ]);

        Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat2->id,
            'iznos' => 200,
            'datum_vreme' => now()->addDay(),
            'tipTransakcije' => 'RASHOD',
            'valuta' => 'USD',
        ]);

        $response = $this->getJson('/api/transakcije/korisnik/'.$user->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['iznos' => "100.00"])
                 ->assertJsonFragment(['iznos' => "200.00"]);
    }

    #[Test]
    public function can_list_user_category_transactions()
    {
        $user = User::factory()->create();
        $kat1 = Kategorija::factory()->create(['idKorisnik' => $user->id]);
        $kat2 = Kategorija::factory()->create(['idKorisnik' => $user->id]);

        Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat1->id,
            'iznos' => 300,
            'datum_vreme' => now(),
            'tipTransakcije' => 'PRIHOD',
            'valuta' => 'RSD',
        ]);

        Transakcija::factory()->create([
            'idKorisnik' => $user->id,
            'idKategorija' => $kat2->id,
            'iznos' => 400,
            'datum_vreme' => now()->addDay(),
            'tipTransakcije' => 'RASHOD',
            'valuta' => 'EUR',
        ]);

        $response = $this->getJson('/api/transakcije/korisnik/'.$user->id.'/kategorija/'.$kat1->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['iznos' => "300.00"])
                 ->assertJsonMissing(['iznos' => 400]); // samo kategorija 1
    }
}
