<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('guest_carts', function (Blueprint $table) {
            $table->string('device_id')->nullable()->index();
        });
    }

    public function down()
    {
        Schema::table('guest_carts', function (Blueprint $table) {
            $table->dropColumn('device_id');
        });
    }
};
