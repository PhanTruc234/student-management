@extends('master')

@section('title', 'Sửa điểm danh cho sinh viên: ' . $attendance->student->name)

@section('content')
    <h3>Sửa điểm danh cho sinh viên: {{ $attendance->student->name }}</h3>

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

    <p>Môn học: <strong>{{ $attendance->subject->name }}</strong></p>

    <form action="{{ route('students.attendances.update', ['student' => $student->id, 'attendance' => $attendance->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Điểm danh</label>
            <div class="row">
                @for ($i = 1; $i <= $attendance->subject->total_sessions; $i++)
                        <div class="col-2 mb-2">
                        <label class="form-check-label">
                        <input type="checkbox" name="sessions[{{ $i - 1 }}]" value="1"
                        {{ empty($attendance->session_details[$i - 1]) ? 'checked' : '' }}>
                        Buổi {{ $i }}
                        </label>
                        {{-- !empty(...) là để kiểm tra xem giá trị tại vị trí đó có phải là true/1 không. --}}
                    </div>
                @endfor
            </div>
            @error('sessions')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        {{-- <a href="{{ route('students.att', $student->id) }}" class="btn btn-secondary">Quay lại</a> --}}
    </form>
@endsection
