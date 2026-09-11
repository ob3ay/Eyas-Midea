<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceRecord; // أو Model الخاص بك
use App\Models\Employee;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();

        if ($employees->isEmpty()) return;

        // إنشاء سجلات للـ 5 أيام الماضية
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            foreach ($employees as $employee) {
                $checkIn = Carbon::parse("{$date} 08:00:00");
                $checkOut = Carbon::parse("{$date} 16:00:00");

                AttendanceRecord::create([
                    'employee_id'    => $employee->id,
                    'date'           => $date,
                    'check_in_time'  => $checkIn,  // تم التعديل
                    'check_out_time' => $checkOut, // تم التعديل
                    'status'         => 'Present', // تم التعديل لتطابق enum
                    'work_minutes'   => 480,
                    'late_minutes'   => 0,
                ]);
            }
        }
    }
}
