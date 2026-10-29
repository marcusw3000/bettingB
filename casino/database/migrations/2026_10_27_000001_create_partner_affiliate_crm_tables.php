<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->string('contact_name', 120);
            $table->string('email', 190);
            $table->string('primary_channel', 80);
            $table->string('channel_url', 500)->nullable();
            $table->string('status', 20)->default('submitted')->index();
            $table->text('admin_note')->nullable();
            $table->unsignedInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_partner_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->string('affiliate_code', 32)->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->decimal('cpa_reward', 18, 4)->nullable();
            $table->decimal('revshare_rate', 8, 6)->nullable();
            $table->decimal('available_credits', 18, 4)->default(0);
            $table->decimal('claimed_credits', 18, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('affiliate_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_profile_id')->index();
            $table->string('name', 120);
            $table->string('slug', 80);
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->timestamps();
            $table->unique(['partner_profile_id', 'slug']);
        });

        Schema::create('affiliate_clicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_profile_id')->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->string('click_token', 64)->unique();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->string('utm_content', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_attributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('player_id')->unique();
            $table->unsignedBigInteger('partner_profile_id')->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->unsignedBigInteger('click_id')->nullable()->index();
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('first_wager_at')->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_cost_imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_profile_id')->index();
            $table->string('original_name', 255);
            $table->unsignedInteger('accepted_rows')->default(0);
            $table->unsignedInteger('rejected_rows')->default(0);
            $table->timestamps();
        });

        Schema::create('affiliate_campaign_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_profile_id')->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->unsignedBigInteger('import_id')->nullable()->index();
            $table->date('cost_date')->index();
            $table->decimal('amount', 18, 4);
            $table->string('source', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('affiliate_partner_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_profile_id')->index();
            $table->unsignedInteger('player_id')->index();
            $table->unsignedBigInteger('campaign_id')->nullable()->index();
            $table->string('type', 20)->index(); // cpa or revshare
            $table->date('source_date')->nullable()->index();
            $table->decimal('ggr_amount', 18, 4)->default(0);
            $table->decimal('rate', 10, 6)->default(0);
            $table->decimal('amount', 18, 4);
            $table->string('status', 20)->default('available')->index();
            $table->json('metadata')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();
            $table->unique(['partner_profile_id', 'player_id', 'type', 'source_date'], 'affiliate_partner_commission_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_partner_commissions');
        Schema::dropIfExists('affiliate_campaign_costs');
        Schema::dropIfExists('affiliate_cost_imports');
        Schema::dropIfExists('affiliate_attributions');
        Schema::dropIfExists('affiliate_clicks');
        Schema::dropIfExists('affiliate_campaigns');
        Schema::dropIfExists('affiliate_partner_profiles');
        Schema::dropIfExists('affiliate_applications');
    }
};
