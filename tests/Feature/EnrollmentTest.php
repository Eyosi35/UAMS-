<?php

namespace Tests\Feature;

use App\Models\CourseSection;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_student_to_section(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
            'capacity' => 35,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => [$student->id],
            ])
            ->assertCreated()
            ->assertJsonPath('assigned_count', 1);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_section_id' => $section->id,
        ]);
    }

    public function test_student_cannot_assign_enrollments(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $this->actingAs($student, 'sanctum')
            ->postJson('/api/admin/enrollments', [])
            ->assertForbidden();
    }

    public function test_student_from_wrong_department_cannot_be_assigned(): void
    {
        $departmentA = Department::factory()->create();
        $departmentB = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $departmentA->id,
            'year_of_study' => 2,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $departmentB->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => [$student->id],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('student_ids');
    }

    public function test_student_from_wrong_year_cannot_be_assigned(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 3,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => [$student->id],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('student_ids');
    }

    public function test_student_cannot_be_enrolled_twice_in_same_section(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $payload = [
            'course_section_id' => $section->id,
            'student_ids' => [$student->id],
        ];

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', $payload)
            ->assertCreated();

        $this->postJson('/api/admin/enrollments', $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('student_ids');
    }

    public function test_section_capacity_cannot_be_exceeded(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
            'capacity' => 1,
        ]);

        $students = User::factory()->count(2)->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => $students->pluck('id')->all(),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('student_ids');

        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_invalid_section_id_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => fake()->uuid(),
                'student_ids' => [fake()->uuid()],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('course_section_id');
    }

    
    public function test_lecturer_cannot_assign_enrollments(): void
    {
        $lecturer = User::factory()->create([
            'role' => 'lecturer',
        ]);

        $this->actingAs($lecturer, 'sanctum')
            ->postJson('/api/admin/enrollments', [])
            ->assertForbidden();
    }

    public function test_lecturer_cannot_be_enrolled_as_student(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $lecturer = User::factory()->create([
            'role' => 'lecturer',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => [$lecturer->id],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('student_ids');
    }

    public function test_admin_can_assign_multiple_students_at_once(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
            'capacity' => 35,
        ]);

        $students = User::factory()->count(3)->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => $students->pluck('id')->all(),
            ])
            ->assertCreated()
            ->assertJsonPath('assigned_count', 3);

        $this->assertDatabaseCount('enrollments', 3);
    }

    public function test_admin_can_view_enrollments(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => [$student->id],
            ])
            ->assertCreated();

        $this->getJson('/api/admin/enrollments')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('data.0.student.id', $student->id);
    }

    public function test_admin_can_delete_enrollment(): void
    {
        $department = Department::factory()->create();

        $section = CourseSection::factory()->create([
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $student = User::factory()->create([
            'role' => 'student',
            'department_id' => $department->id,
            'year_of_study' => 2,
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/admin/enrollments', [
                'course_section_id' => $section->id,
                'student_ids' => [$student->id],
            ])
            ->assertCreated();

        $enrollment = \App\Models\Enrollment::firstOrFail();

        $this->deleteJson("/api/admin/enrollments/{$enrollment->uuid}")
            ->assertOk()
            ->assertJson([
                'message' => 'Enrollment removed successfully.',
            ]);

        $this->assertDatabaseMissing('enrollments', [
            'uuid' => $enrollment->uuid,
        ]);
    }

}
