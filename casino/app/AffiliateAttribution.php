<?php
namespace VanguardLTE;
use Illuminate\Database\Eloquent\Model;
class AffiliateAttribution extends Model {
    protected $table = 'affiliate_attributions';
    protected $fillable = ['player_id','partner_profile_id','campaign_id','click_id','registered_at','first_wager_at'];
    protected $casts = ['registered_at'=>'datetime','first_wager_at'=>'datetime'];
    public function player() { return $this->belongsTo(User::class, 'player_id'); }
    public function profile() { return $this->belongsTo(AffiliatePartnerProfile::class, 'partner_profile_id'); }
    public function campaign() { return $this->belongsTo(AffiliateCampaign::class, 'campaign_id'); }
}
