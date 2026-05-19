<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendRaporReminders extends Command
{
    protected $signature = 'rapor:remind';
    protected $description = 'Send automatic rapor reminder notifications every 3 months based on student join date';

    public function handle()
    {
        $today = Carbon::today();
        
        // Get all active students
        $students = Student::where('status', 'aktif')
            ->whereNotNull('join_date')
            ->with(['parent', 'classroom'])
            ->get();

        $sentCount = 0;

        foreach ($students as $student) {
            $joinDate = Carbon::parse($student->join_date);
            
            // Calculate months difference
            $monthsDiff = $joinDate->diffInMonths($today);
            
            // Check if it's exactly a multiple of 3 months (3, 6, 9, 12, etc.)
            // and it's the join date anniversary (same day of month)
            if ($monthsDiff > 0 && $monthsDiff % 3 === 0 && $joinDate->day === $today->day) {
                
                // Send notification to parent
                if ($student->parent_id) {
                    Notification::send(
                        $student->parent_id,
                        'Pengingat: Pembagian Rapor',
                        'Sudah saatnya untuk pembagian rapor ' . $student->name . ' (periode ' . $monthsDiff . ' bulan). Silakan hubungi guru untuk jadwal pembagian rapor.',
                        'info',
                        'lucide:calendar-check',
                        route('wali.dashboard')
                    );
                }

                // Send notification to teacher (if assigned)
                if ($student->classroom && $student->classroom->teachers) {
                    foreach ($student->classroom->teachers as $teacher) {
                        if ($teacher->user_id) {
                            Notification::send(
                                $teacher->user_id,
                                'Reminder: Pembagian Rapor',
                                'Jadwal pembagian rapor untuk murid ' . $student->name . ' (' . $student->classroom->name . ') - Periode ' . $monthsDiff . ' bulan sejak bergabung.',
                                'info',
                                'lucide:calendar-check',
                                route('guru.rapor', ['student_id' => $student->id])
                            );
                        }
                    }
                }

                // Notify admins
                Notification::notifyAdmins(
                    'Jadwal Pembagian Rapor',
                    'Pengingat pembagian rapor untuk murid ' . $student->name . ' (' . $student->classroom?->name . ') - Periode ' . $monthsDiff . ' bulan.',
                    'info',
                    'lucide:calendar-check',
                    route('admin.murid')
                );

                $sentCount++;
                $this->info("✓ Reminder sent for: {$student->name} ({$monthsDiff} months)");
            }
        }

        if ($sentCount === 0) {
            $this->info('No rapor reminders to send today.');
        } else {
            $this->info("\nTotal reminders sent: {$sentCount}");
        }

        return Command::SUCCESS;
    }
}
