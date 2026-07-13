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
        // 1. departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Update doctors table to add department_id and is_featured
        Schema::table('doctors', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->boolean('is_featured')->default(false);
        });

        // 2. services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name', 100);
            $table->string('patient_title', 100)->nullable(); // e.g. "Heart Patient"
            $table->text('comment');
            $table->tinyInteger('rating')->default(5);
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // 4. notices
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. facilities
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. awards
        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('organization', 255);
            $table->integer('year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. contact_messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('phone', 15)->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 8. working_hours
        Schema::create('working_hours', function (Blueprint $table) {
            $table->id();
            $table->string('day', 15)->unique(); // Monday, Tuesday, etc.
            $table->string('hours', 50); // e.g. "08:00 AM - 08:00 PM" or "Closed"
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        // 9. news_categories
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->timestamps();
        });

        // 10. news
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('content');
            $table->string('image_url', 255)->nullable();
            $table->foreignId('category_id')->nullable()->constrained('news_categories')->onDelete('set null');
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 11. faq_categories
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 12. faqs
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question', 255);
            $table->text('answer');
            $table->foreignId('category_id')->nullable()->constrained('faq_categories')->onDelete('set null');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('faq_categories');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('working_hours');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('services');
        
        if (Schema::hasTable('doctors')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropColumn(['department_id', 'is_featured']);
            });
        }
        
        Schema::dropIfExists('departments');
    }
};
