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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->index(); // functional, technical, cloud_specialized
            $table->string('status')->index(); // available, upcoming, planned
            $table->text('short_description');
            $table->text('description')->nullable();
            $table->json('curriculum')->nullable();
            $table->string('duration')->nullable();
            $table->string('delivery_mode')->nullable();
            $table->unsignedInteger('fee')->nullable();
            $table->boolean('featured')->default(false)->index();
            $table->unsignedInteger('learner_count')->nullable();
            $table->string('badge_label')->nullable();
            $table->json('highlights')->nullable();
            $table->json('target_audience')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
