<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->default(0);
            $table->string('name')->nullable();
            $table->enum('gender',['male','female','other'])->nullable();
            $table->double('shoulder_to_seam')->default(0);
            $table->double('shoulder_to_hips')->default(0);
            $table->double('shoulder_to_floor')->default(0);
            $table->double('arm_length')->default(0);
            $table->double('bicep')->default(0);
            $table->double('wrist')->default(0);
            $table->double('waist')->default(0);
            $table->double('lower_waist')->default(0);
            $table->double('waist_to_floor')->default(0);
            $table->double('hips')->default(0);
            $table->double('max_thigh')->default(0);
            $table->double('calf')->default(0);
            $table->double('ankle')->default(0);
            $table->double('chest')->default(0);
            $table->double('navel_to_floor')->default(0);
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
        Schema::dropIfExists('sizes');
    }
}
