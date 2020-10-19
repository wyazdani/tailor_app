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
            $table->id();
            $table->string('order_no')->nullable();
            $table->bigInteger('user_id')->default(0);
            $table->bigInteger('tailor_id')->default(0);
            $table->bigInteger('size_id')->default(0);
            $table->string('image_url')->nullable();
            $table->text('comments')->nullable();
            $table->enum('order_status',['pending','processing','started','completed']);
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
