<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;
    protected $connection = 'mysql_aiven';
    protected $fillable = ['code', 'name', 'credit', 'total_sessions'];
    public function scores()
    {
        return $this->hasMany(Score::class);
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
