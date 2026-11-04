<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register(): void
    {
        $department = Department::factory()->create();

        $response = $this->postJson('/api/register', [
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'phone_number' => '0912345678',
            'department_id' => $department->id,
            'year_of_study' => 2,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('message', 'registered successful')
            ->assertJsonStructure(['user', 'token']);

        $this->assertDatabaseHas('users', [
            'email' => 'student@example.com',
            'role' => 'student',
        ]);
    }

    public function test_student_cannot_register_with_existing_email(): void
    {
        $department = Department::factory()->create();

        User::factory()->create([
            'email' => 'student@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Another Student',
            'email' => 'student@example.com',
            'phone_number' => '0912345678',
            'department_id' => $department->id,
            'year_of_study' => 2,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_student_can_login(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonStructure(['user', 'token']);
    }

    public function test_student_cannot_login_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => 'password123',
            'role' => 'student',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'student@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid email or password.',
            ]);
    }

    public function test_student_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withToken($token)
            ->postJson('/api/logout');

        $response->assertOk()
            ->assertJson([
                'message' => 'Logout successful.',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logged_out_token_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Log out and revoke the token.
        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();

        // Confirm the token no longer exists.
        $this->assertDatabaseMissing('personal_access_tokens', [
            'token' => hash('sha256', $token),
        ]);

        // Clear cached authentication guards.
        $this->app['auth']->forgetGuards();

        // Try accessing a protected route with the revoked token.
        $this->withToken($token)
            ->getJson('/api/user')
            ->assertUnauthorized();
    }

        public function test_student_cannot_access_admin_enrollment_endpoint(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
        ]);

        $this->actingAs($student, 'sanctum')
            ->postJson('/api/admin/enrollments')
            ->assertForbidden();
    }

    public function test_lecturer_cannot_access_admin_enrollment_endpoint(): void
    {
        $lecturer = User::factory()->create([
            'role' => 'lecturer',
        ]);

        $this->actingAs($lecturer, 'sanctum')
            ->postJson('/api/admin/enrollments')
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_enrollment_endpoint(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments')
            ->assertUnprocessable();
    }
}
