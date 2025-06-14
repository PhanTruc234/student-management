@extends('master')

@section('title')
    Chỉnh sửa điểm cho sinh viên: {{ $student->name }}
@endsection

@section('content')
    <h3>Chỉnh sửa điểm cho sinh viên: {{ $student->name }}</h3>

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

    <form action="{{ route('students.scores.update', [$student->id, $score->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="subject_id">Môn học</label>
            <input type="text" class="form-control" value="{{ $score->subject->name }}" disabled>
        </div>

        <div class="form-group">
            <label for="cc1">Điểm CC1 (Tự động từ điểm danh)</label>
            <input type="number" step="0.1" class="form-control" id="cc1" name="cc1" value="{{ $score->cc1 ?? 'N/A' }}" readonly>
        </div>
        <div class="form-group">
            <label for="cc2">Điểm CC2 (0-10)</label>
            <input type="number" step="0.1" class="form-control" id="cc2" name="cc2" value="{{ $score->cc2 ?? old('cc2') }}" min="0" max="10" required>
            @error('cc2')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="midterm">Điểm giữa kỳ (0-10)</label>
            <input type="number" step="0.1" class="form-control" id="midterm" name="midterm" value="{{ $score->midterm ?? old('midterm') }}" min="0" max="10" required>
            @error('midterm')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="final">Điểm cuối kỳ (0-10)</label>
            <input type="number" step="0.1" class="form-control" id="final" name="final" value="{{ $score->final ?? old('final') }}" min="0" max="10" required>
            @error('final')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">Cập nhật điểm</button>
        <a href="{{ route('students.scores.index', $student->id) }}" class="btn btn-secondary mt-3">Quay lại</a>
    </form>
@endsection