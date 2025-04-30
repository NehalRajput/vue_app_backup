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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_name'); // Expense name
            $table->decimal('amount'); // Expense amount (up to 10 digits, 2 decimals)
            $table->date('expense_date'); // Expense date
            $table->foreignId('group_id')->constrained()->onDelete('cascade'); // Foreign key to groups table
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
