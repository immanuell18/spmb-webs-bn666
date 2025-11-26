<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add timestamps to provinces table
        if (Schema::hasTable('provinces')) {
            Schema::table('provinces', function (Blueprint $table) {
                if (!Schema::hasColumn('provinces', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add timestamps to regencies table
        if (Schema::hasTable('regencies')) {
            Schema::table('regencies', function (Blueprint $table) {
                if (!Schema::hasColumn('regencies', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add timestamps to districts table
        if (Schema::hasTable('districts')) {
            Schema::table('districts', function (Blueprint $table) {
                if (!Schema::hasColumn('districts', 'created_at')) {
                    $table->timestamps();
                }
            });
        }

        // Add timestamps to villages table
        if (Schema::hasTable('villages')) {
            Schema::table('villages', function (Blueprint $table) {
                if (!Schema::hasColumn('villages', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('regencies', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('villages', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};