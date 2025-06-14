<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;
    protected $connection = 'mysql_aiven';
    protected $fillable = [
        'student_id',
        'subject_id',
        'cc1',
        'cc2',
        'midterm',
        'final',
        'score',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function calculateTotalScore()
    {
        if ($this->cc1 === null || $this->cc2 === null || $this->midterm === null || $this->final === null) {
            return null;
        }
        return ($this->cc1 * 0.05) + ($this->cc2 * 0.05) + ($this->midterm * 0.3) + ($this->final * 0.6);
    }
    public function needsRetake()
    {
        $attendance = Attendance::where('student_id', $this->student_id)
            ->where('subject_id', $this->subject_id)
            ->first();
        return $attendance && $attendance->absent_sessions >= 3;
    }
}
