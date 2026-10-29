<?php
namespace VanguardLTE;
use Illuminate\Database\Eloquent\Model;
class AffiliateApplication extends Model {
    protected $table = 'affiliate_applications';
    protected $fillable = ['user_id','contact_name','email','primary_channel','channel_url','status','admin_note','reviewed_by','reviewed_at'];
    protected $casts = ['reviewed_at'=>'datetime'];
    public function user() { return $this->belongsTo(User::class); }
}
