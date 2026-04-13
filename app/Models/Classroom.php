<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'level', 'capacity'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'classroom_teacher');
    }

    public function activeStudentsCount(): int
    {
        return $this->students()->where('status', 'aktif')->count();
    }
}
