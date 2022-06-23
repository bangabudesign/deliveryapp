<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->longText('address')->nullable()->after('lng');
            $table->string('avatar')->nullable()->after('role');
            $table->string('motor_type')->nullable()->after('avatar');
            $table->string('vehicle_number')->nullable()->after('motor_type');
            $table->boolean('is_working')->default(0)->after('vehicle_number');
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
            $table->dropColumn('address');
            $table->dropColumn('avatar');
            $table->dropColumn('motor_type');
            $table->dropColumn('vehicle_number');
            $table->dropColumn('is_working');
        });
    }
}
