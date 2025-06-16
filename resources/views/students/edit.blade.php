@extends('master')

@section('title', 'Chỉnh sửa sinh viên')

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
    <h3>Cập nhật sinh viên</h3>
    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT') 
        
        <div class="form-group">
            <label for="name">Tên:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email', $student->email) }}" class="form-control">
        </div>
        <div class="form-group">
            <label for="gender">Giới tính:</label>
            <select name="gender" id="gender" class="form-control">
                <option value="Male" {{ $student->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ $student->gender == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="dob">Ngày sinh:</label>
            <input type="date" name="dob" id="dob" value="{{ old('dob', $student->dob) }}" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
@endsection
