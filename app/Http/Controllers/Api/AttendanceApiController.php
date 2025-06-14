<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceApiController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('student')->paginate(10);
        return response()->json($attendances);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string|max:255',
            'absent' => 'required|integer|min:0',
        ]);

        $attendance = Attendance::create($request->only('student_id', 'subject', 'absent'));
        return response()->json(['message' => 'Điểm danh đã được thêm', 'attendance' => $attendance], 201);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'absent' => 'required|integer|min:0',
        ]);

        $attendance->update(['absent' => $request->absent]);
        return response()->json(['message' => 'Điểm danh đã được cập nhật', 'attendance' => $attendance]);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return response()->json(['message' => 'Điểm danh đã được xóa']);
    }
}
