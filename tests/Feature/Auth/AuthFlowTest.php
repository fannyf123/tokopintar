<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
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

    public function test_login_page_dapat_diakses(): void
    {
        $this->get('/login')->assertOk()->assertSee('TOKOPINTAR');
    }

    public function test_login_dengan_username_berhasil(): void
    {
        $this->makeUser();

        $this->post('/login', [
            'login' => 'tester',
            'password' => 'rahasia123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_login_dengan_email_berhasil(): void
    {
        $this->makeUser();

        $this->post('/login', [
            'login' => 'tester@example.test',
            'password' => 'rahasia123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();
    }

    public function test_login_password_salah_ditolak(): void
    {
        $this->makeUser();

        $this->from('/login')->post('/login', [
            'login' => 'tester',
            'password' => 'salah',
        ])->assertRedirect('/login')->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_logout_berhasil(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_dashboard_butuh_auth(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
