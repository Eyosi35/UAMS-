<?php

namespace App\Services;

use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function assignStudents(
        CourseSection $section,
        array $studentIds
    ): array {
        return DB::transaction(function () use ($section, $studentIds) {

        $section = CourseSection::query()
                ->whereKey($section->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Retrieve the selected students.
            $students = User::query()
                ->whereIn('id', $studentIds)
                ->lockForUpdate()
                ->get();

            if ($students->count() !== count(array_unique($studentIds))) {
                throw ValidationException::withMessages([
                    'student_ids' => 'One or more students could not be found.',
                ]);
            }

            // Only students from the correct department and year can be assigned.
            foreach ($students as $student) {
                if (
                    $student->role !== 'student' ||
                    $student->department_id !== $section->department_id ||
                    (int) $student->year_of_study !== (int) $section->year_of_study
                ) {
                    throw ValidationException::withMessages([
                        'student_ids' =>
                            'Every student must belong to the section department and year of study.',
                    ]);
                }
            }

            // Prevent duplicate enrollments.
            $existing = Enrollment::query()
                ->where('course_section_id', $section->id)
                ->whereIn('student_id', $studentIds)
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'student_ids' =>
                        'At least one student is already enrolled in this section.',
                ]);
            }

            // Enforce the section's capacity.
            $currentCount = Enrollment::query()
                ->where('course_section_id', $section->id)
                ->count();

            if ($currentCount + count($studentIds) > $section->capacity) {
                throw ValidationException::withMessages([
                    'student_ids' =>
                        'The section does not have enough capacity for all selected students.',
                ]);
            }

            // Insert all enrollments within the same transaction.
            $now = now();

            Enrollment::insert(
                collect($studentIds)->map(fn ($studentId) => [
                    'uuid' => (string) Str::uuid(),
                    'student_id' => $studentId,
                    'course_section_id' => $section->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );

            return [
                'assigned_count' => count($studentIds),
            ];
        });
    }
}
