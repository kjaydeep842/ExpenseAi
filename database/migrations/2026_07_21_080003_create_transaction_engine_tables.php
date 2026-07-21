<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('bank_account_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->uuid('merchant_id')->nullable();
            $table->string('type')->default('expense'); 
            // Types: expense, income, transfer, investment, loan, salary, refund, cashback, rewards, cash_withdrawal, neft, rtgs, imps, upi, atm, credit_card, debit_card, wallet
            $table->decimal('amount', 15, 2);
            $table->decimal('net_amount', 15, 2)->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0.00);
            $table->decimal('fee_amount', 15, 2)->default(0.00);
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('completed'); // completed, pending, flagged
            $table->dateTime('transaction_date');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('location')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->json('tags')->nullable();
            $table->text('raw_sms')->nullable();
            $table->string('attachment_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'category_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
        });

        Schema::create('transaction_imports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('file_name');
            $table->string('file_type'); // csv, excel, pdf, sms_xml
            $table->string('source')->default('bank_statement'); // bank_statement, sms_backup
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('total_count')->default(0);
            $table->integer('processed_count')->default(0);
            $table->json('error_log')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('transaction_sms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->text('raw_body');
            $table->string('sender')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('type')->nullable(); // debit, credit, etc.
            $table->string('merchant')->nullable();
            $table->string('bank')->nullable();
            $table->string('ref_no')->nullable();
            $table->string('parsed_status')->default('parsed'); // parsed, unparsed, transaction_created
            $table->uuid('transaction_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });

        Schema::create('transaction_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('transaction_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('channel')->default('in_app'); // in_app, email, push
            $table->string('status')->default('unread');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
        });

        Schema::create('receipt_scans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('image_url');
            $table->longText('extracted_text')->nullable();
            $table->json('extracted_json')->nullable();
            $table->string('merchant')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('gst', 15, 2)->nullable();
            $table->date('date')->nullable();
            $table->string('status')->default('pending'); // pending, scanned, confirmed
            $table->uuid('transaction_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_scans');
        Schema::dropIfExists('transaction_notifications');
        Schema::dropIfExists('transaction_sms');
        Schema::dropIfExists('transaction_imports');
        Schema::dropIfExists('transactions');
    }
};
