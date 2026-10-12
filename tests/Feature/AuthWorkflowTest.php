<?php

namespace Tests\Feature;

use App\Models\GuruUtama;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_page(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
        $response->assertSee('Portal Masuk');
    }

    public function test_super_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'super_admin',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_guru_utama_can_login_and_redirects_to_guru_utama_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'guru@test.com',
            'role' => 'guru_utama',
            'password' => Hash::make('secret123'),
        ]);

        GuruUtama::create([
            'user_id' => $user->id,
            'nama' => 'Guru Utama Test',
            'asal_sekolah' => 'SMAN 1 Siak',
            'kabupaten' => 'Siak',
        ]);

        $response = $this->post('/login', [
            'email' => 'guru@test.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/guru-utama');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        User::factory()->create([
            'email' => 'user@test.com',
            'password' => Hash::make('validpassword'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthorized_user_cannot_access_super_admin_area(): void
    {
        $user = User::factory()->create([
            'role' => 'guru_utama',
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_access_guru_utama_portal(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $response = $this->actingAs($user)->get('/guru-utama');

        $response->assertStatus(403);
    }
}
