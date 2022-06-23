<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->longText('name')->nullable();
            $table->string('image')->nullable();
            $table->decimal('lat', 10,8)->nullable();
            $table->decimal('lng', 11,8)->nullable();
            $table->longText('address')->nullable();
            $table->timeTz('opening_hours', $precision = 0)->nullable();
            $table->timeTz('closing_hours', $precision = 0)->nullable();
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
        Schema::dropIfExists('restaurants');
    }
}
