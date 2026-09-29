<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
	title: 'Student Information API',
	version: '1.0.0',
	description: 'REST API for the student information system.'
)]
#[OA\Server(url: 'http://localhost:8000', description: 'Local development server')]
#[OA\SecurityScheme(
	securityScheme: 'bearerAuth',
	type: 'http',
	scheme: 'bearer',
	bearerFormat: 'JWT'
)]
#[OA\Tag(name: 'Auth', description: 'JWT authentication: login, current user and token revocation.')]
#[OA\Tag(name: 'Students', description: 'Student profiles, search, filters and deactivation.')]
#[OA\Tag(name: 'Programs', description: 'Academic programs offered by the institution.')]
#[OA\Tag(name: 'Courses', description: 'Course catalog entries belonging to a program.')]
#[OA\Tag(name: 'Academic Terms', description: 'Academic terms (semesters) and their registration windows.')]
#[OA\Tag(name: 'Course Offerings', description: 'Scheduled sections of a course for an academic term.')]
#[OA\Tag(name: 'Enrollments', description: 'Student enrollments, student enrollment lists and offering rosters.')]
#[OA\Tag(name: 'Grades', description: 'Grades recorded for enrollments, guarded by instructor ownership.')]
#[OA\Tag(name: 'Academic Record', description: 'Per-student academic record grouped by academic term.')]
#[OA\Tag(name: 'Users', description: 'Directory of active user accounts used for instructor pickers.')]
class OpenApi
{
}