<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:admin'])
            ->get('/_test_admin_only', fn () => 'OK_ADMIN');

        Route::middleware(['web', 'auth', 'role:kasir,admin'])
            ->get('/_test_kasir_or_admin', fn () => 'OK_MIX');
    }

    private function makeUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'username' => $role,
            'email' => $role . '@example.test',
            'password' => Hash::make('rahasia123'),
            'role' => $role,
            'aktif' => true,
        ]);
    }

    public function test_admin_boleh_akses_admin_route(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->get('/_test_admin_only')
            ->assertOk()
            ->assertSee('OK_ADMIN');
    }

    public function test_kasir_ditolak_di_admin_route(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_KASIR))
            ->get('/_test_admin_only')
            ->assertForbidden();
    }

    public function test_kasir_boleh_akses_kasir_atau_admin_route(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_KASIR))
            ->get('/_test_kasir_or_admin')
            ->assertOk();
    }

    public function test_gudang_ditolak_di_kasir_atau_admin_route(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_GUDANG))
            ->get('/_test_kasir_or_admin')
            ->assertForbidden();
    }
}
