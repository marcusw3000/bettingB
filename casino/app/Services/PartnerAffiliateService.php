<?php

namespace VanguardLTE\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use VanguardLTE\AffiliateAttribution;
use VanguardLTE\AffiliatePartnerCommission;
use VanguardLTE\AffiliatePartnerProfile;
use VanguardLTE\AffiliateCampaign;
use VanguardLTE\User;

class PartnerAffiliateService
{
    public function profileFor(User $user): ?AffiliatePartnerProfile
    {
        return AffiliatePartnerProfile::where('user_id', $user->id)->first();
    }

    public function createCode(): string
    {
        do { $code = 'PRX' . strtoupper(Str::random(8)); }
        while (AffiliatePartnerProfile::where('affiliate_code', $code)->exists());
        return $code;
    }

    public function trackClick(AffiliatePartnerProfile $profile, ?AffiliateCampaign $campaign, Request $request): array
    {
        $token = Str::random(48);
        $utm = [
            'utm_source' => (string) $request->query('utm_source', $campaign?->utm_source),
            'utm_medium' => (string) $request->query('utm_medium', $campaign?->utm_medium),
            'utm_campaign' => (string) $request->query('utm_campaign', $campaign?->utm_campaign),
            'utm_content' => (string) $request->query('utm_content', $campaign?->utm_content),
        ];
        $id = DB::table('affiliate_clicks')->insertGetId(array_merge($utm, [
            'partner_profile_id' => $profile->id, 'campaign_id' => $campaign?->id,
            'click_token' => $token, 'ip_hash' => hash('sha256', (string) $request->ip()),
            'user_agent' => Str::limit((string) $request->userAgent(), 500), 'created_at' => now(), 'updated_at' => now(),
        ]));
        return ['id' => $id, 'token' => $token, 'profile_id' => $profile->id, 'campaign_id' => $campaign?->id] + $utm;
    }

    public function attachRegistration(User $player): void
    {
        $data = session('partner_affiliate_attribution');
        if (!$data || AffiliateAttribution::where('player_id', $player->id)->exists()) return;
        $profile = AffiliatePartnerProfile::where('id', $data['profile_id'] ?? 0)->where('status', 'approved')->first();
        if (!$profile || $profile->user_id === $player->id) return;
        AffiliateAttribution::create([
            'player_id' => $player->id, 'partner_profile_id' => $profile->id,
            'campaign_id' => $data['campaign_id'] ?? null, 'click_id' => $data['id'] ?? null, 'registered_at' => now(),
        ]);
        session()->forget('partner_affiliate_attribution');
    }

    public function rates(AffiliatePartnerProfile $profile): array
    {
        return [
            'cpa' => $profile->cpa_reward ?? (float) settings('partner_affiliate_cpa_reward', 0),
            'revshare' => $profile->revshare_rate ?? (float) settings('partner_affiliate_revshare_rate', .20),
        ];
    }

    public function recordSettledSportsBet(object $bet): void
    {
        $attribution = AffiliateAttribution::where('player_id', $bet->user_id)->first();
        if (!$attribution) return;
        $profile = AffiliatePartnerProfile::where('id', $attribution->partner_profile_id)->where('status', 'approved')->first();
        if (!$profile) return;
        $stake = (float) ($bet->stake_amount ?? 0);
        if (!$attribution->first_wager_at && $stake > 0 && (int) ($bet->status ?? 0) !== 4) {
            $rates = $this->rates($profile);
            $attribution->update(['first_wager_at' => now()]);
            if ($rates['cpa'] > 0) $this->credit($profile, $attribution, 'cpa', null, $rates['cpa'], 0, 0, ['sports_bet_id' => $bet->id]);
        }
    }

