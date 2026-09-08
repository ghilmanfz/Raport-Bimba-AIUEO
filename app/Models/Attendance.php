<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public const STATUSES = ['hadir', 'sakit', 'izin', 'alpa'];

    public const STATUS_LABELS = [
        'hadir' => 'Hadir',
        'sakit' => 'Sakit',
        'izin' => 'Izin',
        'alpa' => 'Alpa',
    ];

    public const STATUS_CODES = [
        'hadir' => 'H',
        'sakit' => 'S',
        'izin' => 'I',
        'alpa' => 'A',
    ];

    protected $fillable = [
        'student_id',
        'classroom_id',
        'recorded_by',
        'attendance_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeInMonth(Builder $query, string $month): Builder
    {
        $periodStart = Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfMonth();

        return $query->whereBetween('attendance_date', [
            $periodStart->toDateString(),
            $periodStart->copy()->endOfMonth()->toDateString(),
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusCodeAttribute(): string
    {
        return self::STATUS_CODES[$this->status] ?? '-';
    }
}
