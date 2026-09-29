<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseOfferingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'academic_term_id' => $this->academic_term_id,
            'instructor_id' => $this->instructor_id,
            'section' => $this->section,
            'room' => $this->room,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time?->format('H:i'),
            'end_time' => $this->end_time?->format('H:i'),
            'delivery_mode' => $this->delivery_mode,
            'capacity' => $this->capacity,
            'enrolled_count' => $this->enrolled_count,
            'status' => $this->status,
            'course' => $this->whenLoaded('course'),
            'academic_term' => $this->whenLoaded('academicTerm'),
            'instructor' => $this->whenLoaded('instructor'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
