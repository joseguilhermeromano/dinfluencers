<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase; // Reseta o banco após cada teste

    /** @test */
    public function it_logs_in_successfully_without_remember_token()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_logs_in_successfully_with_remember_token()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $response->assertStatus(200);

        $user->refresh();

        // Verifica se o usuário tem o remember_token setado
        $this->assertNotNull($user->remember_token);
    }

    /** @test */
    public function it_fails_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['error' => 'Credenciais inválidas']);

        $this->assertGuest();
    }

    /** @test */
    public function it_logs_out_successfully()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $token = $response->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Logout realizado com sucesso']);
    }

    /** @test */
    public function it_registers_a_user_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Usuário registrado com sucesso']);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function it_fails_to_access_protected_endpoint_without_token()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
                ->assertJson(['message' => 'Unauthenticated.']);
    }

    /** @test */
    public function it_logs_in_successfully_with_case_insensitive_email()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'USER@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure(['access_token']);
    }


}
