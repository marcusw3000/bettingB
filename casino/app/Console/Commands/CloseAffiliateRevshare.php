<?php
namespace VanguardLTE\Console\Commands;
use Illuminate\Console\Command;
use VanguardLTE\Services\PartnerAffiliateService;
class CloseAffiliateRevshare extends Command {
    protected $signature = 'affiliates:close-revshare {date? : YYYY-MM-DD, default yesterday}';
    protected $description = 'Close daily social sportsbook RevShare for approved affiliates';
    public function handle(PartnerAffiliateService $service): int { $count=$service->closeDailyRevshare($this->argument('date')); $this->info("Affiliate RevShare records created: {$count}"); return self::SUCCESS; }
}
