<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUarUsersTable extends Migration
{
    public function up()
    {
        Schema::create('uar_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uar_id')->constrained('u_a_r_s')->onDelete('cascade');
            $table->json('user_data'); // Store row data as JSON
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('uar_users');
    }
}
