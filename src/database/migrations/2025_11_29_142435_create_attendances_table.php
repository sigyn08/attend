<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('break_start')->nullable()->after('clock_out');
            $table->time('break_end')->nullable()->after('break_start');
            // break_minutes はすでにあるので追加不要
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['break_start', 'break_end']);
        });
    }
}
