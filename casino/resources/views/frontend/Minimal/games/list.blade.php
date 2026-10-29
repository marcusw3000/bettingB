@extends('frontend.Minimal.layouts.clean')

@section('page-title', 'Promex - Plataforma de jogos sociais')

@section('content')

@php
    $cedarBrand = settings('cedar_display_name', 'CEDAR');
    $coinLabel = settings('coin_display_name', 'Créditos Promex');
    $navCedarGames = settings('nav_label_cedar_games', 'Jogos CEDAR');
    $navSports = settings('nav_label_sportsbook', 'Esportes');
    $navPredictions = settings('nav_label_predictions', 'Previsões');
@endphp

<!-- Hero Feature Banner Carousel -->
<div class="relative overflow-hidden rounded-2xl md:rounded-3xl bg-gradient-to-r from-[#121826] via-[#162032] to-[#111624] border border-white/[0.08] shadow-2xl p-6 sm:p-8 md:p-10">
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2.5 max-w-xl">
            <div class="inline-flex items-center gap-2 bg-primary/10 border border-primary/25 px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-[11px] font-bold text-primary font-mono-jet uppercase tracking-wider">PLATAFORMA OFICIAL DE JOGOS SOCIAIS</span>
            </div>
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Jogue mais de 1.000 slots e <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">originais {{ $cedarBrand }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-muted leading-relaxed">
                Aproveite slots de arcade, o empolgante {{ $cedarBrand }} Space Crash, palpites esportivos e jackpots com múltiplos sorteios usando {{ $coinLabel }} gratuitos.
            </p>
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <a href="/categories/cedar_games" class="bg-gradient-to-r from-accent-gold to-accent-amber hover:from-yellow-400 hover:to-accent-gold text-black font-extrabold px-5 py-2.5 rounded-xl text-xs uppercase tracking-wider shadow-lg shadow-accent-gold/25 transition-all no-underline flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">rocket_launch</span>
                    <span>Jogar {{ $cedarBrand }} Crash</span>
                </a>
                <button type="button" class="open-modal bg-white/[0.06] hover:bg-white/[0.12] text-white border border-white/10 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all" data-target="{{ Auth::check() ? 'modal-profile' : 'modal-login' }}">
                    {{ Auth::check() ? 'Resgatar recarga grátis' : 'Entrar / Criar conta' }}
                </button>
            </div>
        </div>

        <!-- Featured Crash Multiplier Display -->
        <div class="hidden sm:flex flex-col items-center justify-center p-6 rounded-2xl bg-[#0b0e14]/60 border border-white/[0.08] backdrop-blur-md text-center min-w-[200px]">
            <span class="text-[10px] font-bold text-on-surface-subtle uppercase tracking-widest mb-1">MULTIPLICADOR {{ $cedarBrand }} CRASH</span>
            <span class="font-mono-jet text-4xl font-extrabold text-accent-gold tracking-tighter animate-pulse">48.72x</span>
            <span class="text-[10px] text-primary font-bold mt-1">ÚLTIMA MELHOR RODADA</span>
            <a href="/game/CedarCrash" class="mt-3 text-xs font-bold text-white bg-primary hover:bg-primary-dark px-4 py-1.5 rounded-lg no-underline transition-colors uppercase">
                Abrir
            </a>
        </div>
    </div>
    
    <!-- Ambient Background Lighting -->
    <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-10 -top-10 w-72 h-72 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>
</div>

