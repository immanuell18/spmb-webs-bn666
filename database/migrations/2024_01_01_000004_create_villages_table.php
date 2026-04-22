<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('district_code', 7);
            $table->string('name');
            $table->timestamps();
            
            $table->foreign('district_code')->references('code')->on('districts');
        });
    }

    public function down()
    {
        Schema::dropIfExists('villages');
    }
};