<?php

namespace Tests\Feature;

use App\Models\Kategorija;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KategorijaControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_categories()
    {
        $user = User::factory()->create();
        Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Hrana']);
        Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Transport']);

        $response = $this->getJson('/api/kategorije');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [
                    ['idKategorija','idKorisnik','naziv','opis']
                ]]);
    }

    #[Test]
    public function can_show_single_category()
    {
        $user = User::factory()->create();
        $kategorija = Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Hrana']);

        $response = $this->getJson('/api/kategorije/'.$kategorija->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['naziv' => 'Hrana']);
    }

    #[Test]
    public function can_list_user_categories()
    {
        $user = User::factory()->create();
        Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Hrana']);
        Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Transport']);

        $response = $this->getJson('/api/kategorije/korisnik/'.$user->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['naziv' => 'Hrana'])
                 ->assertJsonFragment(['naziv' => 'Transport']);
    }

    #[Test]
    public function can_create_category()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/kategorije', [
            'idKorisnik' => $user->id,
            'naziv' => 'Hrana',
            'opis' => 'Troškovi za hranu',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['naziv' => 'Hrana']);

        $this->assertDatabaseHas('kategorije', ['naziv' => 'Hrana']);
    }

    #[Test]
    public function can_update_category()
    {
        $user = User::factory()->create();
        $kategorija = Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Hrana']);

        $response = $this->putJson('/api/kategorije/'.$kategorija->id, [
            'naziv' => 'Transport',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['naziv' => 'Transport']);

        $this->assertDatabaseHas('kategorije', ['id' => $kategorija->id, 'naziv' => 'Transport']);
    }

    #[Test]
    public function can_delete_category()
    {
        $user = User::factory()->create();
        $kategorija = Kategorija::factory()->create(['idKorisnik' => $user->id, 'naziv' => 'Hrana']);

        $response = $this->deleteJson('/api/kategorije/'.$kategorija->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Kategorija je obrisana']);

        $this->assertDatabaseMissing('kategorije', ['id' => $kategorija->id]);
    }
}
