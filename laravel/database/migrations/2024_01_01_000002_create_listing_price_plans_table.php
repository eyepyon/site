<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_price_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('プラン名');
            $table->text('description')->nullable()->comment('プラン説明');
            $table->decimal('price', 12, 2)->comment('価格');
            $table->boolean('includes_members')->default(false)->comment('会員データ含む');
            $table->boolean('includes_source')->default(false)->comment('ソースコード含む');
            $table->boolean('includes_installation')->default(false)->comment('設置サポート含む');
            $table->integer('sort_order')->default(0)->comment('表示順');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_price_plans');
    }
};
