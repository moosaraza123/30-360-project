<?php

namespace Modules\DayCountCalculator\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneCalculationPii extends Command
{
    protected $signature = 'calculations:prune-pii {--days=90 : Age in days after which IP addresses are removed}';

    protected $description = 'Remove IP addresses from calculation records older than the retention window (data-minimisation / PDPL hygiene)';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $affected = DB::table('calculations')
            ->whereNotNull('ip_address')
            ->where('created_at', '<', now()->subDays($days))
            ->update(['ip_address' => null]);

        $this->info("Cleared IP addresses on {$affected} calculation(s) older than {$days} days.");

        return self::SUCCESS;
    }
}
