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
        $lastNip = static::whereNotNull('nip')
            ->orderByRaw("CAST(SUBSTR(nip, 3) AS INTEGER) DESC")
            ->value('nip');

        $nextNum = $lastNip ? ((int) substr($lastNip, 2)) + 1 : 1;

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
