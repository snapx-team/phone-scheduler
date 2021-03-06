<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ps_rows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phone_line_id');
            $table->string('name');
            $table->timestamps();
            $table->foreign('phone_line_id')->references('id')->on('ps_phone_lines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ps_rows');
    }
}
