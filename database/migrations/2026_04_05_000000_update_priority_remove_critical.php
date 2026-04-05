<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('incidences')
            ->where('priority', 'critical')
            ->update(['priority' => 'high']);
    }

    public function down(): void
    {
        DB::table('incidences')
            ->where('priority', 'high')
            ->update(['priority' => 'critical']);
    }
};
