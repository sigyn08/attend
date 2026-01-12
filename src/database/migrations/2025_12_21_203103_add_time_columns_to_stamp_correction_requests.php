<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeColumnsToStampCorrectionRequests extends Migration
{
    public function up()
    {
        Schema::table('stamp_correction_requests', function (Blueprint $table) {});
    }

    public function down() {}
}
