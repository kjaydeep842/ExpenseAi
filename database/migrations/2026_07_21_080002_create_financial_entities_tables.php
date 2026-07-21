<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('logo')->nullable();
            $table->string('country_code', 5)->default('US');
            $table->boolean('is_supported')->default(true);
            $table->timestamps();
        });

        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('bank_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name');
            $table->string('account_type')->default('savings'); // savings, current, credit, wallet, cash
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            $table->boolean('is_active')->default(true);
            $table->string('color', 20)->default('#6366f1');
            $table->string('icon', 50)->default('building-library');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('set null');
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('name');
            $table->string('type')->default('digital'); // digital, physical_cash, crypto
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            $table->string('color', 20)->default('#10b981');
            $table->string('icon', 50)->default('wallet');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('expense'); // expense, income, transfer, investment
            $table->string('icon')->default('tag');
            $table->string('color', 20)->default('#3b82f6');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('merchants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id')->nullable();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->decimal('total_expenses', 15, 2)->default(0.00);
            $table->boolean('is_verified')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::create('merchant_categories', function (Blueprint $table) {
            $table->uuid('merchant_id');
            $table->uuid('category_id');
            $table->primary(['merchant_id', 'category_id']);
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('type')->default('debit_card'); // upi, credit_card, debit_card, net_banking, cash, wallet
            $table->string('name');
            $table->json('details')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('merchant_categories');
        Schema::dropIfExists('merchants');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('banks');
    }
};
