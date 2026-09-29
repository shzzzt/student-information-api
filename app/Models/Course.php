<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code', 'title', 'description', 'credits', 'lecture_hours',
        'lab_hours', 'program_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'lecture_hours' => 'integer',
            'lab_hours' => 'integer',
            'program_id' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
