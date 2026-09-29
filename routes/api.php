<?php

use App\Http\Controllers\AcademicRecordController;
use App\Http\Controllers\AcademicTermController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserDirectoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->group(function (): void {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::prefix('v1')->middleware('auth:api')->group(function (): void {
    Route::middleware('role:admin,registrar,student')->group(function (): void {
        Route::apiResource('students', StudentController::class);
    });

    Route::middleware('role:admin,registrar')->group(function (): void {
        Route::get('users', [UserDirectoryController::class, 'index']);
        Route::apiResource('programs', ProgramController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('academic-terms', AcademicTermController::class);
        Route::post('course-offerings', [CourseOfferingController::class, 'store']);
        Route::match(['put', 'patch'], 'course-offerings/{course_offering}', [CourseOfferingController::class, 'update']);
        Route::delete('course-offerings/{course_offering}', [CourseOfferingController::class, 'destroy']);
        Route::post('enrollments', [EnrollmentController::class, 'store']);
        Route::match(['put', 'patch'], 'enrollments/{enrollment}', [EnrollmentController::class, 'update']);
        Route::delete('enrollments/{enrollment}', [EnrollmentController::class, 'destroy']);
    });

    Route::middleware('role:admin,registrar,faculty,instructor,advisor,student')->group(function (): void {
        Route::get('course-offerings', [CourseOfferingController::class, 'index']);
        Route::get('course-offerings/{course_offering}', [CourseOfferingController::class, 'show']);
        Route::get('enrollments', [EnrollmentController::class, 'index']);
        Route::get('enrollments/{enrollment}', [EnrollmentController::class, 'show']);
        Route::get('students/{student}/enrollments', [EnrollmentController::class, 'studentEnrollments']);
        Route::get('students/{student}/grades', [GradeController::class, 'studentGrades']);
        Route::get('students/{student}/academic-record', [AcademicRecordController::class, 'show']);
        Route::get('course-offerings/{course_offering}/students', [EnrollmentController::class, 'offeringStudents']);
        Route::get('grades', [GradeController::class, 'index']);
        Route::get('grades/{grade}', [GradeController::class, 'show']);
    });

    Route::middleware('role:admin,faculty,instructor')->group(function (): void {
        Route::post('grades', [GradeController::class, 'store']);
        Route::match(['put', 'patch'], 'grades/{grade}', [GradeController::class, 'update']);
    });
});
