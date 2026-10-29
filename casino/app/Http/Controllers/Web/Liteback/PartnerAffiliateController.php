<?php
namespace VanguardLTE\Http\Controllers\Web\Liteback;
use Illuminate\Http\Request;
use VanguardLTE\AffiliateApplication;
use VanguardLTE\AffiliatePartnerProfile;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Services\PartnerAffiliateService;
class PartnerAffiliateController extends Controller {
    public function __construct(private PartnerAffiliateService $service) { $this->middleware(['auth','checker']); }
    public function index() { return view('liteback.partner-affiliates.index', ['applications'=>AffiliateApplication::with('user')->latest()->get(), 'profiles'=>AffiliatePartnerProfile::with('user')->latest()->get()]); }
    public function review(Request $request, AffiliateApplication $application) {
        $data=$request->validate(['status'=>'required|in:approved,rejected,suspended','admin_note'=>'nullable|string|max:2000','cpa_reward'=>'nullable|numeric|min:0','revshare_rate'=>'nullable|numeric|min:0|max:1']);
        $application->update(['status'=>$data['status'],'admin_note'=>$data['admin_note']??null,'reviewed_by'=>auth()->id(),'reviewed_at'=>now()]);
        $profile=AffiliatePartnerProfile::firstOrNew(['user_id'=>$application->user_id]);
        if (!$profile->exists) $profile->affiliate_code=$this->service->createCode();
        $profile->status=$data['status']==='approved' ? 'approved' : $data['status'];
        $profile->cpa_reward=$data['cpa_reward'] ?? $profile->cpa_reward;
        $profile->revshare_rate=$data['revshare_rate'] ?? $profile->revshare_rate;
        $profile->save(); return back()->with('success','Perfil de parceiro atualizado.');
    }
    public function settings(Request $request) { $data=$request->validate(['cpa_reward'=>'required|numeric|min:0','revshare_rate'=>'required|numeric|min:0|max:1']); settings()->set('partner_affiliate_cpa_reward',(float)$data['cpa_reward']); settings()->set('partner_affiliate_revshare_rate',(float)$data['revshare_rate']); settings()->save(); return back()->with('success','Contrato global atualizado.'); }
}
