<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['website', 'app', 'saas'])->default('website');
            $table->decimal('price', 12, 2);
            $table->string('url')->nullable();
            $table->json('tech_stack')->nullable();
            
            // 共通指標
            $table->integer('monthly_revenue')->nullable()->comment('月間売上');
            $table->integer('monthly_profit')->nullable()->comment('月間利益');
            
            // Web/SaaS向け指標
            $table->integer('monthly_pv')->nullable()->comment('月間PV');
            $table->integer('monthly_uu')->nullable()->comment('月間UU');
            
            // アプリ/SaaS向け指標
            $table->integer('total_users')->nullable()->comment('登録ユーザー数');
            $table->integer('dau')->nullable()->comment('DAU（デイリーアクティブユーザー）');
            $table->integer('mau')->nullable()->comment('MAU（マンスリーアクティブユーザー）');
            $table->integer('total_downloads')->nullable()->comment('累計ダウンロード数');
            
            $table->enum('status', ['draft', 'active', 'sold', 'suspended'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