<!-- Live Social Winners Marquee -->
<div class="flex items-center gap-3 overflow-hidden bg-[#121622] border border-white/[0.06] rounded-xl px-4 py-2.5 text-xs">
    <div class="flex items-center gap-1.5 text-accent-gold font-bold flex-shrink-0 uppercase tracking-wider text-[11px]">
        <span class="material-symbols-outlined text-base">military_tech</span>
        <span>Vitórias ao vivo:</span>
    </div>
    <div class="flex items-center gap-6 overflow-x-auto no-scrollbar font-mono-jet text-on-surface-muted whitespace-nowrap text-[11px]">
        <div class="flex items-center gap-1.5"><span class="text-white font-bold">Ahmad_K</span> ganhou <span class="text-primary font-bold">142.000 C</span> em <span class="text-white">Book of Ra</span></div>
        <span class="text-white/20">•</span>
        <div class="flex items-center gap-1.5"><span class="text-white font-bold">Maya_L</span> atingiu <span class="text-accent-gold font-bold">38,4x</span> em <span class="text-white">Cedar Space Crash</span></div>
        <span class="text-white/20">•</span>
        <div class="flex items-center gap-1.5"><span class="text-white font-bold">Charbel99</span> ganhou <span class="text-primary font-bold">88.500 C</span> em <span class="text-white">Beetle Mania</span></div>
        <span class="text-white/20">•</span>
        <div class="flex items-center gap-1.5"><span class="text-white font-bold">Georges_B</span> acertou <span class="text-secondary font-bold">3 números</span> no <span class="text-white">Jackpot Lotto</span></div>
    </div>
</div>

