<?php

namespace Tests\Feature;

use App\Models\Account;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /** @test */
    public function can_register()
    {
        $this->postJson('register', [
            'email' => 'test@test.app',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'email']);
    }

    /** @test */
    public function can_not_register_with_existing_email()
    {
        Account::factory()->create(['email' => 'test@test.app']);

        $this->postJson('register', [
            'email' => 'test@test.app',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
