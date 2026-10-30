<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('withdraw_funds', function (Blueprint $table) {
            if (!Schema::hasColumn('withdraw_funds', 'workflow_status')) $table->string('workflow_status', 30)->default('requested')->index();
            if (!Schema::hasColumn('withdraw_funds', 'idempotency_key')) $table->uuid('idempotency_key')->nullable()->unique();
            if (!Schema::hasColumn('withdraw_funds', 'provider_payout_id')) $table->string('provider_payout_id', 150)->nullable()->unique();
            if (!Schema::hasColumn('withdraw_funds', 'pix_key_encrypted')) $table->text('pix_key_encrypted')->nullable();
            if (!Schema::hasColumn('withdraw_funds', 'pix_key_fingerprint')) $table->string('pix_key_fingerprint', 64)->nullable()->index();
            if (!Schema::hasColumn('withdraw_funds', 'receiver_document_encrypted')) $table->text('receiver_document_encrypted')->nullable();
            if (!Schema::hasColumn('withdraw_funds', 'first_approved_by')) $table->unsignedBigInteger('first_approved_by')->nullable()->index();
            if (!Schema::hasColumn('withdraw_funds', 'second_approved_by')) $table->unsignedBigInteger('second_approved_by')->nullable()->index();
            if (!Schema::hasColumn('withdraw_funds', 'approved_at')) $table->timestamp('approved_at')->nullable();
            if (!Schema::hasColumn('withdraw_funds', 'sent_at')) $table->timestamp('sent_at')->nullable();
            if (!Schema::hasColumn('withdraw_funds', 'paid_at')) $table->timestamp('paid_at')->nullable();
            if (!Schema::hasColumn('withdraw_funds', 'failure_reason')) $table->string('failure_reason', 500)->nullable();
        });
    }
    public function down(): void {}
};
