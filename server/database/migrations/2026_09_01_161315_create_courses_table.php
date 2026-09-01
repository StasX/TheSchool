<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('Course_ID');
            $table->string('Name', 255);
            $table->text('Description')->nullable();
            $table->string('Image', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
