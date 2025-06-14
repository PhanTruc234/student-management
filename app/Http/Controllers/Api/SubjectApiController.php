<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;

class SubjectApiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $subjects = Subject::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return response()->json($subjects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'credit' => 'required|integer|min:1',
        ]);

        $subjectCode = 'PKA' . str_pad(Subject::count() + 1, 3, '0', STR_PAD_LEFT);

        $subject = Subject::create([
            'code' => $subjectCode,
            'name' => $request->name,
            'credit' => $request->credit,
        ]);

        return response()->json(['message' => 'Môn học đã được thêm', 'subject' => $subject], 201);
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'credit' => 'required|integer|min:1',
        ]);

        $subject->update($request->only('name', 'credit'));

        return response()->json(['message' => 'Môn học đã được cập nhật', 'subject' => $subject]);
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Môn học đã được xóa']);
    }
}
