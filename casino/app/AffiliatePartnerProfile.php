<?php
namespace VanguardLTE;
use Illuminate\Database\Eloquent\Model;
class AffiliatePartnerProfile extends Model {
    protected $table = 'affiliate_partner_profiles';
    protected $fillable = ['user_id','affiliate_code','status','cpa_reward','revshare_rate','available_credits','claimed_credits'];
    protected $casts = ['cpa_reward'=>'float','revshare_rate'=>'float','available_credits'=>'float','claimed_credits'=>'float'];
    public function user() { return $this->belongsTo(User::class); }
    public function campaigns() { return $this->hasMany(AffiliateCampaign::class, 'partner_profile_id'); }
}
