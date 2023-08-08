<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offerwalls', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->longText('conversion_id');
            $table->double('point_value');
            $table->double('usd_value');
            $table->string('offer_title');
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
        Schema::dropIfExists('offerwalls');
    }
};
