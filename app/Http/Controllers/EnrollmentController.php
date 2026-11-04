<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function store(
        StoreEnrollmentRequest $request,
        EnrollmentService $enrollmentService
    ): JsonResponse {
        $validated = $request->validated();

        $section = CourseSection::findOrFail(
            $validated['course_section_id']
        );

        $result = $enrollmentService->assignStudents(
            $section,
            $validated['student_ids']
        );

        return response()->json([
            'message' => 'Students assigned successfully.',
            'assigned_count' => $result['assigned_count'],
        ], 201);
    }

    public function index(): JsonResponse{
        $enrollments = Enrollment::with([
            'student:id,name,email',
            'courseSection',
        ])
            ->latest()
            ->paginate(15);

        return response()->json($enrollments);
    }

    public function destroy(string $uuid): JsonResponse{
        $enrollment = Enrollment::findOrFail($uuid);

        $enrollment->delete();

        return response()->json([
            'message' => 'Enrollment removed successfully.',
        ]);
    }
}
