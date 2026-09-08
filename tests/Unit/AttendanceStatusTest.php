<?php

namespace Tests\Unit;

use App\Models\Attendance;
use PHPUnit\Framework\TestCase;

class AttendanceStatusTest extends TestCase
{
    public function test_all_attendance_statuses_have_labels_and_codes(): void
    {
        foreach (Attendance::STATUSES as $status) {
            $this->assertArrayHasKey($status, Attendance::STATUS_LABELS);
            $this->assertArrayHasKey($status, Attendance::STATUS_CODES);
        }

        $this->assertSame(['H', 'S', 'I', 'A'], array_values(Attendance::STATUS_CODES));
    }
}
