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
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('rating', 3, 1)->default(4.9)->nullable()->after('learner_count');
            $table->unsignedInteger('ratings_count')->default(13952)->nullable()->after('rating');
            $table->unsignedInteger('modules_count')->default(11)->nullable()->after('duration');
            $table->unsignedInteger('discount_percent')->default(62)->nullable()->after('fee');
            $table->unsignedInteger('original_fee')->nullable()->after('discount_percent');
            $table->string('tag_badge')->default('HOT')->nullable()->after('badge_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'rating',
                'ratings_count',
                'modules_count',
                'discount_percent',
                'original_fee',
                'tag_badge',
            ]);
        });
    }
};