<!-- Games Grid Section -->
<section class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl" style="font-variation-settings: 'FILL' 1;">grid_view</span>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold tracking-tight text-white uppercase">Arena de jogos</h2>
            </div>
            <p class="text-on-surface-muted text-xs sm:text-sm mt-1">Escolha um jogo para iniciar o modo de demonstração com 10.000 créditos gratuitos.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="font-mono-jet text-xs text-primary font-bold bg-primary/10 border border-primary/20 px-3 py-1 rounded-lg">
                {{ $totalGames }} JOGOS DISPONÍVEIS
            </span>
        </div>
    </div>

    <!-- Category Filter Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1.5 custom-scrollbar max-w-full">
        <a href="{{ route('frontend.game.list.category', 'all') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap uppercase tracking-wider no-underline {{ ($category1 ?? 'all') == 'all' ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-surface-card text-on-surface-muted hover:text-white border border-white/[0.06] hover:border-white/15' }}">
            Todos os jogos
        </a>
        <a href="/categories/cedar_games" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap uppercase tracking-wider no-underline {{ request()->is('categories/cedar_games*') ? 'bg-accent-gold text-black shadow-md shadow-accent-gold/25' : 'bg-surface-card text-accent-gold hover:text-yellow-300 border border-accent-gold/20' }}">
            🚀 {{ $navCedarGames }}
        </a>
        <a href="/categories/cedar_remakes" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap uppercase tracking-wider no-underline {{ request()->is('categories/cedar_remakes*') ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-surface-card text-primary hover:text-primary-light border border-primary/20' }}">
            🌲 CEDAR Slots
        </a>
        @if(is_iterable($categories))
            @foreach($categories as $cat)
                @continue(in_array($cat->href, ['cedar_games', 'cedar_cards', 'cedar_remakes'], true))
                <a href="{{ route('frontend.game.list.category', $cat->href) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap uppercase tracking-wider no-underline {{ ($category1 ?? '') == $cat->href ? 'bg-primary text-white shadow-md shadow-primary/25' : 'bg-surface-card text-on-surface-muted hover:text-white border border-white/[0.06] hover:border-white/15' }}">
                    {{ $cat->title }}
                </a>
            @endforeach
        @endif
    </div>

    <!-- Games Grid: 2-col mobile, 3-col tablet, 4-col laptop, 5-col wide -->
    <div id="games-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
        @forelse($games as $game)
            @php($isCedarProvider = str_starts_with($game->name, 'Cedar') || $game->name === 'RoyalSteps')
            <div class="group relative aspect-[3/4] rounded-2xl overflow-hidden bg-[#161c2b] border border-white/[0.07] hover:border-primary/50 shadow-md hover:shadow-xl hover:shadow-primary/10 transition-all duration-300 flex flex-col justify-end">
                <!-- Cover Image -->
                <img src="{{ in_array($game->name, \VanguardLTE\Services\CedarArcadeService::GAMES, true) ? rtrim(config('licensing.cedar_public_origin'), '/') . '/cedar/games/' . $game->name . '/cover.png?v=1' : '/frontend/Default/ico/' . $game->name . '.jpg' }}"
                     onerror="this.src='/frontend/Default/ico/DayofDead.jpg'"
                     alt="{{ $game->title }}" 
                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                     width="300"
                     height="400"
                     decoding="async"
                     fetchpriority="{{ $loop->index < 5 ? 'high' : 'low' }}"
                     loading="{{ $loop->index < 5 ? 'eager' : 'lazy' }}">
                
                <!-- Dark Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#0b0e14] via-[#0b0e14]/40 to-transparent opacity-90 group-hover:opacity-95 transition-opacity"></div>
                
                <!-- Game Info & Action -->
                <div class="relative z-10 p-3 sm:p-4 space-y-1.5">
                    <span class="text-[9px] text-primary font-mono-jet font-bold uppercase tracking-wider block">
                        {{ $isCedarProvider ? strtoupper($cedarBrand) . ' ORIGINAL' : strtoupper(substr($game->name, -2) === 'AM' ? 'AMATIC' : (substr($game->name, -3) === 'PGD' ? 'PGD' : 'SLOT')) }}
                    </span>
                    <h4 class="text-xs sm:text-sm font-bold text-white leading-tight truncate" title="{{ $game->title }}">
                        {{ $game->title }}
                    </h4>
                    
                    <a href="{{ route('frontend.game.go', $game->name) }}" class="block text-center bg-primary hover:bg-primary-dark text-white py-1.5 sm:py-2 rounded-xl font-bold text-[11px] sm:text-xs uppercase tracking-wider transition-all no-underline shadow-md shadow-primary/20">
                        Jogar
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-[#121622] rounded-3xl border border-white/[0.06]">
                <span class="material-symbols-outlined text-4xl text-on-surface-subtle mb-2">sentiment_dissatisfied</span>
                <p class="text-on-surface-muted text-sm font-medium">Nenhum jogo foi encontrado nesta categoria.</p>
                <a href="{{ route('frontend.game.list') }}" class="mt-3 inline-block bg-primary text-white text-xs font-bold px-4 py-2 rounded-xl no-underline uppercase">
                    Ver todos os jogos
                </a>
            </div>
        @endforelse
    </div>

    @if($totalGames > $games->count())
        <div class="flex justify-center pt-2">
            <button id="load-more-games"
                    type="button"
                    data-endpoint="{{ route('frontend.game.catalog') }}"
                    data-category="{{ $category1 }}"
                    data-page="2"
                    data-per-page="{{ $initialGameLimit }}"
                    class="rounded-xl border border-primary/35 bg-primary/10 px-5 py-3 text-xs font-extrabold uppercase tracking-wider text-primary transition-colors hover:bg-primary hover:text-white">
                Ver mais jogos
            </button>
        </div>
    @endif
</section>

<!-- Sports & Prediction Highlights Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 pt-4">
    <!-- Battle Odds Card -->
    <div class="lg:col-span-2 bg-[#121622] rounded-3xl p-6 border border-white/[0.08] shadow-lg space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">sports_soccer</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white tracking-tight">{{ $navSports }} Arena</h3>
                    <span class="text-[11px] text-on-surface-muted">Partidas globais ao vivo</span>
                </div>
            </div>
            <a href="{{ route('frontend.sports.index') }}" class="text-xs font-bold text-secondary hover:text-secondary-light no-underline uppercase flex items-center gap-1">
                <span>Ver palpites</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="bg-[#161c2b] rounded-2xl p-4 border border-white/[0.06] flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4 text-center sm:text-left flex-1">
                <div class="flex-1">
                    <span class="text-[10px] text-on-surface-subtle uppercase tracking-wider block">CHAMPIONS LEAGUE</span>
                    <span class="text-sm font-bold text-white">REAL MADRID</span>
                </div>
                <span class="text-xs font-bold text-secondary bg-secondary/10 px-2.5 py-1 rounded-lg">VS</span>
                <div class="flex-1">
                    <span class="text-[10px] text-on-surface-subtle uppercase tracking-wider block">CHAMPIONS LEAGUE</span>
                    <span class="text-sm font-bold text-white">MANCHESTER CITY</span>
                </div>
            </div>
            <a href="{{ route('frontend.sports.index') }}" class="w-full sm:w-auto bg-secondary hover:bg-secondary-dark text-white px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-colors no-underline text-center">
                Palpitar grátis
            </a>
        </div>
    </div>

    <!-- Future Vote Card -->
    <div class="bg-[#121622] rounded-3xl p-6 border border-white/[0.08] shadow-lg space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-xl" style="font-variation-settings: 'FILL' 1;">query_stats</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white tracking-tight">{{ $navPredictions }}</h3>
                    <span class="text-[11px] text-on-surface-muted">Mercados de previsões</span>
                </div>
            </div>
            <a href="{{ route('frontend.predictions.index') }}" class="text-xs font-bold text-primary hover:text-primary-light no-underline uppercase flex items-center gap-1">
                <span>Votar</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="space-y-3">
            <h4 class="text-xs sm:text-sm font-bold text-white leading-snug">Robôs humanoides comerciais chegarão às casas dos consumidores antes de 2027?</h4>
            <div class="space-y-1.5">
                <div class="flex justify-between font-mono-jet text-xs font-bold">
                    <span class="text-primary">SIM 42%</span>
                    <span class="text-on-surface-muted">NÃO 58%</span>
                </div>
                <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden flex">
                    <div class="h-full bg-primary" style="width: 42%"></div>
                    <div class="h-full bg-white/10" style="width: 58%"></div>
                </div>
            </div>
            <a href="{{ route('frontend.predictions.index') }}" class="block text-center w-full bg-primary hover:bg-primary-dark text-white py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all no-underline shadow-md shadow-primary/20">
                Votar na previsão gratuitamente
            </a>
        </div>
    </div>
</div>

<script>
    (function () {
        function escapeHtml(value) {
            const node = document.createElement('div');
            node.textContent = value || '';
            return node.innerHTML;
        }

        function cardMarkup(game) {
            return `<div class="group relative aspect-[3/4] rounded-2xl overflow-hidden bg-[#161c2b] border border-white/[0.07] hover:border-primary/50 shadow-md hover:shadow-xl hover:shadow-primary/10 transition-all duration-300 flex flex-col justify-end">
                <img src="${escapeHtml(game.cover_url)}" onerror="this.src='/frontend/Default/ico/DayofDead.jpg'" alt="${escapeHtml(game.title)}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" width="300" height="400" decoding="async" fetchpriority="low" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0b0e14] via-[#0b0e14]/40 to-transparent opacity-90 group-hover:opacity-95 transition-opacity"></div>
                <div class="relative z-10 p-3 sm:p-4 space-y-1.5">
                    <span class="text-[9px] text-primary font-mono-jet font-bold uppercase tracking-wider block">${escapeHtml(game.provider)}</span>
                    <h4 class="text-xs sm:text-sm font-bold text-white leading-tight truncate" title="${escapeHtml(game.title)}">${escapeHtml(game.title)}</h4>
                    <a href="${escapeHtml(game.url)}" class="block text-center bg-primary hover:bg-primary-dark text-white py-1.5 sm:py-2 rounded-xl font-bold text-[11px] sm:text-xs uppercase tracking-wider transition-all no-underline shadow-md shadow-primary/20">Jogar</a>
                </div>
            </div>`;
        }

        function initProgressiveCatalog() {
            const button = document.getElementById('load-more-games');
            const grid = document.getElementById('games-grid');
            if (!button || !grid || button.dataset.bound === 'true') return;
            button.dataset.bound = 'true';

            button.addEventListener('click', async function () {
                const originalLabel = button.textContent;
                button.disabled = true;
                button.textContent = 'Carregando jogos...';

                try {
                    const params = new URLSearchParams({
                        category: button.dataset.category,
                        page: button.dataset.page,
                        per_page: button.dataset.perPage
                    });
                    const response = await fetch(`${button.dataset.endpoint}?${params}`, { headers: { Accept: 'application/json' } });
                    if (!response.ok) throw new Error('catalog_request_failed');
                    const data = await response.json();
                    data.items.forEach((game) => grid.insertAdjacentHTML('beforeend', cardMarkup(game)));

                    if (data.next_page) {
                        button.dataset.page = data.next_page;
                        button.disabled = false;
                        button.textContent = originalLabel;
                    } else {
                        button.remove();
                    }
                } catch (error) {
                    button.disabled = false;
                    button.textContent = 'Tentar novamente';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', initProgressiveCatalog);
        document.addEventListener('turbo:load', initProgressiveCatalog);
        initProgressiveCatalog();
    })();
</script>

@endsection
