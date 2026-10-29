<?php
namespace VanguardLTE;
use Illuminate\Database\Eloquent\Model;
class AffiliateCampaign extends Model {
    protected $table = 'affiliate_campaigns';
    protected $fillable = ['partner_profile_id','name','slug','utm_source','utm_medium','utm_campaign','utm_content','status'];
    public function profile() { return $this->belongsTo(AffiliatePartnerProfile::class, 'partner_profile_id'); }
}
