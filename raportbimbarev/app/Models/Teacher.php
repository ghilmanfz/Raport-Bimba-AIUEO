<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'nip', 'specialization', 'status'];

    /**
     * Generate the next sequential NIP (T-001, T-002, ...).
     */
    public static function generateNip(): string
    {
        $lastNumber = static::whereNotNull('nip')
            ->where('nip', 'like', 'T-%')
            ->pluck('nip')
            ->map(function (string $nip): ?int {
                return preg_match('/^T-(\d+)$/', $nip, $matches)
                    ? (int) $matches[1]
                    : null;
            })
            ->filter(fn (?int $number): bool => $number !== null)
            ->max() ?? 0;

        $nextNum = $lastNumber + 1;

        return 'T-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_teacher');
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function students()
    {
        return Student::whereIn('classroom_id', $this->classrooms()->pluck('classrooms.id'));
    }
}
