<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->string('code', 7)->primary();
            $table->string('regency_code', 4);
            $table->string('name');
            $table->timestamps();
            
            $table->foreign('regency_code')->references('code')->on('regencies');
        });
    }

    public function down()
    {
        Schema::dropIfExists('districts');
    }
};