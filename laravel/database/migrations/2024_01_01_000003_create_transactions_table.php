<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('listing_price_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->decimal('platform_fee', 12, 2);
            $table->enum('payment_method', ['stripe', 'xrpl'])->default('stripe')->comment('決済方法');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('xrpl_escrow_sequence')->nullable()->comment('XRPLエスクローシーケンス番号');
            $table->string('xrpl_transaction_hash')->nullable()->comment('XRPLトランザクションハッシュ');
            $table->decimal('xrp_amount', 20, 6)->nullable()->comment('XRP金額');
            $table->enum('status', ['pending', 'paid', 'escrowed', 'released', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
