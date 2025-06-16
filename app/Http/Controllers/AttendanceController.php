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

        $attendances = Attendance::with(['student', 'subject']);

        if (!empty($subjectName)) {
            $attendances = $attendances->whereHas('subject', function ($query) use ($subjectName) {
                $query->where('name', 'like', '%' . $subjectName . '%');
            });
        }
        if (!empty($studentName)) {
            $attendances = $attendances->whereHas('student', function ($query) use ($studentName) {
                $query->where('name', 'like', '%' . $studentName . '%');
            });
        }

        $attendances = $attendances->get();
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

        $attendances = Attendance::where('student_id', $student->id)->with('subject');

        if ($filter === 'fail') {
            $attendances = $attendances->where('absent_sessions', '>=', 3);
        }

        $attendances = $attendances->get();

        return view('attendances.index', compact('attendances', 'student', 'filter'));
    }
    public function create(Student $student)
    {
        $subjects = Subject::whereNotIn('id', function ($query) use ($student) {
            $query->select('subject_id')->from('attendances')->where('student_id', $student->id);
        })->get();

        return view('attendances.create', compact('student', 'subjects'));
    }
    public function store(Request $request, Student $student)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'sessions' => 'array',
        ]);

        $subject = Subject::find($request->subject_id);
        $totalSessions = $subject->total_sessions;
        $sessionDetails = array_fill(0, $totalSessions, false);
        if ($request->has('sessions')) {
            foreach ($request->input('sessions') as $index => $value) {
                if ($value === '1' && $index < $totalSessions) {
                    $sessionDetails[(int)$index] = true;
                }
            }
        }
        $absentSessions = 0;
        foreach ($sessionDetails as $present) {
            if (!$present) {
                $absentSessions++;
            }
        }
        Attendance::create([
            'student_id' => $student->id,
            'subject_id' => $request->subject_id,
            'absent_sessions' => $absentSessions,
            'session_details' => json_encode($sessionDetails),
        ]);
        $score = Score::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if ($score) {
            $cc1 = 10 - ($absentSessions * (10 / $totalSessions));
            $score->cc1 = $cc1 < 0 ? 0 : $cc1;
            $score->score = $score->calculateTotalScore();
            $score->save();
        }

        return redirect()->route('students.attendances.index', $student->id)->with('success', 'Đã thêm điểm danh.');
    }
    public function edit(Student $student, Attendance $attendance)
    {
        $total = $attendance->subject->total_sessions;

        $details = json_decode($attendance->session_details, true);
        if (!is_array($details)) {
            $details = array_fill(0, $total, false);
        }

        $attendance->session_details = $details;

        return view('attendances.edit', compact('attendance', 'student'));
    }
    public function update(Request $request, Student $student, Attendance $attendance)
    {
        $request->validate([
            'sessions' => 'array',
        ]);

        $total = $attendance->subject->total_sessions;
        $sessionDetails = array_fill(0, $total, false);

        if ($request->has('sessions')) {
            foreach ($request->input('sessions') as $index => $value) {
                if ($value === '1' && $index < $total) {
                    $sessionDetails[(int)$index] = true;
                }
            }
        }

        $absentSessions = 0;
        foreach ($sessionDetails as $present) {
            if (!$present) {
                $absentSessions++;
            }
        }

        $attendance->update([
            'absent_sessions' => $absentSessions,
            'session_details' => json_encode($sessionDetails),
        ]);

        $score = Score::where('student_id', $attendance->student_id)
            ->where('subject_id', $attendance->subject_id)
            ->first();

        if ($score) {
            $cc1 = 10 - ($absentSessions * (10 / $total));
            $score->cc1 = $cc1 < 0 ? 0 : $cc1;
            $score->score = $score->calculateTotalScore();
            $score->save();
        }

        return redirect()->route('students.attendances.index', $student->id)->with('success', 'Cập nhật thành công.');
    }
    public function destroy(Student $student, Attendance $attendance)
    {
        $attendance->delete();

        $score = Score::where('student_id', $attendance->student_id)
            ->where('subject_id', $attendance->subject_id)
            ->first();

        if ($score) {
            $score->cc1 = 10;
            $score->score = $score->calculateTotalScore();
            $score->save();
        }

        return redirect()->route('students.attendances.index', $student->id)->with('success', 'Đã xóa điểm danh.');
    }
}
