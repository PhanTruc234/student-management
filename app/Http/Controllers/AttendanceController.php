<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Subject;
use App\Models\Score;

class AttendanceController extends Controller
{
    public function allAttendances(Request $request)
    {
        $subjectName = $request->query('subject_name');
        $studentName = $request->query('student_name');
        $attendances = Attendance::with(['student', 'subject'])
            ->when($subjectName, function ($query) use ($subjectName) {
                return $query->whereHas('subject', function ($q) use ($subjectName) {
                    $q->where('name', 'like', '%' . $subjectName . '%');
                });
            })
            ->when($studentName, function ($query) use ($studentName) {
                $query->whereHas('student', function ($q) use ($studentName) {
                    $q->where('name', 'like', '%' . $studentName . '%');
                });
            })
            ->get();
        if ($request->has('over3')) {
            $attendances = $attendances->filter(function ($attendance) {
                return $attendance->absent_sessions > 3;
            });
        }
        return view('attendances.all', [
            'attendances' => $attendances,
            'search' => $subjectName,
            'student_name' => $studentName,
        ]);
    }

    public function index(Request $request, Student $student)
    {
        $filter = $request->query('filter', '');
        $attendances = Attendance::where('student_id', $student->id)
            ->with('subject')
            ->when($filter === 'fail', function ($query) {
                return $query->where('absent_sessions', '>=', 3);
            })
            ->get()
            ->sortBy(function ($attendance) {
                return $attendance->subject->name;
            });

        return view('attendances.index', compact('attendances', 'student', 'filter'));
    }

    public function create(Student $student)
    {
        $subjects = Subject::whereNotIn('id', function ($query) use ($student) {
            $query->select('subject_id')
                ->from('attendances')
                ->where('student_id', $student->id);
        })->get();
        return view('attendances.create', compact('student', 'subjects'));
    }

    public function store(Request $request, Student $student)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'sessions' => 'array',
        ]);

        $subject = Subject::findOrFail($request->subject_id);
        $totalSessions = $subject->total_sessions;

        $sessionDetails = array_fill(0, $totalSessions, false);
        if ($request->has('sessions')) {
            foreach ($request->input('sessions') as $index => $value) {
                if ($value === '1' && $index < $totalSessions) {
                    $sessionDetails[(int)$index] = true;
                }
            }
        }
        $absentSessions = count(array_filter($sessionDetails, fn($session) => $session === false));

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'subject_id' => $request->subject_id,
            'absent_sessions' => $absentSessions,
            'session_details' => json_encode($sessionDetails),
        ]);

        $score = Score::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->first();
        if ($score) {
            $score->cc1 = max(10 - ($absentSessions * (10 / $totalSessions)), 0);
            $score->score = $score->calculateTotalScore();
            $score->save();
        }
        return redirect()->route('students.attendances.index', $student->id)->with('success', 'Đã thêm điểm danh.');
    }

    public function edit(Student $student, Attendance $attendance)
    {
        if (empty($attendance->session_details)) {
            $attendance->session_details = array_fill(0, $attendance->subject->total_sessions, false);
        } else {
            $attendance->session_details = json_decode($attendance->session_details, true);
            if (!is_array($attendance->session_details)) {
                $attendance->session_details = array_fill(0, $attendance->subject->total_sessions, false);
            }
        }
        return view('attendances.edit', compact('attendance', 'student'));
    }

    public function update(Request $request, Student $student, Attendance $attendance)
    {
        $request->validate([
            'sessions' => 'array',
        ]);

        $totalSessions = $attendance->subject->total_sessions;

        $sessionDetails = array_fill(0, $totalSessions, false);
        if ($request->has('sessions')) {
            foreach ($request->input('sessions') as $index => $value) {
                if ($value === '1' && $index < $totalSessions) {
                    $sessionDetails[(int)$index] = true;
                }
            }
        }
        $absentSessions = count(array_filter($sessionDetails, fn($session) => $session === false));

        $attendance->update([
            'absent_sessions' => $absentSessions,
            'session_details' => json_encode($sessionDetails),
        ]);

        $score = Score::where('student_id', $attendance->student_id)
            ->where('subject_id', $attendance->subject_id)
            ->first();
        if ($score) {
            $score->cc1 = max(10 - ($absentSessions * (10 / $totalSessions)), 0); // Tỷ lệ vắng
            $score->score = $score->calculateTotalScore();
            $score->save();
        }

        return redirect()->route('students.attendances.index', $student->id)->with('success', 'Cập nhật thành công.');
    }

    public function destroy(Student $student, Attendance $attendance)
    {
        $studentId = $attendance->student_id;
        $subjectId = $attendance->subject_id;
        $attendance->delete();

        $score = Score::where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->first();
        if ($score) {
            $score->cc1 = 10;
            $score->score = $score->calculateTotalScore();
            $score->save();
        }

        return redirect()->route('students.attendances.index', $student->id)->with('success', 'Đã xóa điểm danh.');
    }
}
