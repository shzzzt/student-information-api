<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'course_offering_id', 'enrolled_on', 'dropped_on',
        'status', 'grade_mode',
    ];

    protected function casts(): array
    {
        return [
            'student_id' => 'integer',
            'course_offering_id' => 'integer',
            'enrolled_on' => 'date',
            'dropped_on' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function grade(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Grade::class);
    }
}
