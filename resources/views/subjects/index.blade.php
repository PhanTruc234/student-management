@extends('master')

@section('title')
    Danh sách môn học
@endsection

@section('content')

<form action="{{ route('subjects.index') }}" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Tìm môn học" value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline-primary">Tìm kiếm</button>
    </div>
</form>

<a href="{{ route('subjects.create') }}" class="btn btn-success mb-3">+ Thêm môn học mới</a>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<table class="table table-bordered table-hover text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>Mã Môn Học</th>
            <th>Tên Môn Học</th>
            <th>Số tín</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($subjects as $subject)
            <tr>
                <td>{{ $subject->code }}</td>
                <td>{{ $subject->name }}</td>
                <td>
                    <span class="badge bg-info text-dark">{{ $subject->credit }}</span>
                </td>
                <td>
                    <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa môn học này?')">
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
    {{ $subjects->appends(['search' => request('search')])->links() }}
</div>

@endsection
