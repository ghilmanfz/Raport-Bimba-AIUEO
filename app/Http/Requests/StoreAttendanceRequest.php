<?php

namespace App\Http\Requests;

use App\Models\Attendance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attendance_date' => ['required', 'date', 'before_or_equal:today'],
            'classroom_id' => ['nullable', 'exists:classrooms,id'],
            'search' => ['nullable', 'string', 'max:100'],
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*.student_id' => ['required', 'integer', 'distinct', 'exists:students,id'],
            'attendances.*.status' => ['nullable', Rule::in(Attendance::STATUSES)],
            'attendances.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance_date.before_or_equal' => 'Tanggal absensi tidak boleh melewati hari ini.',
            'attendances.required' => 'Daftar murid yang akan diabsen tidak ditemukan.',
            'attendances.*.status.in' => 'Status kehadiran yang dipilih tidak valid.',
        ];
    }
}
