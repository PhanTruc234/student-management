@extends('master')

@section('title', 'Danh sách điểm của ' . $student->name)

@section('content')

<a href="{{ route('students.scores.create', $student->id) }}" class="btn btn-success mb-3">+ Thêm điểm</a>
<table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>Môn học</th>
            <th>CC1</th>
            <th>CC2</th>
            <th>Giữa kỳ</th>
            <th>Cuối kỳ</th>
            <th>Tổng</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($scores as $score)
            <tr>
                <td>{{ $score->subject->name }}</td>
                <td>{{ $score->cc1 ?? 'null' }}</td>
                <td>{{ $score->cc2 ?? 'null' }}</td>
                <td>{{ $score->midterm ?? 'null' }}</td>
                <td>{{ $score->final ?? 'null' }}</td>
                <td>
                    {{ $score->score !== null ? number_format($score->score, 2) : 'null' }}
                </td>
                <td>
                    @if($score->needsRetake())
                        <span class="badge bg-danger">Học lại</span>
                    @elseif($score->score < 4)
                        <span class="badge bg-warning text-dark">Thất bại</span>
                    @else
                        <span class="badge bg-success">Đạt</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('students.scores.edit', ['student' => $student->id, 'score' => $score->id]) }}" class="btn btn-sm btn-warning mb-1">Sửa</a>
                    <form action="{{ route('students.scores.destroy', ['student' => $student->id, 'score' => $score->id]) }}" method="POST" class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger mb-1" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
