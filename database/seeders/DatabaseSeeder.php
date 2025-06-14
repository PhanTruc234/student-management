<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Score;
use App\Models\Attendance;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mysql_aiven')->statement('SET FOREIGN_KEY_CHECKS=0;');
        Student::truncate();
        Subject::truncate();
        Score::truncate();
        Attendance::truncate();
        DB::connection('mysql_aiven')->statement('SET FOREIGN_KEY_CHECKS=1;');
        // Chèn dữ liệu vào bảng students
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
            'role' => 'admin',
        ]);
        for ($i = 0; $i < 20; ++$i) {
            Student::create([
                'code' => 'PMT' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'name' => fake()->name(),
                'email' => fake()->unique()->userName() . '@gmail.com',
                'gender' => fake()->randomElement(['Male', 'Female']),
                'dob' => fake()->date(),
            ]);
        }

        // Chèn dữ liệu vào bảng subjects
        $subjects = [
            ['code' => 'SUB001', 'name' => 'C', 'credit' => 3, 'total_sessions' => 30],
            ['code' => 'SUB002', 'name' => 'Java', 'credit' => 4, 'total_sessions' => 40],
            ['code' => 'SUB003', 'name' => 'Web', 'credit' => 3, 'total_sessions' => 30],
        ];
        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // Chèn dữ liệu vào bảng scores
        $students = Student::all();
        $subjects = Subject::all();
        foreach ($students->take(5) as $student) {
            foreach ($subjects as $subject) {
                $cc1 = fake()->randomFloat(1, 0, 10);
                $cc2 = fake()->randomFloat(1, 0, 10);
                $midterm = fake()->randomFloat(1, 0, 10);
                $final = fake()->randomFloat(1, 0, 10);

                // Tính score trước khi tạo
                $scoreValue = round(($cc1 * 0.1) + ($cc2 * 0.1) + ($midterm * 0.3) + ($final * 0.5), 1);

                Score::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'cc1' => $cc1,
                    'cc2' => $cc2,
                    'midterm' => $midterm,
                    'final' => $final,
                    'score' => $scoreValue,
                ]);
            }
        }

        // Chèn dữ liệu vào bảng attendances
        foreach ($students->take(5) as $student) { // Chèn điểm danh cho 5 sinh viên đầu tiên
            foreach ($subjects as $subject) {
                $session_details = [];
                for ($i = 1; $i <= $subject->total_sessions; $i++) {
                    $session_details["session_$i"] = fake()->boolean(20); // 20% xác suất vắng
                }
                $attendance = Attendance::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'session_details' => $session_details,
                ]);
                $attendance->absent_sessions = $attendance->calculateAbsentSessions();
                $attendance->save();
            }
        }
    }
}
