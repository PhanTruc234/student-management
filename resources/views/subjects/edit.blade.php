@extends('master')

@section('title')
    Sửa môn học: {{ $subject->name }}
@endsection

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

    <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên Môn Học</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $subject->name) }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="credit" class="form-label">Số tín chỉ</label>
            <input type="number" class="form-control" id="credit" name="credit" value="{{ old('credit', $subject->credit) }}" required min="1">
            @error('credit')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="total_sessions" class="form-label">Số buổi học</label>
            <input type="number" class="form-control" id="total_sessions" name="total_sessions" value="{{ old('total_sessions', $subject->total_sessions) }}" required min="1" max="50">
            @error('total_sessions')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật môn học</button>
        <a href="{{ route('subjects.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
@endsection