<?php
namespace VanguardLTE;
use Illuminate\Database\Eloquent\Model;
class AffiliatePartnerCommission extends Model {
    protected $table = 'affiliate_partner_commissions';
    protected $fillable = ['partner_profile_id','player_id','campaign_id','type','source_date','ggr_amount','rate','amount','status','metadata','claimed_at'];
    protected $casts = ['source_date'=>'date','ggr_amount'=>'float','rate'=>'float','amount'=>'float','metadata'=>'array','claimed_at'=>'datetime'];
}
