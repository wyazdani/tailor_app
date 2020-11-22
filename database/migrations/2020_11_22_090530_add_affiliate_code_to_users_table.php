<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class AddAffiliateCodeToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('affiliate_code')->nullable()->after('role');
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','customer','tailor','affiliate')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('affiliate_code');
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','customer','tailor')");
        });
    }
}
