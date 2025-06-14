@extends('master')

@section('title', 'Quản lý điểm danh của ' . $student->name)

@section('content')
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <a href="{{ route('students.attendances.create', $student->id) }}" class="btn btn-success mb-3">+ Thêm điểm danh</a>

    @if($attendances->isEmpty())
        <div class="alert alert-info">Chưa có dữ liệu điểm danh cho sinh viên này.</div>
    @else
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Môn học</th>
                    <th>Số buổi vắng</th>
                    <th>Chi tiết buổi vắng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $index => $attendance)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $attendance->subject->name }}</td>
                        <td>
                            <strong class="{{ $attendance->absent_sessions > 3 ? 'text-danger' : ($attendance->absent_sessions > 0 ? 'text-warning' : 'text-success') }}">
                                {{ $attendance->absent_sessions }}
                            </strong>
                        </td>
                        <td>
                            @php
                                $sessionDetails = $attendance->session_details;
                                if (!is_array($sessionDetails)) {
                                    $sessionDetails = json_decode($sessionDetails, true);
                                }
                                if (!is_array($sessionDetails)) {
                                    $sessionDetails = array_fill(0, $attendance->subject->total_sessions, false);
                                }
                                $absentSessions = array_keys(array_filter($sessionDetails, fn($session) => $session === false));
                                $absentSessionNumbers = array_map(fn($index) => $index + 1, $absentSessions);
                                $displayText = count($absentSessionNumbers) > 0 ? 'Buổi: ' . implode(', ', $absentSessionNumbers) : 'Không';
                            @endphp
                            {{ $displayText }}
                        </td>
                        <td>
                            @if($attendance->absent_sessions > 3)
                                <span class="text-danger">Học lại</span>
                            @else
                                <span class="text-success">Đủ</span>
                            @endif
                        </td>
                        <td style="min-width: 180px;">
                            <a href="{{ route('students.attendances.edit', ['student' => $student->id, 'attendance' => $attendance->id]) }}" class="btn btn-sm btn-warning mb-1">Sửa</a>
                            <form action="{{ route('students.attendances.destroy', ['student' => $student->id, 'attendance' => $attendance->id]) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger mb-1">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
