<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProgress extends Model
{
    protected $table = 'student_progress';

    protected $fillable = [
        'student_id', 'material_id', 'teacher_id',
        'start_date', 'understand_date', 'skilled_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'understand_date' => 'date',
            'skilled_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Auto-calculate K/B/P/T status based on dates.
     */
    public function calculateStatus(): string
    {
        if ($this->skilled_date) return 'T';
        if ($this->understand_date) return 'P';
        if ($this->start_date) return 'B';
        return 'K';
    }
}
