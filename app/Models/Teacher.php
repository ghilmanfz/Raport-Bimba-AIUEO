<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id', 'nip', 'specialization', 'status'];

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
