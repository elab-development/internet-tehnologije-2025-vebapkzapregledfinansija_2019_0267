<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Budzet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BudzetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_all_budgets()
    {
        $user = User::factory()->create();
        Budzet::factory()->count(2)->create(['idKorisnik' => $user->id]);

        $response = $this->getJson('/api/budzeti');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    #[Test]
    public function can_list_user_budgets()
    {
        $user = User::factory()->create();
        Budzet::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->getJson('/api/budzeti/korisnik/'.$user->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    #[Test]
    public function can_show_single_budget()
    {
        $user = User::factory()->create();
        $budzet = Budzet::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->getJson('/api/budzeti/'.$budzet->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'data' => [
                        'idBudzet',
                        'idKorisnik',
                        'mesec',
                        'godina',
                        'limit',
                        'potroseno',
                    ]
                ]);
    }

    #[Test]
    public function can_create_budget()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/budzeti', [
            'idKorisnik' => $user->id,
            'mesec' => 4,
            'godina' => 2026,
            'limit' => 1000,
            'potroseno' => 200,
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['idBudzet', 'idKorisnik', 'mesec', 'godina', 'limit', 'potroseno']);

        $this->assertDatabaseHas('budzeti', ['idKorisnik' => $user->id, 'mesec' => 4]);
    }

    #[Test]
    public function cannot_create_budget_with_invalid_data()
    {
        $response = $this->postJson('/api/budzeti', [
            'idKorisnik' => 999, // ne postoji
            'mesec' => 13,       // invalid
            'godina' => 2026,
            'limit' => -100,     // invalid
            'potroseno' => -50,  // invalid
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    #[Test]
    public function can_update_budget()
    {
        $user = User::factory()->create();
        $budzet = Budzet::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->putJson('/api/budzeti/'.$budzet->id, [
            'limit' => 2000,
            'potroseno' => 500,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment([
                    'limit' => "2000.00",
                    'potroseno' => "500.00",
                ]);

        $this->assertDatabaseHas('budzeti', ['id' => $budzet->id, 'limit' => 2000]);
    }

    #[Test]
    public function update_budget_not_found()
    {
        $response = $this->putJson('/api/budzeti/999', [
            'limit' => 2000,
        ]);

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Budzet nije pronadjen']);
    }

    #[Test]
    public function can_delete_budget()
    {
        $user = User::factory()->create();
        $budzet = Budzet::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->deleteJson('/api/budzeti/'.$budzet->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Budzet je obrisan']);

        $this->assertDatabaseMissing('budzeti', ['id' => $budzet->id]);
    }

    #[Test]
    public function delete_budget_not_found()
    {
        $response = $this->deleteJson('/api/budzeti/999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Budzet nije pronadjen']);
    }
}

