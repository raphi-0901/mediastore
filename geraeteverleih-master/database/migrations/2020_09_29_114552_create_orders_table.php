<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('comment')->nullable();
            $table->date('from');
            $table->date('to');

            $table->dateTime('picked_at')->nullable();
            $table->dateTime('returned_at')->nullable();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->tinyInteger('answer')->nullable();
            $table->unsignedBigInteger('answered_by')->nullable();
            $table->foreign('answered_by')->references('id')->on('users');

            $table->unsignedBigInteger('given_by')->nullable();
            $table->foreign('given_by')->references('id')->on('users');

            $table->unsignedBigInteger('returned_by')->nullable();
            $table->foreign('returned_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
