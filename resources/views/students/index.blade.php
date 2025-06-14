@extends('master')

@section('title')
    Danh sách sinh viên
@endsection

@section('content')
    <!-- Form tìm kiếm -->
    <form action="{{ route('students.index') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Tìm sinh viên" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            @if (request('search'))
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Xóa tìm kiếm</a>
            @endif
        </div>
    </form>

    <a href="{{ route('students.create') }}" class="btn btn-success mb-3">+ Thêm sinh viên mới</a>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($students->isEmpty())
        <div class="alert alert-info">Không tìm thấy sinh viên nào.</div>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->code }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ ucfirst($student->gender) }}</td>
                        <td>{{ $student->dob }}</td>
                        <td>
                            @if ($student->scores->count() > 0)
                                {{ number_format($student->scores->avg('score'), 2) }}
                            @else
                                <span class="text-muted">Chưa có điểm</span>
                            @endif
                        </td>
                        <td style="min-width: 200px;">
                            <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary btn-sm mb-1">Sửa</a>
                            <a href="{{ route('students.scores.index', ['student' => $student->id]) }}" class="btn btn-info btn-sm mb-1">Điểm</a>
                            <a href="{{ route('students.attendances.index', ['student' => $student->id]) }}" class="btn btn-warning btn-sm mb-1">Điểm danh</a>
                            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm mb-1">Xóa</button>
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
