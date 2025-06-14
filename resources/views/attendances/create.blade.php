@extends('master')

@section('title', 'Thêm điểm danh cho sinh viên: ' . $student->name)

@section('content')
    <h3>Thêm điểm danh cho sinh viên {{ $student->name }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($subjects->isEmpty())
        <div class="alert alert-warning">
            Sinh viên đã có điểm danh cho tất cả môn học. Không thể thêm điểm danh mới.
        </div>
        <a href="{{ route('students.attendances.index', $student->id) }}" class="btn btn-secondary">Quay lại</a>
    @else
        <form action="{{ route('students.attendances.store', $student->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="subject_id" class="form-label">Môn học</label>
                <select name="subject_id" id="subject_id" class="form-control" required>
                    <option value="" disabled selected>Chọn môn học</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" data-total-sessions="{{ $subject->total_sessions }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3" id="sessions-container" style="display: none;">
                <label class="form-label">Điểm danh</label>

                <!-- Checkbox Tick tất cả -->
                <div class="form-check mb-2">
                    <input type="checkbox" id="checkAll" class="form-check-input">
                    <label for="checkAll" class="form-check-label"><strong>Tick tất cả</strong></label>
                </div>

                <div class="row" id="sessions-checkboxes"></div>

                @error('sessions')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('students.attendances.index', $student->id) }}" class="btn btn-secondary">Quay lại</a>
        </form>

        <script>
            document.getElementById('subject_id').addEventListener('change', function () {
                const totalSessions = parseInt(this.options[this.selectedIndex].getAttribute('data-total-sessions')) || 0;
                const container = document.getElementById('sessions-checkboxes');
                const checkAll = document.getElementById('checkAll');

                container.innerHTML = '';
                checkAll.checked = false;

                if (totalSessions > 0) {
                    document.getElementById('sessions-container').style.display = 'block';

                    for (let i = 1; i <= totalSessions; i++) {
                        const div = document.createElement('div');
                        div.className = 'col-2 mb-2';
                        div.innerHTML = `
                            <label class="form-check-label">
                                <input type="checkbox" name="sessions[${i-1}]" value="1" class="form-check-input session-checkbox">
                                Buổi ${i}
                            </label>
                        `;
                        container.appendChild(div);
                    }

                    checkAll.onchange = () => {
                        const checked = checkAll.checked;
                        document.querySelectorAll('.session-checkbox').forEach(cb => cb.checked = checked);
                    };
                } else {
                    document.getElementById('sessions-container').style.display = 'none';
                }
            });
        </script>
    @endif
@endsection
