<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = ['nis', 'name', 'classroom_id', 'parent_id', 'join_date', 'status', 'photo', 'report_token', 'development_notes'];

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (empty($student->report_token)) {
                $student->report_token = Str::random(40);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
        ];
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function progressBySkill(string $skillType)
    {
        return $this->progress()
            ->whereHas('material', fn ($q) => $q->where('skill_type', $skillType))
            ->with('material')
            ->get();
    }

    public function skillPercentage(string $skillType): float
    {
        $progress = $this->progress()
            ->whereHas('material', fn ($q) => $q->where('skill_type', $skillType));

        $total = $progress->count();
        if ($total === 0) return 0;

        $skilled = (clone $progress)->where('status', 'T')->count();
        return round(($skilled / $total) * 100, 1);
    }
}
