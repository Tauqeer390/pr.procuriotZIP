<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeaconProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beacon_products', function (Blueprint $table) {
            $table->id();
             $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('tagline')->nullable();
            $table->string('image')->nullable();
               $table->string('beacon_id')->nullable();;
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
        Schema::dropIfExists('beacon_products');
    }
}
