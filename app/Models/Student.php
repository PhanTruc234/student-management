<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $connection = 'mysql_aiven';
    protected $table = 'students';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['code', 'name', 'email', 'gender', 'dob'];

    public function scores()
    {
        return $this->hasMany(Score::class, 'student_id', 'id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'id');
    }
}
