<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeixinOpenidToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('weixin_openid')->unique()->nullable()->after('password');
            $table->string('weixin_unionid')->unique()->nullable()->after('weixin_openid');
            $table->string('password')->nullable()->change();
//            $table->string('email')->nullable()->change();

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
            $table->dropColumn('weixin_openid');
            $table->dropColumn('weixin_unionid');
            $table->string('password')->nullable(false)->change();
//            $table->string('email')->nullable(false)->change();

        });
    }
}
