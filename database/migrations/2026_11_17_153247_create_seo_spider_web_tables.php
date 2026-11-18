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
        if (! Schema::hasTable('internal_link_overrides')) {
            Schema::create('internal_link_overrides', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 50)->index(); // 'blog', 'course', 'category'
                $table->unsignedBigInteger('source_id')->nullable()->index();
                $table->string('target_type', 50)->index(); // 'blog', 'course', 'category'
                $table->unsignedBigInteger('target_id')->index();
                $table->enum('action', ['pin', 'exclude'])->default('pin');
                $table->string('custom_anchor_text', 150)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['source_type', 'source_id', 'target_type', 'target_id'], 'link_override_unique');
            });
        }

        if (! Schema::hasTable('internal_linking_logs')) {
            Schema::create('internal_linking_logs', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 50)->index();
                $table->unsignedBigInteger('source_id')->nullable()->index();
                $table->string('action', 50)->index(); // 'auto_linked', 'recalculated', 'purged'
                $table->unsignedInteger('links_generated')->default(0);
                $table->unsignedInteger('qualifying_score')->default(0);
                $table->json('details')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->nullable();
                $table->string('phone', 50)->nullable()->index();
                $table->string('email', 150)->nullable()->index();
                $table->string('course_name', 150)->nullable();
                $table->unsignedBigInteger('course_id')->nullable()->index();
                $table->string('source_url', 500)->nullable();
                $table->string('destination_url', 500)->nullable();
                $table->string('cta_type', 50)->nullable()->index(); // 'hero_demo', 'blog_bridge', 'whatsapp', 'counselor', 'syllabus'
                $table->string('referrer', 500)->nullable();
                $table->string('utm_source', 100)->nullable();
                $table->string('utm_medium', 100)->nullable();
                $table->string('utm_campaign', 100)->nullable();
                $table->string('status', 50)->default('new')->index(); // 'new', 'contacted', 'enrolled', 'closed'
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
        Schema::dropIfExists('internal_linking_logs');
        Schema::dropIfExists('internal_link_overrides');
    }
};
