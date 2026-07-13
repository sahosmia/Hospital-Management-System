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
        // 1. user_profiles
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 15)->nullable();
            $table->json('medical_history')->nullable();
            $table->timestamps();
        });

        // 2. doctors
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('specialization', 100);
            $table->decimal('consultation_fee', 10, 2);
            $table->decimal('surgery_fee', 10, 2)->nullable();
            $table->integer('experience_years')->nullable();
            $table->json('qualifications')->nullable();
            $table->string('chamber_location', 255)->nullable();
            $table->timestamps();
        });

        // 3. doctor_schedules
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->enum('day_of_week', ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('slot_duration')->default(15);
            $table->integer('max_patients')->default(20);
            $table->boolean('is_available')->default(true);
            $table->string('chamber_location', 255)->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'day_of_week']);
        });

        // 4. doctor_holidays
        Schema::create('doctor_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('holiday_date');
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'holiday_date']);
        });

        // 5. beds
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->string('bed_number', 20)->unique();
            $table->string('ward_name', 50);
            $table->enum('bed_type', ['General', 'Private', 'Deluxe', 'ICU', 'HDU']);
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->foreignId('current_patient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('daily_charge', 10, 2);
            $table->json('features')->nullable();
            $table->timestamp('last_cleaned_at')->nullable();
            $table->timestamps();
        });

        // 6. appointments
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->date('appointment_date');
            $table->time('appointment_time');
            $table->string('serial_number', 50)->unique();
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'no-show'])->default('scheduled');
            $table->enum('type', ['physical', 'video', 'telephone'])->default('physical');
            $table->enum('booking_channel', ['online', 'offline'])->default('online');
            $table->foreignId('booked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('symptoms')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'doctor_id', 'appointment_date', 'status']);
        });

        // 7. admissions
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('admission_number', 50)->unique();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('bed_id')->constrained('beds')->onDelete('cascade');
            $table->date('admit_date');
            $table->time('admit_time');
            $table->enum('admit_type', ['emergency', 'planned', 'referred'])->default('planned');
            $table->enum('patient_condition', ['critical', 'serious', 'stable', 'good'])->default('stable');
            $table->text('primary_diagnosis')->nullable();
            $table->enum('payment_type', ['cash', 'insurance', 'corporate', 'government', 'free'])->default('cash');
            $table->enum('status', ['active', 'discharged', 'transferred', 'on_hold'])->default('active');
            $table->date('discharge_date')->nullable();
            $table->time('discharge_time')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['patient_id', 'doctor_id', 'bed_id', 'status']);
        });

        // 8. treatment_plans
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->text('diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. medication_orders
        Schema::create('medication_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('treatment_plan_id')->nullable()->constrained('treatment_plans')->onDelete('set null');
            $table->string('medicine_name', 255);
            $table->string('generic_name', 255)->nullable();
            $table->string('medicine_category', 100)->nullable();
            $table->enum('medicine_form', ['tablet', 'capsule', 'syrup', 'injection', 'drip', 'ointment', 'inhaler'])->default('tablet');
            $table->string('dosage', 50);
            $table->string('frequency', 100);
            $table->enum('route', ['oral', 'iv', 'im', 'sc', 'topical', 'inhalation', 'rectal'])->default('oral');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->json('scheduled_times')->nullable();
            $table->enum('status', ['active', 'completed', 'cancelled', 'on_hold'])->default('active');
            $table->boolean('is_emergency')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['admission_id', 'status', 'medicine_name']);
        });

        // 10. medication_administrations
        Schema::create('medication_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_order_id')->constrained('medication_orders')->onDelete('cascade');
            $table->foreignId('admission_id')->constrained('admissions')->onDelete('cascade');
            $table->foreignId('administered_by')->constrained('users')->onDelete('cascade');
            $table->date('administered_date');
            $table->time('administered_time');
            $table->time('scheduled_time');
            $table->string('dosage_given', 50);
            $table->enum('status', ['given', 'missed', 'refused', 'held', 'delayed'])->default('given');
            $table->text('reason_for_missed')->nullable();
            $table->text('patient_response')->nullable();
            $table->text('side_effects')->nullable();
            $table->json('vitals_before')->nullable();
            $table->json('vitals_after')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['medication_order_id', 'admission_id', 'administered_date']);
        });

        // 11. operation_theaters
        Schema::create('operation_theaters', function (Blueprint $table) {
            $table->id();
            $table->string('ot_number', 20)->unique();
            $table->string('ot_name', 100);
            $table->enum('ot_type', ['general', 'cardiac', 'neuro', 'orthopedic', 'ent', 'ophthalmic', 'multi_specialty'])->default('general');
            $table->integer('capacity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_ventilator')->default(false);
            $table->boolean('has_heart_lung_machine')->default(false);
            $table->boolean('has_c_arm')->default(false);
            $table->boolean('has_microscope')->default(false);
            $table->boolean('has_laparoscopic_tower')->default(false);
            $table->boolean('has_robotic_system')->default(false);
            $table->string('floor', 10)->nullable();
            $table->string('room_number', 20)->nullable();
            $table->decimal('base_charge', 10, 2)->default(0);
            $table->decimal('per_hour_charge', 10, 2)->default(0);
            $table->enum('status', ['available', 'occupied', 'cleaning', 'maintenance', 'reserved'])->default('available');
            $table->unsignedBigInteger('current_surgery_id')->nullable(); // Circular reference solved: we constrain after surgeries table creation.
            $table->timestamps();

            $table->index(['status', 'ot_type']);
        });

        // 12. surgeries
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->string('surgery_number', 50)->unique();
            $table->foreignId('admission_id')->constrained('admissions')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('surgeon_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assistant_surgeon_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('anesthesiologist_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('scrub_nurse_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('ot_id')->constrained('operation_theaters')->onDelete('cascade');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->timestamp('actual_start_time')->nullable();
            $table->timestamp('actual_end_time')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('surgery_type', 100);
            $table->string('surgery_name', 255);
            $table->enum('urgency', ['elective', 'urgent', 'emergency', 'semi_emergency'])->default('elective');
            $table->enum('priority', ['routine', 'high', 'critical'])->default('routine');
            $table->text('patient_condition_before')->nullable();
            $table->text('patient_condition_after')->nullable();
            $table->enum('outcome', ['successful', 'partial', 'unsuccessful', 'complicated'])->nullable();
            $table->enum('anesthesia_type', ['general', 'local', 'regional', 'spinal', 'epidural', 'sedation'])->nullable();
            $table->text('pre_op_notes')->nullable();
            $table->text('post_op_notes')->nullable();
            $table->text('surgical_notes')->nullable();
            $table->text('complications')->nullable();
            $table->enum('status', ['scheduled', 'pre_op', 'in_progress', 'completed', 'cancelled', 'postponed'])->default('scheduled');
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['patient_id', 'surgeon_id', 'ot_id', 'scheduled_date', 'status']);
        });

        // Complete the circular dependency on operation_theaters
        Schema::table('operation_theaters', function (Blueprint $table) {
            $table->foreign('current_surgery_id')->references('id')->on('surgeries')->onDelete('set null');
        });

        // 13. surgical_supplies
        Schema::create('surgical_supplies', function (Blueprint $table) {
            $table->id();
            $table->string('supply_code', 50)->unique();
            $table->string('supply_name', 255);
            $table->enum('category', ['suture', 'dressing', 'glove', 'mask', 'gown', 'drape', 'catheter', 'tube', 'drain', 'implant', 'other']);
            $table->string('unit', 20);
            $table->integer('quantity_per_unit')->default(1);
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->integer('maximum_stock')->default(100);
            $table->integer('reorder_level')->default(20);
            $table->decimal('purchase_price', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('last_purchase_price', 10, 2)->nullable();
            $table->string('supplier_name', 255)->nullable();
            $table->string('supplier_contact', 50)->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->string('lot_number', 50)->nullable();
            $table->string('storage_location', 100)->nullable();
            $table->string('shelf_number', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category', 'current_stock', 'expiry_date']);
        });

        // 14. inventory_transactions
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->foreignId('supply_id')->constrained('surgical_supplies')->onDelete('cascade');
            $table->foreignId('surgery_id')->nullable()->constrained('surgeries')->onDelete('cascade');
            $table->foreignId('admission_id')->nullable()->constrained('admissions')->onDelete('cascade');
            $table->enum('transaction_type', ['purchase', 'consumption', 'return', 'waste', 'transfer', 'adjustment']);
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->text('description')->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('performed_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['supply_id', 'surgery_id', 'transaction_type']);
        });

        // 15. financial_transactions
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->onDelete('cascade');
            $table->string('transaction_number', 50)->unique();
            $table->enum('transaction_type', ['debit', 'credit']);
            $table->string('category', 50); // bed_charge, medication, procedure, consultation, test, surgery
            $table->string('sub_category', 100)->nullable();
            $table->decimal('amount', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type', 50)->nullable();
            $table->date('posting_date');
            $table->time('posting_time');
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'posted', 'approved', 'cancelled', 'reversed'])->default('posted');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['admission_id', 'category', 'posting_date']);
        });

        // 16. doctor_reviews
        Schema::create('doctor_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->tinyInteger('rating');
            $table->tinyInteger('communication_rating')->nullable();
            $table->tinyInteger('expertise_rating')->nullable();
            $table->tinyInteger('behavior_rating')->nullable();
            $table->tinyInteger('cleanliness_rating')->nullable();
            $table->tinyInteger('waiting_time_rating')->nullable();
            $table->text('review')->nullable();
            $table->text('positive_points')->nullable();
            $table->text('negative_points')->nullable();
            $table->json('tags')->nullable();
            $table->json('media')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->text('doctor_reply')->nullable();
            $table->timestamp('doctor_replied_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('pending');
            $table->text('flagged_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'doctor_id', 'appointment_id']);
            $table->index(['doctor_id', 'rating', 'status']);
        });

        // 17. doctor_rating_stats
        Schema::create('doctor_rating_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->unique()->constrained('users')->onDelete('cascade');
            $table->integer('total_reviews')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('rating_5_count')->default(0);
            $table->integer('rating_4_count')->default(0);
            $table->integer('rating_3_count')->default(0);
            $table->integer('rating_2_count')->default(0);
            $table->integer('rating_1_count')->default(0);
            $table->decimal('avg_communication', 3, 2)->default(0);
            $table->decimal('avg_expertise', 3, 2)->default(0);
            $table->decimal('avg_behavior', 3, 2)->default(0);
            $table->decimal('avg_cleanliness', 3, 2)->default(0);
            $table->decimal('avg_waiting_time', 3, 2)->default(0);
            $table->timestamp('last_updated')->useCurrent();

            $table->index('average_rating');
        });

        // 18. notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('message');
            $table->enum('type', ['appointment', 'medication', 'surgery', 'billing', 'system', 'general'])->default('general');
            $table->boolean('is_read')->default(false);
            $table->string('link', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'is_read']);
        });

        // 19. audit_logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 100);
            $table->string('resource_type', 50);
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'resource_type', 'created_at']);
        });

        // 20. system_settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('updated_at')->useCurrent();

            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('doctor_rating_stats');
        Schema::dropIfExists('doctor_reviews');
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('surgical_supplies');

        // Remove circular dependency on operation_theaters before dropping
        if (Schema::hasTable('operation_theaters')) {
            Schema::table('operation_theaters', function (Blueprint $table) {
                $table->dropForeign(['current_surgery_id']);
            });
        }

        Schema::dropIfExists('surgeries');
        Schema::dropIfExists('operation_theaters');
        Schema::dropIfExists('medication_administrations');
        Schema::dropIfExists('medication_orders');
        Schema::dropIfExists('treatment_plans');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('doctor_holidays');
        Schema::dropIfExists('doctor_schedules');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('user_profiles');
    }
};
