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
        $scores = Score::with(['student', 'subject'])
            ->when($subjectName, function ($query) use ($subjectName) {
                $query->whereHas('subject', function ($q) use ($subjectName) {
                    $q->where('name', 'like', '%' . $subjectName . '%');
                });
            })
            ->when($studentName, function ($query) use ($studentName) {
                $query->whereHas('student', function ($q) use ($studentName) {
                    $q->where('name', 'like', '%' . $studentName . '%');
                });
            })
            ->get();
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
        $sort = $request->query('sort', '');
        $filter = $request->query('filter', '');
        $scores = $student->scores()->with('subject')
            ->when($sort === 'score_desc', function ($query) {
                return $query->orderBy('score', 'desc');
            })
            ->when($sort === 'score_asc', function ($query) {
                return $query->orderBy('score', 'asc');
            })
            ->when($filter === 'fail', function ($query) {
                return $query->where('score', '<', 4);
            })
            ->get();

        return view('students.scores.index', compact('student', 'scores', 'sort', 'filter'));
    }

    public function create(Student $student)
    {
        $subjectIdsWithScore = $student->scores()->pluck('subject_id');
        $subjects = Subject::whereNotIn('id', $subjectIdsWithScore)->get();
        return view('students.scores.create', compact('student', 'subjects'));
    }

    public function store(Request $request, Student $student)
    {
        $subject = Subject::find($request->subject_id);
        if (!$subject) {
            return redirect()->back()->with('error', 'Môn học không tồn tại');
        }

        $existingScore = $student->scores()->where('subject_id', $request->subject_id)->first();
        if ($existingScore) {
            return redirect()->back()->with('error', 'Môn học này đã có điểm, không thể thêm lại!');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'cc2' => 'required|numeric|min:0|max:10',
            'midterm' => 'required|numeric|min:0|max:10',
            'final' => 'required|numeric|min:0|max:10',
        ]);
        $attendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->first();
        $cc1 = $attendance ? max(10 - ($attendance->absent_sessions * 3), 0) : 10;

        $totalScore = ($cc1 * 0.05) + ($request->cc2 * 0.05) + ($request->midterm * 0.3) + ($request->final * 0.6);

        $score = $student->scores()->create([
            'subject_id' => $request->subject_id,
            'cc1' => $cc1,
            'cc2' => $request->cc2,
            'midterm' => $request->midterm,
            'final' => $request->final,
            'score' => $totalScore,
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
        $cc1 = $attendance ? max(10 - ($attendance->absent_sessions * 3), 0) : 10;

        $score->update([
            'cc1' => $cc1,
            'cc2' => $request->cc2,
            'midterm' => $request->midterm,
            'final' => $request->final,
            'score' => $score->calculateTotalScore(),
        ]);

        return redirect()->route('students.scores.index', $student->id)->with('success', 'Cập nhật điểm thành công');
    }

    public function destroy(Student $student, Score $score)
    {
        $score->delete();
        return redirect()->route('students.scores.index', $student->id)->with('success', 'Xóa điểm thành công');
    }
}
