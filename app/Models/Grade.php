<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id', 'letter_grade', 'numeric_grade', 'grade_points',
        'status', 'graded_by', 'graded_at', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_id' => 'integer',
            'numeric_grade' => 'decimal:2',
            'grade_points' => 'decimal:2',
            'graded_by' => 'integer',
            'graded_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
