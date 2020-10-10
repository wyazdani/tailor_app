<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddDateToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_date')->after('comments')->nullable();
            DB::statement("ALTER TABLE `orders` CHANGE `order_status` `order_status` ENUM('pending','processing','started','completed');");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
            DB::statement("ALTER TABLE `orders` CHANGE `order_status` `order_status` ENUM('pending','processing','completed');");
        });
    }
}
