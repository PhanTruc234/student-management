<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $connection = 'mysql_aiven';
    protected $fillable = [
        'student_id',
        'subject_id',
        'absent_sessions',
        'session_details',
    ];

    protected $casts = [
        'session_details' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function calculateAbsentSessions()
    {
        if (empty($this->session_details)) {
            return 0;
        }
        return count(array_filter($this->session_details, fn($session) => $session === true));
    }
}
