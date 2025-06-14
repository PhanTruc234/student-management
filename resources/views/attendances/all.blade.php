@extends('master')

@section('title', 'Tất cả điểm danh sinh viên')

@section('content')

<!-- Form tìm kiếm môn học -->
<form method="GET" action="{{ route('attendances.all') }}" class="mb-4">
    <div class="input-group mb-4">
        <input 
            type="text" 
            name="student_name" 
            value="{{ request('student_name') }}" 
            class="form-control" 
            placeholder="Nhập tên sinh viên..." 
        >
        <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
    </div>
    <div class="input-group">
        <input type="text" name="subject_name" value="{{ $search }}" placeholder="Nhập tên môn học..." class="form-control">
        <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
    </div>
    <div class="mb-3 mt-4">
        <a href="{{ route('attendances.all', array_merge(request()->all(), ['over3' => 1])) }}" class="btn btn-warning">
        Lọc vắng
        </a>
        @if(request('over3'))
            <a href="{{ route('attendances.all') }}" class="btn btn-secondary">
            Xóa lọc
            </a>
        @endif
    </div>
</form>
<!-- Bảng điểm danh -->
<table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>Sinh viên</th>
            <th>Môn học</th>
            <th>Số buổi vắng</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($attendances as $attendance)
            <tr>
                <td>{{ $attendance->student->name }}</td>
                <td>{{ $attendance->subject->name }}</td>
                <td>
                    <strong class="{{ $attendance->absent_sessions > 3 ? 'text-danger' : ($attendance->absent_sessions > 0 ? 'text-warning' : 'text-success') }}">
                        {{ $attendance->absent_sessions }}
                    </strong>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-muted">Không có dữ liệu phù hợp.</td>
            </tr>
        @endforelse
    </tbody>
</table>


@endsection
