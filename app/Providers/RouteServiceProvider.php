<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Models\Attendance;
use App\Models\Student;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot()
    {
        Route::model('student', Student::class);
        Route::bind('attendance', function ($value, $route) {
            $student = $route->parameter('student');
            $studentId = is_object($student) ? $student->id : $student;
            return Attendance::where('id', $value)->where('student_id', $studentId)->firstOrFail();
        });

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
