<?php

namespace Tests\Feature;

use App\Models\Account;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /** @test */
    public function authenticate()
    {
        $user = Account::factory()->create();

        $this->postJson('login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['token', 'expires_in'])
            ->assertJson(['token_type' => 'bearer']);
    }

    /** @test */
    public function fetch_the_current_user()
    {
        $this->actingAs(Account::factory()->create())
            ->getJson('user')
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'email']);
    }

    /** @test */
    public function log_out()
    {
        $token = $this->postJson('login', [
            'email' => Account::factory()->create()->email,
            'password' => 'password',
        ])->json()['token'];

        $this->postJson("logout?token=$token")
            ->assertSuccessful();

        $this->getJson("user?token=$token")
            ->assertStatus(401);
    }
}
