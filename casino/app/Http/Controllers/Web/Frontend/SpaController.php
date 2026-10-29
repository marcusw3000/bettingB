<?php

namespace VanguardLTE\Http\Controllers\Web\Frontend;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use VanguardLTE\Http\Controllers\Controller;
use VanguardLTE\Services\AffiliateService;
use VanguardLTE\Services\VipService;
use VanguardLTE\SportsBet;
use VanguardLTE\SportsMatch;

/**
 * Read-only presentation API for the Vue frontend.
 *
 * Financial mutations remain in their existing controllers/services. This keeps
 * the migration incremental and makes every SPA action use the same session,
 * CSRF policy and transaction paths as the Blade application.
 */
class SpaController extends Controller
{
    public function index(Request $request)
    {
        $manifestPath = base_path('../minimal/spa/manifest.json');
        abort_unless(is_file($manifestPath), 503, 'O frontend está sendo preparado. Execute npm run spa:build.');

        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        $entry = $manifest['resources/spa/main.js']['file'] ?? null;
        abort_unless($entry, 503, 'Manifesto do frontend inválido.');

        $requestedPath = '/' . trim($request->path(), '/');
        $spaInitialPath = match (true) {
            $requestedPath === '/' => '/app',
            $requestedPath === '/app' || str_starts_with($requestedPath, '/app/') => $requestedPath . ($request->getQueryString() ? '?' . $request->getQueryString() : ''),
            $requestedPath === '/sports' => '/app/esportes',
            $requestedPath === '/profile' => '/app/perfil',
            $requestedPath === '/vip' => '/app/vip',
            $requestedPath === '/affiliates' => '/app/afiliados',
            $requestedPath === '/help' || $requestedPath === '/faq' => '/app/ajuda',
            $requestedPath === '/bonuses' || $requestedPath === '/bonus-conditions' => '/app/beneficios',
            $requestedPath === '/progress' => '/app/progresso',
            $requestedPath === '/search' => '/app/jogos' . ($request->getQueryString() ? '?' . $request->getQueryString() : ''),
            str_starts_with($requestedPath, '/categories/') => '/app/jogos?category=' . rawurlencode(explode('/', trim($requestedPath, '/'))[1] ?? 'all'),
            default => '/app',
        };

        return view('frontend.Minimal.spa.app', [
            'spaEntry' => '/minimal/spa/' . $entry,
            'spaCss' => collect($manifest['resources/spa/main.js']['css'] ?? [])
                ->map(fn ($file) => '/minimal/spa/' . $file)->all(),
            'spaInitialPath' => $spaInitialPath,
            'spaMeta' => match (parse_url($spaInitialPath, PHP_URL_PATH)) {
                '/app/jogos' => ['title' => 'Jogos sociais | Promex', 'description' => 'Explore jogos sociais e use seus créditos Promex.'],
                '/app/esportes' => ['title' => 'Esportes sociais | Promex', 'description' => 'Acompanhe partidas e faça palpites com créditos sociais.'],
                '/app/vip' => ['title' => 'Clube VIP | Promex', 'description' => 'Acompanhe seu nível, XP e benefícios sociais.'],
                '/app/afiliados' => ['title' => 'Indique e ganhe | Promex', 'description' => 'Convide amigos e acompanhe seus créditos sociais.'],
                default => ['title' => 'Promex | Jogos sociais', 'description' => 'Jogos, esportes e recompensas em créditos sociais.'],
            },
        ]);
    }

    public function bootstrap(): JsonResponse
    {
        return response()->json([
            'app' => [
                'name' => settings('app_name', 'Promex'),
                'coin_label' => settings('coin_display_name', 'Créditos Promex'),
            ],
            'user' => $this->userPayload(),
            'navigation' => [
                ['label' => 'Início', 'to' => '/app'],
                ['label' => 'Jogos', 'to' => '/app/jogos'],
                ['label' => 'Esportes', 'to' => '/app/esportes'],
                ['label' => 'VIP', 'to' => '/app/vip'],
                ['label' => 'Indique e ganhe', 'to' => '/app/afiliados'],
            ],
        ])->header('Cache-Control', 'private, no-store');
    }

