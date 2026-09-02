<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('administrators')) {
            return;
        }
        Schema::create('administrators', function (Blueprint $table) {
            $table->increments('Administrator_ID');
            $table->string('Email', 64)->unique();
            $table->string('Name', 32);
            $table->string('Role', 12);
            $table->string('Phone', 16);
            $table->string('Password');
            $table->string('Image', 255);
        });
    }

    public function down(): void
    {
    }
};
