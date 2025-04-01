<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUarFilesTable extends Migration
{
    public function up()
    {
        Schema::create('uar_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uar_id')->constrained('u_a_r_s')->onDelete('cascade');


            $table->string('user_list');
            $table->string('screenshot');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('uar_files');
    }
}