    public function sports(Request $request): JsonResponse
    {
        $sport = (string) $request->query('sport', 'all');
        $query = SportsMatch::query()->where('status', 'upcoming')->where('start_time', '>', now());
        if ($sport !== 'all') {
            $query->where('sport_key', 'like', "%{$sport}%");
        }

        $matches = $query->orderBy('start_time')->limit(100)->get()->map(fn (SportsMatch $match) => [
            'id' => $match->match_id,
            'sport' => $match->sport_title ?: 'Esportes',
            'home' => $match->home_team,
            'away' => $match->away_team,
            'starts_at' => optional($match->start_time)->toIso8601String(),
            'odds' => [
                'home' => (float) $match->odds_home,
                'draw' => (float) ($match->odds_draw ?: 3.2),
                'away' => (float) $match->odds_away,
            ],
        ]);

        return response()->json(['matches' => $matches, 'sport' => $sport])
            ->header('Cache-Control', 'private, max-age=15');
    }

    public function profile(): JsonResponse
    {
        abort_unless(Auth::check(), 401);
        $user = Auth::user();

        return response()->json([
            'user' => $this->userPayload(),
            'recent_bets' => SportsBet::where('user_id', $user->id)->latest('id')->limit(8)->get()
                ->map(fn (SportsBet $bet) => [
                    'id' => $bet->id,
                    'type' => $bet->type,
                    'stake' => (float) $bet->stake,
                    'potential_win' => (float) $bet->potential_win,
                    'status' => $bet->status,
                    'created_at' => optional($bet->created_at)->toIso8601String(),
                ]),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function vip(): JsonResponse
    {
        $user = Auth::user();
        $tiers = VipService::getTiers();
        $profile = $user ? app(VipService::class)->getVipProfile($user) : [
            'vip_level' => 'Bronze', 'vip_xp' => 0, 'unclaimed_rakeback' => 0,
            'current_tier' => $tiers['Bronze'], 'next_tier' => $tiers['Silver'],
            'progress_pct' => 0, 'xp_to_next' => $tiers['Silver']['threshold'],
            'claimed_bonuses' => [], 'recent_claims' => [],
        ];

        return response()->json(['profile' => $profile, 'tiers' => array_values($tiers), 'user' => $this->userPayload()])
            ->header('Cache-Control', 'private, no-store');
    }

    public function affiliates(): JsonResponse
    {
        abort_unless(Auth::check(), 401);
        $stats = app(AffiliateService::class)->getReferralStats(Auth::user());

        return response()->json(['stats' => $stats, 'user' => $this->userPayload()])
            ->header('Cache-Control', 'private, no-store');
    }

    public function claimAffiliate(Request $request): JsonResponse
    {
        abort_unless(Auth::check(), 401);
        $result = AffiliateService::claimCommissions(Auth::user());
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function claimVip(Request $request): JsonResponse
    {
        abort_unless(Auth::check(), 401);
        $result = VipService::claimRakeback(Auth::user());
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        abort_unless(Auth::check(), 401);
        $user = Auth::user();
        $data = $request->validate([
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);
        $user->fill($data);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Dados do perfil atualizados.', 'user' => $this->userPayload()]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        abort_unless(Auth::check(), 401);
        $data = $request->validate([
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:old_password'],
        ]);
        $user = Auth::user();
        if (!Hash::check($data['old_password'], $user->password)) {
            return response()->json(['message' => 'A senha atual não confere.'], 422);
        }
        $user->password = Hash::make($data['password']);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Senha atualizada com segurança.']);
    }

    private function userPayload(): ?array
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return [
            'id' => (int) $user->id,
            'username' => (string) $user->username,
            'first_name' => (string) ($user->first_name ?? ''),
            'last_name' => (string) ($user->last_name ?? ''),
            'email' => (string) ($user->email ?? ''),
            'email_verified_at' => (string) $user->status === \VanguardLTE\Support\Enum\UserStatus::ACTIVE,
            'phone' => (string) ($user->phone ?? ''),
            'balance' => (float) $user->balance,
            'currency' => (string) ($user->currency ?: 'CR'),
            'vip_level' => (string) ($user->vip_level ?: 'Bronze'),
            'avatar' => $user->avatar ?: null,
        ];
    }
}
