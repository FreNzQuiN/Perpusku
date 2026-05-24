<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->unique(['borrowing_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::table('borrowing_details', function (Blueprint $table) {
            $table->dropUnique(['borrowing_id', 'book_id']);
        });
    }
};
