<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentProfileResource;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    public function show(Request $request)
    {
        $student = $request->user();

        if (!$student->isStudent()) {
            return response()->json([
                'message' => 'Only students can access this profile.',
            ], 403);
        }

        $student->load([
            'department',
            'courseSections.courseOffering.course',
            'courseSections.courseOffering.semester',
            'courseSections.lecturers',
        ]);

        return new StudentProfileResource($student);
    }
}