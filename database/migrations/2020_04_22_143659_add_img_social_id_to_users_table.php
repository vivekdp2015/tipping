<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImgSocialIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('profile_img')->after('about')->nullable();
            $table->string('type')->after('profile_img');
            $table->string('status')->after('type')->default('1');
            $table->text('social_id')->after('type')->nullable();
            $table->enum('login_type', ['app', 'facebook', 'google', 'apple'])->after('status')->default('app');
            $table->string('password')->nullable()->change();
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
            $table->dropColumn([
                'type', 'status', 'profile_img', 'social_id', 'login_type'
            ]);
        });
    }
}
