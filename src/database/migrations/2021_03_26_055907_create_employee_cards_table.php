<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ps_employee_cards', function (Blueprint $table) {
            $table->id();
            $table->integer('index');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('column_id');
            $table->unsignedBigInteger('member_id');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('ps_employees')->onDelete('cascade');
            $table->foreign('column_id')->references('id')->on('ps_columns')->onDelete('cascade');
            $table->foreign('member_id')->references('id')->on('ps_members')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ps_employee_cards');
    }
}
