@extends('master')

@section('title', 'Thêm sinh viên')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <h3>Thêm sinh viên mới</h3>
    <form action="{{ route('students.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tên</label>
            <input type="text" name="name" class="form-control" id="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="email" required>
        </div>
        <div class="mb-3">
            <label for="gender" class="form-label">Giới tính</label>
            <select name="gender" class="form-control" id="gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="dob" class="form-label">Ngày sinh</label>
            <input type="date" name="dob" class="form-control" id="dob" required>
        </div>
        <button type="submit" class="btn btn-primary">Thêm sinh viên</button>
        <a href="{{ route('students.index') }}" class="btn btn-warning">Trở về</a>
    </form>
@endsection
