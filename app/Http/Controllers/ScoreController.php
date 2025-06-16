<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function allScores(Request $request)
    {
        $subjectName = $request->query('subject_name');
        $studentName = $request->query('student_name');

        $scores = Score::with(['student', 'subject'])->get();
        if (!empty($subjectName)) {
            $scores = $scores->filter(function ($score) use ($subjectName) {
                return stripos($score->subject->name, $subjectName) !== false;
            });
        }
        if (!empty($studentName)) {
            $scores = $scores->filter(function ($score) use ($studentName) {
                return stripos($score->student->name, $studentName) !== false;
            });
        }
        if ($request->has('fail')) {
            $scores = $scores->filter(function ($score) {
                return $score->score !== null && $score->score < 4;
            });
        }

        return view('students.scores.all', [
            'scores' => $scores,
            'search' => $subjectName,
            'student_name' => $studentName,
        ]);
    }
    public function index(Request $request, Student $student)
    {
        $scores = $student->scores()->with('subject')->get();
        return view('students.scores.index', [
            'student' => $student,
            'scores' => $scores
        ]);
    }
    public function create(Student $student)
    {
        $subjectIds = [];

        foreach ($student->scores as $score) {
            $subjectIds[] = $score->subject_id;
        }

        $subjects = Subject::whereNotIn('id', $subjectIds)->get();

        return view('students.scores.create', compact('student', 'subjects'));
    }

    public function store(Request $request, Student $student)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'cc2' => 'required|numeric|min:0|max:10',
            'midterm' => 'required|numeric|min:0|max:10',
            'final' => 'required|numeric|min:0|max:10',
        ]);

        $subject = Subject::find($request->subject_id);
        if (!$subject) {
            return back()->with('error', 'Môn học không tồn tại.');
        }
        foreach ($student->scores as $score) {
            if ($score->subject_id == $request->subject_id) {
                return back()->with('error', 'Môn học này đã có điểm.');
            }
        }
        $attendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->first();

        $cc1 = 10;

        if ($attendance) {
            $cc1 = 10 - ($attendance->absent_sessions * 3);
            if ($cc1 < 0) {
                $cc1 = 0;
            }
        }

        $total = $cc1 * 0.05 + $request->cc2 * 0.05 + $request->midterm * 0.3 + $request->final * 0.6;

        Score::create([
            'student_id' => $student->id,
            'subject_id' => $request->subject_id,
            'cc1' => $cc1,
            'cc2' => $request->cc2,
            'midterm' => $request->midterm,
            'final' => $request->final,
            'score' => $total,
        ]);

        return redirect()->route('students.scores.index', $student->id)->with('success', 'Thêm điểm thành công');
    }

    public function edit(Student $student, Score $score)
    {
        return view('students.scores.edit', compact('student', 'score'));
    }

    public function update(Request $request, Student $student, Score $score)
    {
        $request->validate([
            'cc2' => 'required|numeric|min:0|max:10',
            'midterm' => 'required|numeric|min:0|max:10',
            'final' => 'required|numeric|min:0|max:10',
        ]);

        $attendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $score->subject_id)
            ->first();

        $cc1 = 10;

        if ($attendance) {
            $cc1 = 10 - ($attendance->absent_sessions * 3);
            if ($cc1 < 0) {
                $cc1 = 0;
            }
        }

        $score->cc1 = $cc1;
        $score->cc2 = $request->cc2;
        $score->midterm = $request->midterm;
        $score->final = $request->final;
        $score->score = $score->calculateTotalScore();
        $score->save();

        return redirect()->route('students.scores.index', $student->id)->with('success', 'Cập nhật điểm thành công');
    }

    public function destroy(Student $student, Score $score)
    {
        $score->delete();

        return redirect()->route('students.scores.index', $student->id)->with('success', 'Xóa điểm thành công');
    }
}
