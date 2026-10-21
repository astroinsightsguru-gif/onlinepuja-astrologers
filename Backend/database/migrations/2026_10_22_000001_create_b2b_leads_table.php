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
        if (!Schema::hasTable('b2b_leads')) {
            Schema::create('b2b_leads', function (Blueprint $table) {
                $table->id();
                $table->string('business_name', 191);
                $table->string('contact_person', 120)->nullable();
                $table->string('phone', 40)->index();
                $table->string('email', 191)->nullable()->index();
                $table->string('category', 80)->default('travel_agency')->index(); // travel_agency, astrologer, samagri_store, corporate, hotel, other
                $table->string('city', 80)->nullable()->index();
                $table->string('state', 80)->nullable();
                $table->text('address')->nullable();
                $table->string('pincode', 20)->nullable();
                $table->decimal('google_rating', 3, 2)->nullable();
                $table->unsignedInteger('google_reviews_count')->default(0);
                $table->string('google_place_id', 191)->nullable()->index();
                $table->string('website', 255)->nullable();
                $table->string('source', 50)->default('google_maps')->index(); // google_maps, csv_import, manual, referral
                $table->string('status', 40)->default('new')->index(); // new, contacted, interested, partner_onboarded, unresponsive
                $table->string('partner_code', 40)->nullable()->unique();
                $table->decimal('commission_pct', 5, 2)->default(25.00);
                $table->unsignedInteger('total_bookings')->default(0);
                $table->decimal('total_revenue', 10, 2)->default(0.00);
                $table->timestamp('last_contacted_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['city', 'category']);
                $table->index(['status', 'city']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2b_leads');
    }
};
