@extends('master')

@section('title', 'Quản lý điểm danh - ' . $student->name)

@section('content')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('students.attendances.create', $student->id) }}" class="btn btn-success mb-3">+ Thêm điểm danh</a>

    @if($attendances->isEmpty())
        <div class="alert alert-info">Chưa có dữ liệu điểm danh cho sinh viên này.</div>
    @else
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Môn học</th>
                    <th>Số buổi vắng</th>
                    <th>Buổi vắng</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $i => $a)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $a->subject->name }}</td>
                        <td class="{{ $a->absent_sessions > 3 ? 'text-danger' : ($a->absent_sessions > 0 ? 'text-warning' : 'text-success') }}">
                            {{ $a->absent_sessions }}
                        </td>
                        <td>
                            @php
                                $details = is_array($a->session_details) ? $a->session_details : json_decode($a->session_details, true);
                                $absents = [];
                                if (is_array($details)) {
                                    foreach ($details as $key => $val) {
                                        if (!$val) $absents[] = $key + 1;
                                    }
                                }
                            @endphp
                            {{ count($absents) ? 'Buổi: ' . implode(', ', $absents) : 'Không' }}
                        </td>
                        <td>
                            @if($a->absent_sessions > 3)
                                <span class="text-danger">Học lại</span>
                            @else
                                <span class="text-success">Đủ</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('students.attendances.edit', [$student->id, $a->id]) }}" class="btn btn-warning btn-sm mb-1">Sửa</a>
                            <form action="{{ route('students.attendances.destroy', [$student->id, $a->id]) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-1">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endsection
