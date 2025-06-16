@extends('master')

@section('title', 'Danh sách sinh viên')

@section('content')

    {{-- Form tìm kiếm --}}
    <form action="{{ route('students.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm sinh viên" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm</button>
            @if (request('search'))
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Xóa</a>
            @endif
        </div>
    </form>
    <a href="{{ route('students.create') }}" class="btn btn-success mb-3">+ Thêm sinh viên</a>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($students->isEmpty())
        <div class="alert alert-info">Không có sinh viên nào.</div>
    @else
        <table class="table table-bordered table-hover text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Mã SV</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Giới tính</th>
                    <th>Ngày sinh</th>
                    <th>ĐTB</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->code }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->gender }}</td>
                        <td>{{ $student->dob }}</td>
                        <td>
                            @if ($student->scores->count())
                                {{ number_format($student->scores->avg('score'), 2) }}
                            @else
                                <span class="text-muted">Chưa có điểm</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                            <a href="{{ route('students.scores.index', $student->id) }}" class="btn btn-sm btn-info">Điểm</a>
                            <a href="{{ route('students.attendances.index', $student->id) }}" class="btn btn-sm btn-warning">Điểm danh</a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-center">
            {{ $students->appends(['search' => request('search')])->links() }}
        </div>
    @endif

@endsection
