<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Historical rows were stored as 'Actual/365' while the rest of the app
     * (validation, convention lists, educational pages) uses 'Actual/365 Fixed'.
     * Align stored data with the canonical name.
     */
    public function up(): void
    {
        DB::table('calculations')
            ->where('convention_type', 'Actual/365')
            ->update(['convention_type' => 'Actual/365 Fixed']);
    }

    public function down(): void
    {
        DB::table('calculations')
            ->where('convention_type', 'Actual/365 Fixed')
            ->update(['convention_type' => 'Actual/365']);
    }
};
