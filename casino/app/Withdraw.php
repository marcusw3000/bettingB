<?php 
namespace VanguardLTE
{
    class Withdraw extends \Illuminate\Database\Eloquent\Model
    {
        protected $table = 'withdraw_funds';
        protected $fillable = [
            'user_id', 
            'amount', 
            'coin_amount',
            'fiat_amount',
            'currency', 
            'method',
            'wallet', 
            'status', 
            'admin_note',
            'shop_id',
            'created_at',
            'confirmed_at'
            ,'workflow_status','idempotency_key','provider_payout_id','pix_key_encrypted','pix_key_fingerprint',
            'receiver_document_encrypted','first_approved_by','second_approved_by','approved_at','sent_at','paid_at','failure_reason'
        ];
        public $timestamps = false;
        public static function boot()
        {
            parent::boot();
        }
        public function user()
        {
            return $this->hasOne('VanguardLTE\User', 'id', 'user_id');
        }
        public function shop()
        {
            return $this->hasOne('VanguardLTE\Shop', 'id', 'shop_id');
        }
    }

}
