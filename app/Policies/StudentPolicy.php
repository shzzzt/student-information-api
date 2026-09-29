<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewNestedRecords(User $user, Student $student): bool
    {
        if (in_array($user->role, ['admin', 'registrar'], true)) {
            return true;
        }

        if ($user->role === 'student') {
            return $student->user_id === $user->id;
        }

        if (in_array($user->role, ['faculty', 'advisor'], true)) {
            return $student->enrollments()
                ->whereHas(
                    'courseOffering',
                    fn ($query) => $query->where('instructor_id', $user->id),
                )
                ->exists();
        }

        return false;
    }
}
