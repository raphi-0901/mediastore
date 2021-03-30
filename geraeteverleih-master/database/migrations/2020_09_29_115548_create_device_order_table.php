<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('device_id');
            $table->unsignedBigInteger('order_id');
            $table->string('note_before')->nullable();
            $table->string('note_after')->nullable();
            $table->dateTime('out_scan')->nullable();
            $table->dateTime('back_scan')->nullable();
            $table->timestamps();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unique(['device_id','order_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_order');
    }
}
