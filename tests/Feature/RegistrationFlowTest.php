<?php

namespace Tests\Feature;

use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_registration_creates_company_user_and_trial(): void
    {
        Plan::create(['name' => 'Basic', 'price' => 99.90, 'max_users' => 50, 'max_trainings' => 20]);

        $response = $this->post('/register', [
            'company_name' => 'Minha Empresa',
            'name' => 'Joao Admin',
            'email' => 'joao@empresa.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('companies', ['name' => 'Minha Empresa', 'slug' => 'minha-empresa']);
        $this->assertDatabaseHas('users', ['email' => 'joao@empresa.com', 'role' => 'admin']);
        $this->assertDatabaseHas('subscriptions', ['status' => 'trial']);
    }
}
