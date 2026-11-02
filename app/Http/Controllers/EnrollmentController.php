<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\CourseSection;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;

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
}
