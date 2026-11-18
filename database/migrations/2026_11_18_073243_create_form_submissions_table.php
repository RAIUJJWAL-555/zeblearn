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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('form_type', 50)->index(); // 'contact', 'book_demo', 'hero_demo', 'course_enquiry', 'callback', 'brochure', 'newsletter', 'lead', 'review'
            $table->string('form_name', 150); // e.g. "Contact Us Form", "Book Free Demo Form", "Course Fee & Brochure Calculator"
            $table->string('name', 150)->nullable()->index();
            $table->string('email', 150)->nullable()->index();
            $table->string('phone', 50)->nullable()->index();
            $table->string('whatsapp_number', 50)->nullable();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('course_name', 150)->nullable()->index();
            $table->string('company_name', 150)->nullable();
            $table->text('message')->nullable();
            $table->json('additional_data')->nullable(); // JSON payload for all extra form fields (date, slot, background, mode, etc.)
            $table->string('page_url', 1000)->nullable();
            $table->string('page_title', 255)->nullable();
            $table->string('source', 100)->nullable(); // e.g. 'direct', 'referral', 'organic', 'whatsapp_cta', 'floating_bar'
            $table->string('utm_source', 100)->nullable()->index();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_term', 150)->nullable();
            $table->string('utm_content', 150)->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'converted', 'rejected'])->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('submitted_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
