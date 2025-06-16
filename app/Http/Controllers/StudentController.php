<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        $students = Student::query()
            ->leftJoin('scores', 'students.id', '=', 'scores.student_id')
            ->leftJoin('subjects', 'scores.subject_id', '=', 'subjects.id')
            ->select(
                'students.*',
                DB::raw('
                    ROUND(
                        COALESCE(SUM(scores.score * subjects.credit), 0) / NULLIF(SUM(subjects.credit), 0),
                        2
                    ) AS average_score
                ')
            )
            ->groupBy('students.id')
            ->when($search, function ($query) use ($search) {
                return $query->where('students.name', 'like', "%$search%");
            })
            ->paginate(10);

        return view('students.index', compact('students', 'search'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $last = Student::latest('id')->first();
        $newId = $last ? $last->id + 1 : 1;
        $code = 'PMT' . str_pad($newId, 3, '0', STR_PAD_LEFT);

        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|unique:students,email',
            'gender' => 'required',
            'dob' => 'required|date',
        ]);

        Student::create([
            'code' => $code,
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
        ]);

        return redirect()->route('students.index')->with('success', 'Thêm thành công');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|max:50',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'gender' => 'required',
            'dob' => 'required|date',
        ]);

        $student->update($request->only('name', 'email', 'gender', 'dob'));

        return redirect()->route('students.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return back()->with('success', 'Xóa thành công');
    }
}
