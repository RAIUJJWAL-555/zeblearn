<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon_path')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('categories', function (Blueprint $table) {
                if (! Schema::hasColumn('categories', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
            });
        }

        if (Schema::hasTable('courses') && ! Schema::hasColumn('courses', 'category_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'category_id')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'description')) {
                    $table->dropColumn('description');
                }
            });
        }
    }
};
