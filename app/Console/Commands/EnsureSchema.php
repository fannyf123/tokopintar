<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnsureSchema extends Command
{
    protected $signature = 'tokopintar:ensure-schema';
    protected $description = 'Ensure postgres schema (DB_SCHEMA) exists before running migrations';

    public function handle(): int
    {
        $schema = env('DB_SCHEMA', 'public');
        if ($schema === 'public' || config('database.default') !== 'pgsql') {
            $this->info("Skip: schema is '{$schema}' on driver " . config('database.default'));
            return self::SUCCESS;
        }

        $this->info("Ensuring schema '{$schema}' exists...");
        DB::statement('CREATE SCHEMA IF NOT EXISTS "' . $schema . '"');
        $this->info('Done.');
        return self::SUCCESS;
    }
}
