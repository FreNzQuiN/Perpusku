<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex(['title']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE books ADD FULLTEXT fulltext_title (title)');
        } else {
            Schema::table('books', function (Blueprint $table) {
                $table->index('title');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE books DROP INDEX fulltext_title');
        }

        Schema::table('books', function (Blueprint $table) {
            $table->index('title');
        });
    }
};
