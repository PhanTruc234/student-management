<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateScoresTable extends Migration
{
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->decimal('cc1', 4, 1)->nullable()->after('subject_id');
            $table->decimal('cc2', 4, 1)->nullable()->after('cc1');
            $table->decimal('midterm', 4, 1)->nullable()->after('cc2');
            $table->decimal('final', 4, 1)->nullable()->after('midterm');
        });
    }

    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn(['cc1', 'cc2', 'midterm', 'final']);
        });
    }
}
