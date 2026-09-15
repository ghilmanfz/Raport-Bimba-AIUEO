<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use App\Services\ReportPeriodService;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendRaporReminders extends Command
{
    protected $signature = 'rapor:remind {--dry-run : Show due reminders without sending notifications}';

    protected $description = 'Pengingat rapor setiap tiga bulan sejak tanggal masuk murid';

    public function handle(ReportPeriodService $periods): int
    {
        $today = CarbonImmutable::today('Asia/Jakarta');
        $admins = User::where('role', 'admin')->pluck('id');
        $sent = 0;
        $due = 0;

        Student::active()->whereNotNull('join_date')->with(['teacher', 'classroom'])->chunkById(100, function ($students) use ($periods, $today, $admins, &$sent, &$due) {
            foreach ($students as $student) {
                $period = $periods->nextDistribution($student);
                if (! $period['due_date']->isSameDay($today)) {
                    continue;
                }

                $due++;
                if ($this->option('dry-run')) {
                    $this->line("Murid #{$student->id}: periode {$period['number']}, ".$period['due_date']->toDateString());

                    continue;
                }

                $params = ['student_id' => $student->id, 'period_number' => $period['number'], 'period_end' => $period['end']->toDateString()];
                $recipients = [];
                if ($student->parent_id) {
                    $recipients[$student->parent_id] = route('wali.rapor.periode', $params);
                }
                if ($student->teacher?->user_id) {
                    $recipients[$student->teacher->user_id] = route('guru.rapor', $params);
                }
                foreach ($admins as $adminId) {
                    $recipients[$adminId] = route('admin.murid');
                }

                $sent += DB::transaction(function () use ($student, $period, $recipients) {
                    $created = 0;
                    foreach ($recipients as $userId => $link) {
                        $notification = Notification::firstOrCreate([
                            'deduplication_key' => 'rapor:'.$student->id.':'.$period['end']->toDateString().':'.$userId,
                        ], [
                            'user_id' => $userId,
                            'type' => 'info',
                            'icon' => 'lucide:calendar-check',
                            'title' => 'Pengingat: Pembagian Rapor',
                            'message' => 'Jadwal pembagian rapor '.$student->name.' untuk periode '.$period['number'].' ('.$period['start']->translatedFormat('d M Y').' - '.$period['end']->translatedFormat('d M Y').').',
                            'link' => $link,
                        ]);
                        $created += (int) $notification->wasRecentlyCreated;
                    }

                    return $created;
                });
            }
        });

        $this->info($this->option('dry-run') ? "{$due} murid memiliki jadwal hari ini; tidak ada notifikasi dikirim." : "{$sent} notifikasi baru untuk {$due} murid dengan jadwal hari ini.");

        return self::SUCCESS;
    }
}
