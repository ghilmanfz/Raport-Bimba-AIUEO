<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = ['nis', 'name', 'classroom_id', 'parent_id', 'teacher_id', 'join_date', 'status', 'photo', 'report_token', 'development_notes'];

    public static function generateNextNis(): string
    {
        $lastNis = static::where('nis', 'like', 'BM%')
            ->orderByRaw("CAST(SUBSTR(nis, 3) AS UNSIGNED) DESC")
            ->value('nis');

        $nextNumber = $lastNis ? ((int) substr($lastNis, 2)) + 1 : 1;

        return 'BM' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate the next sequential NIS (BM001, BM002, ...).
     */
    public static function generateNis(): string
    {
        $lastNumber = static::whereNotNull('nis')
            ->where('nis', 'like', 'BM%')
            ->pluck('nis')
            ->map(function (string $nis): ?int {
                return preg_match('/^BM(\d+)$/', $nis, $matches)
                    ? (int) $matches[1]
                    : null;
            })
            ->filter(fn (?int $number): bool => $number !== null)
            ->max() ?? 0;

        $nextNum = $lastNumber + 1;

        return 'BM' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (empty($student->report_token)) {
                $student->report_token = Str::random(40);
            }
            if (empty($student->nis)) {
                $student->nis = static::generateNis();
            }
        });
    }

    protected $casts = [
        'join_date' => 'date',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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
