<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FinansijskiCilj;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FinansijskiCiljControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_list_all_goals()
    {
        $user = User::factory()->create();
        FinansijskiCilj::factory()->count(2)->create(['idKorisnik' => $user->id]);

        $response = $this->getJson('/api/finansijski-ciljevi');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['idCilj','idKorisnik','naziv','ciljni_iznos','trenutni_iznos','rok']]]);
    }

    #[Test]
    public function can_list_user_goals()
    {
        $user = User::factory()->create();
        FinansijskiCilj::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->getJson('/api/finansijski-ciljevi/korisnik/'.$user->id);

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['idCilj','idKorisnik','naziv','ciljni_iznos','trenutni_iznos','rok']]]);
    }

    #[Test]
    public function can_show_single_goal()
    {
        $user = User::factory()->create();
        $cilj = FinansijskiCilj::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->getJson('/api/finansijski-ciljevi/'.$cilj->id);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                    'data'=>[
                        'idCilj',
                        'idKorisnik',
                        'naziv',
                        'ciljni_iznos',
                        'trenutni_iznos',
                        'rok',
                    ]
                 ]);
    }

    #[Test]
    public function can_create_goal()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/finansijski-ciljevi', [
            'idKorisnik' => $user->id,
            'naziv' => 'Kupovina stana',
            'ciljni_iznos' => 50000,
            'trenutni_iznos' => 10000,
            'rok' => now()->addYear()->toDateString(),
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'idCilj',
                     'idKorisnik',
                     'naziv',
                     'ciljni_iznos',
                     'trenutni_iznos',
                     'rok',
                 ]);

        $this->assertDatabaseHas('finansijski_ciljevi', ['naziv' => 'Kupovina stana']);
    }

    #[Test]
    public function cannot_create_goal_with_invalid_data()
    {
        $response = $this->postJson('/api/finansijski-ciljevi', [
            'idKorisnik' => 999,
            'naziv' => '',
            'ciljni_iznos' => -100,
            'trenutni_iznos' => -50,
            'rok' => 'not-a-date',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message','errors']);
    }

    #[Test]
    public function can_update_goal()
    {
        $user = User::factory()->create();
        $cilj = FinansijskiCilj::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->putJson('/api/finansijski-ciljevi/'.$cilj->id, [
            'trenutni_iznos' => 20000,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['trenutni_iznos' => "20000.00"]);

        $this->assertDatabaseHas('finansijski_ciljevi', ['id' => $cilj->id, 'trenutni_iznos' => 20000]);
    }

    #[Test]
    public function can_delete_goal()
    {
        $user = User::factory()->create();
        $cilj = FinansijskiCilj::factory()->create(['idKorisnik' => $user->id]);

        $response = $this->deleteJson('/api/finansijski-ciljevi/'.$cilj->id);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Finansijski cilj je obrisan']);

        $this->assertDatabaseMissing('finansijski_ciljevi', ['id' => $cilj->id]);
    }

    #[Test]
    public function delete_goal_not_found()
    {
        $response = $this->deleteJson('/api/finansijski-ciljevi/999');

        $response->assertStatus(404)
                 ->assertJson(['message' => 'Finansijski cilj nije pronadjen']);
    }
}
