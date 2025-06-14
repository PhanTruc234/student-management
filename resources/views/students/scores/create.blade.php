@extends('master')

@section('title')
    Thêm điểm cho sinh viên: {{ $student->name }}
@endsection

@section('content')
    <h3>Thêm điểm cho sinh viên: {{ $student->name }}</h3>

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

    @if($subjects->isEmpty())
        <div class="alert alert-warning">
            Sinh viên đã có điểm cho tất cả môn học. Không thể thêm điểm mới.
        </div>
        <a href="{{ route('students.scores.index', $student->id) }}" class="btn btn-secondary">Quay lại</a>
    @else
        <form action="{{ route('students.scores.store', $student->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="subject_id">Môn học</label>
                <select name="subject_id" class="form-control" required>
                    <option value="" disabled selected>Chọn môn học</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="cc2">Điểm CC2 (0-10)</label>
                <input type="number" name="cc2" id="cc2" class="form-control" min="0" max="10" step="0.1" value="{{ old('cc2') }}" required>
                @error('cc2')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="midterm">Điểm giữa kỳ (0-10)</label>
                <input type="number" name="midterm" id="midterm" class="form-control" min="0" max="10" step="0.1" value="{{ old('midterm') }}" required>
                @error('midterm')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="final">Điểm cuối kỳ (0-10)</label>
                <input type="number" name="final" id="final" class="form-control" min="0" max="10" step="0.1" value="{{ old('final') }}" required>
                @error('final')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">Thêm điểm</button>
            <a href="{{ route('students.scores.index', $student->id) }}" class="btn btn-secondary mt-3">Quay lại</a>
        </form>
    @endif
@endsection