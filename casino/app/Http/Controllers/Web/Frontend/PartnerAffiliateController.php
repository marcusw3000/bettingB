<?php

namespace VanguardLTE\Http\Controllers\Web\Frontend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use VanguardLTE\AffiliateApplication;
use VanguardLTE\AffiliateCampaign;
use VanguardLTE\AffiliatePartnerCommission;
use VanguardLTE\AffiliatePartnerProfile;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Services\PartnerAffiliateService;

class PartnerAffiliateController extends Controller
{
    public function __construct(private PartnerAffiliateService $service) {}
    public function landing() { return view('frontend.Minimal.affiliate.landing'); }
    public function applyForm(Request $request) { if (!auth()->check()) return redirect()->route('frontend.auth.login')->with('success', 'Entre na sua conta para solicitar parceria.'); return view('frontend.Minimal.affiliate.apply', ['application'=>AffiliateApplication::where('user_id', auth()->id())->first()]); }
    public function apply(Request $request) {
        $data = $request->validate(['contact_name'=>'required|string|max:120','primary_channel'=>'required|string|max:80','channel_url'=>'nullable|url|max:500']);
        $data['user_id']=auth()->id(); $data['email']=auth()->user()->email; $data['status']='submitted';
        AffiliateApplication::updateOrCreate(['user_id'=>auth()->id()], $data);
        return redirect()->route('partner-affiliate.apply')->with('success', 'Candidatura enviada para análise.');
    }
    public function redirect(Request $request, string $code) {
        $profile = AffiliatePartnerProfile::where('affiliate_code',$code)->where('status','approved')->firstOrFail();
        $campaign = $request->query('campaign') ? AffiliateCampaign::where('partner_profile_id',$profile->id)->where('slug',$request->query('campaign'))->where('status','active')->first() : null;
        session(['partner_affiliate_attribution'=>$this->service->trackClick($profile,$campaign,$request)]);
        return redirect('/?ref=' . optional($profile->user)->invite_code);
    }
    public function dashboard() {
        $profile=$this->approvedProfile();
        $campaigns=$profile->campaigns()->latest()->get();
        $clicks=DB::table('affiliate_clicks')->where('partner_profile_id',$profile->id)->count();
        $attributions=DB::table('affiliate_attributions')->where('partner_profile_id',$profile->id);
        $registrations=(clone $attributions)->count(); $firstWagers=(clone $attributions)->whereNotNull('first_wager_at')->count();
        $commissions=AffiliatePartnerCommission::where('partner_profile_id',$profile->id);
        $earned=(clone $commissions)->sum('amount'); $ggr=(clone $commissions)->sum('ggr_amount');
        $costs=DB::table('affiliate_campaign_costs')->where('partner_profile_id',$profile->id)->sum('amount');
        return view('frontend.Minimal.affiliate.dashboard', compact('profile','campaigns','clicks','registrations','firstWagers','earned','ggr','costs'));
    }
    public function campaign(Request $request) {
        $profile=$this->approvedProfile(); $data=$request->validate(['name'=>'required|string|max:120','utm_source'=>'nullable|string|max:100','utm_medium'=>'nullable|string|max:100','utm_campaign'=>'nullable|string|max:100','utm_content'=>'nullable|string|max:100']);
        $data['partner_profile_id']=$profile->id; $data['slug']=Str::slug($data['name']) ?: Str::random(8); $data['status']='active';
        $base=$data['slug']; $n=1; while(AffiliateCampaign::where('partner_profile_id',$profile->id)->where('slug',$data['slug'])->exists()) $data['slug']=$base.'-'.++$n;
        AffiliateCampaign::create($data); return back()->with('success','Campanha criada.');
    }
    public function importCosts(Request $request) {
        $profile=$this->approvedProfile(); $request->validate(['csv'=>'required|file|mimes:csv,txt|max:2048']);
        $importId=DB::table('affiliate_cost_imports')->insertGetId(['partner_profile_id'=>$profile->id,'original_name'=>$request->file('csv')->getClientOriginalName(),'created_at'=>now(),'updated_at'=>now()]); $accepted=0;$rejected=0;
        foreach (array_map('str_getcsv', file($request->file('csv')->getRealPath())) as $i=>$row) { if ($i===0) continue; [$date,$campaign,$amount]=array_pad($row,3,null); $c=AffiliateCampaign::where('partner_profile_id',$profile->id)->where('name',$campaign)->first(); if (!$date || !is_numeric($amount)) {$rejected++; continue;} DB::table('affiliate_campaign_costs')->insert(['partner_profile_id'=>$profile->id,'campaign_id'=>$c?->id,'import_id'=>$importId,'cost_date'=>$date,'amount'=>$amount,'source'=>'csv','created_at'=>now(),'updated_at'=>now()]);$accepted++; }
        DB::table('affiliate_cost_imports')->where('id',$importId)->update(['accepted_rows'=>$accepted,'rejected_rows'=>$rejected]); return back()->with('success',"CSV importado: {$accepted} linhas aceitas; {$rejected} rejeitadas.");
    }
    public function claim() { $amount=$this->service->claim($this->approvedProfile()); return back()->with($amount ? 'success' : 'error', $amount ? 'Créditos adicionados ao saldo.' : 'Não há créditos disponíveis.'); }
    private function approvedProfile(): AffiliatePartnerProfile { $p=$this->service->profileFor(auth()->user()); abort_unless($p && $p->status==='approved',403,'Seu perfil de afiliado ainda não está aprovado.'); return $p; }
}
