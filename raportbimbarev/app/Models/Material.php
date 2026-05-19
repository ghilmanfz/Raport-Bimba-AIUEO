<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['name', 'skill_type', 'level', 'sort_order'];

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function scopeBySkill($query, string $skillType)
    {
        return $query->where('skill_type', $skillType);
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('level', $level);
    }
}
