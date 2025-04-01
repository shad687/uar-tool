<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('u_a_r_s', function (Blueprint $table) {
            $table->id();
            $table->string('application');
            $table->foreignId('app_owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('primary_reviewer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('secondary_reviewer_id')->constrained('users')->onDelete('cascade');
            $table->enum('frequency', ['monthly', 'quarterly', 'semiannual', 'annual']);
            $table->date('start_at');
            $table->date('next_due');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('u_a_r_s');
    }
};
