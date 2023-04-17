<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'title', 'key', 'type', 'value'
            ]);
            $table->string('slug')->after('id');
            $table->longText('data')->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->string('key')->after('title');
            $table->string('value')->after('key');
            $table->enum('type', ['general', 'social_media'])->after('value');
            $table->dropColumn([
                'slug', 'data'
            ]);
        });
    }
}
