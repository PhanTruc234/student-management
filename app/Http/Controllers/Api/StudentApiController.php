<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentApiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', 'name');

        $students = Student::query()
            ->leftJoin('scores', 'students.id', '=', 'scores.student_id')
            ->leftJoin('subjects', 'scores.subject_id', '=', 'subjects.id')
            ->groupBy(
                'students.id',
                'students.code',
                'students.name',
                'students.email',
                'students.gender',
                'students.dob',
                'students.created_at',
                'students.updated_at'
            )
            ->select(
                'students.*',
                DB::raw('
                    ROUND(
                        COALESCE(SUM(scores.score * subjects.credit), 0) / NULLIF(SUM(subjects.credit), 0),
                        2
                    ) AS average_score
                ')
            )
            ->when($search, function ($query, $search) {
                return $query->where('students.name', 'like', "%{$search}%");
            });

        if ($sort === 'name_desc') {
            $students = $students->orderBy('students.name', 'desc');
        } elseif ($sort === 'average_score_desc') {
            $students = $students->orderBy('average_score', 'desc');
        } else {
            $students = $students->orderBy('students.name', 'asc');
        }

        return response()->json($students->paginate(10));
    }

    public function store(Request $request)
    {
        $lastStudent = Student::latest('id')->first();
        $nextId = $lastStudent ? $lastStudent->id + 1 : 1;
        $studentCode = 'PMT' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:students,email',
            'gender' => 'required|in:Male,Female',
            'dob' => 'required|date',
        ]);

        $student = Student::create([
            'code' => $studentCode,
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
        ]);

        return response()->json(['message' => 'Thêm thành công', 'student' => $student], 201);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'gender' => 'required|in:Male,Female',
            'dob' => 'required|date',
        ]);

        $student->update($request->only('name', 'email', 'gender', 'dob'));

        return response()->json(['message' => 'Cập nhật thành công', 'student' => $student]);
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return response()->json(['message' => 'Xóa thành công']);
    }
}
