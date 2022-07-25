<?php

namespace Tests\Feature;

use App\Models\Account;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    /** @test */
    public function update_profile_info()
    {
        $this->actingAs($user = Account::factory()->create())
            ->patchJson('settings/profile', [
                'name' => 'Test User',
                'email' => 'test@test.app',
            ])
            ->assertSuccessful()
            ->assertJsonStructure(['id', 'name', 'email']);

        $this->assertDatabaseHas('re_account', [
            'id' => $user->id,
            'email' => 'test@test.app',
        ]);
    }

    /** @test */
    public function update_password()
    {
        $this->actingAs($user = Account::factory()->create())
            ->patchJson('settings/password', [
                'password' => 'updated',
                'password_confirmation' => 'updated',
            ])
            ->assertSuccessful();

        $this->assertTrue(Hash::check('updated', $user->password));
    }
}
