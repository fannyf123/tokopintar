<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::create(array_merge([
            'name' => 'Test',
            'username' => 'tester',
            'email' => 'tester@example.test',
            'password' => Hash::make('rahasia123'),
        ], $attrs));
    }

    public function test_user_dapat_lihat_halaman_profil(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Profil Saya');
    }

    public function test_user_dapat_update_profil(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->put('/profile', [
                'name' => 'Nama Baru',
                'username' => 'tester_baru',
                'email' => 'baru@example.test',
            ])->assertRedirect('/profile')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
            'username' => 'tester_baru',
            'email' => 'baru@example.test',
        ]);
    }

    public function test_username_unique_validasi(): void
    {
        $this->makeUser(['username' => 'orang_lain', 'email' => 'a@a.test']);
        $user = $this->makeUser();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/profile', [
                'name' => 'X',
                'username' => 'orang_lain',
                'email' => $user->email,
            ])->assertSessionHasErrors('username');
    }

    public function test_user_dapat_ganti_password(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->put('/profile/password', [
                'current_password' => 'rahasia123',
                'password' => 'passwordbaru1',
                'password_confirmation' => 'passwordbaru1',
            ])->assertRedirect('/profile')
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('passwordbaru1', $user->fresh()->password));
    }

    public function test_password_lama_salah_ditolak(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password' => 'salah',
                'password' => 'passwordbaru1',
                'password_confirmation' => 'passwordbaru1',
            ])->assertSessionHasErrors('current_password');
    }
}
