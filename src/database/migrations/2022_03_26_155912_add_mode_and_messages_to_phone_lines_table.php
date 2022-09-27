<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModeAndMessagesToPhoneLinesTable  extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ps_phone_lines', function (Blueprint $table) {
            $table->string('mode')->default('sequential')->after('is_active');
            $table->text('message_fr')->nullable()->after('is_active');
            $table->text('message_en')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ps_phone_lines', function (Blueprint $table) {
            $table->dropColumn('is_sequential');
            $table->dropColumn('message_fr');
            $table->dropColumn('message_en');
        });
    }
}
