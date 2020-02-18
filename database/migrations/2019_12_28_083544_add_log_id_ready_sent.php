<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogIdReadySent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ready_sent', function (Blueprint $table) {
            $table->integer('logId')->index('logId');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ready_sent', function (Blueprint $table) {
            $table->dropIndex('logId');
            $table->dropColumn('logId');
        });
    }
}
