@extends('master') {{-- Hoặc tên layout của bạn --}}

@section('title', 'Tất cả điểm sinh viên')

@section('content')

<!-- Form tìm kiếm -->
<form method="GET" action="{{ route('scores.all') }}" class="mb-4">
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
        <input type="text" name="subject_name" value="{{ $search }}" class="form-control" placeholder="Nhập tên môn học...">
        <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
    </div>
    <div class="mb-3 mt-4">
        <a href="{{ route('scores.all', array_merge(request()->all(), ['fail' => 1])) }}" class="btn btn-warning">
            Lọc điểm trượt
        </a>
        @if(request('fail'))
            <a href="{{ route('scores.all') }}" class="btn btn-secondary">Xóa lọc</a>
        @endif
    </div>
</form>

<!-- Bảng danh sách điểm -->
<table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>Sinh viên</th>
            <th>Môn học</th>
            <th>CC1</th>
            <th>CC2</th>
            <th>Giữa kỳ</th>
            <th>Cuối kỳ</th>
            <th>Điểm tổng</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($scores as $score)
            <tr>
                <td>{{ $score->student->name }}</td>
                <td>{{ $score->subject->name }}</td>
                <td>{{ $score->cc1 ?? 'null' }}</td>
                <td>{{ $score->cc2 ?? 'null' }}</td>
                <td>{{ $score->midterm ?? 'null' }}</td>
                <td>{{ $score->final ?? 'null' }}</td>
                <td>
                    @if($score->score !== null)
                        <strong class="{{ $score->score < 4 ? 'text-danger' : 'text-success' }}">
                            {{ number_format($score->score, 2) }}
                        </strong>
                    @else
                        N/A
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-muted">Không có dữ liệu phù hợp.</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