    public function closeDailyRevshare(?string $date = null): int
    {
        $date = $date ?: now()->subDay()->toDateString();
        $betTable = DB::getTablePrefix() . 'sports_bets';
        $rows = DB::table('sports_bets')
            ->join('affiliate_attributions', 'affiliate_attributions.player_id', '=', 'sports_bets.user_id')
            ->join('affiliate_partner_profiles', 'affiliate_partner_profiles.id', '=', 'affiliate_attributions.partner_profile_id')
            ->where('affiliate_partner_profiles.status', 'approved')
            ->whereIn(DB::raw('"' . $betTable . '"."status"::text'), ['1', '3', 'won', 'lost'])
            ->whereDate(DB::raw('coalesce("' . $betTable . '"."result_time", "' . $betTable . '"."updated_at")'), $date)
            ->select('affiliate_attributions.player_id', 'affiliate_attributions.partner_profile_id', 'affiliate_attributions.campaign_id')
            ->selectRaw('sum("' . $betTable . '"."stake_amount") as stake, sum(case when "' . $betTable . '"."status"::text in (\'1\', \'won\') then "' . $betTable . '"."return_amount" else 0 end) as payout')
            ->groupBy('affiliate_attributions.player_id', 'affiliate_attributions.partner_profile_id', 'affiliate_attributions.campaign_id')->get();
        $created = 0;
        foreach ($rows as $row) {
            $profile = AffiliatePartnerProfile::find($row->partner_profile_id);
            $attribution = AffiliateAttribution::where('player_id', $row->player_id)->first();
            $ggr = (float) $row->stake - (float) $row->payout;
            if (!$profile || !$attribution) continue;
            $rates = $this->rates($profile);
            if (!$attribution->first_wager_at && (float) $row->stake > 0) {
                $attribution->update(['first_wager_at' => now()]);
                if ($rates['cpa'] > 0) $this->credit($profile, $attribution, 'cpa', null, $rates['cpa'], 0, 0, ['first_settlement_date' => $date]);
            }
            if ($ggr <= 0) continue;
            $before = AffiliatePartnerCommission::where(['partner_profile_id'=>$profile->id,'player_id'=>$row->player_id,'type'=>'revshare','source_date'=>$date])->exists();
            $this->credit($profile, $attribution, 'revshare', $date, round($ggr * $rates['revshare'], 4), $ggr, $rates['revshare'], ['settlement_date'=>$date]);
            if (!$before) $created++;
        }
        return $created;
    }

    private function credit(AffiliatePartnerProfile $profile, AffiliateAttribution $attribution, string $type, ?string $date, float $amount, float $ggr, float $rate, array $metadata): void
    {
        if ($amount <= 0) return;
        DB::transaction(function () use ($profile, $attribution, $type, $date, $amount, $ggr, $rate, $metadata) {
            $existing = AffiliatePartnerCommission::where(['partner_profile_id'=>$profile->id, 'player_id'=>$attribution->player_id, 'type'=>$type, 'source_date'=>$date])->exists();
            if ($existing) return;
            AffiliatePartnerCommission::create(['partner_profile_id'=>$profile->id, 'player_id'=>$attribution->player_id, 'campaign_id'=>$attribution->campaign_id, 'type'=>$type, 'source_date'=>$date, 'ggr_amount'=>$ggr, 'rate'=>$rate, 'amount'=>$amount, 'status'=>'available', 'metadata'=>$metadata]);
            AffiliatePartnerProfile::where('id', $profile->id)->increment('available_credits', $amount);
        });
    }

    public function claim(AffiliatePartnerProfile $profile): float
    {
        return DB::transaction(function () use ($profile) {
            $locked = AffiliatePartnerProfile::where('id', $profile->id)->lockForUpdate()->first();
            $amount = (float) $locked->available_credits;
            if ($amount <= 0) return 0.0;
            User::where('id', $locked->user_id)->lockForUpdate()->increment('balance', $amount);
            $locked->update(['available_credits' => 0, 'claimed_credits' => $locked->claimed_credits + $amount]);
            AffiliatePartnerCommission::where('partner_profile_id', $locked->id)->where('status', 'available')->update(['status'=>'claimed','claimed_at'=>now()]);
            return $amount;
        });
    }
}
