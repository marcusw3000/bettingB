@extends('frontend.Minimal.layouts.clean')

@section('page-title', $content['title'])

@section('content')
<div class="max-w-6xl mx-auto space-y-6" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
    <section class="rounded-3xl border border-white/[0.08] bg-gradient-to-br from-[#15243a] via-[#121622] to-[#0e1c20] p-6 sm:p-9 shadow-2xl">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-primary/15 text-primary flex items-center justify-center flex-shrink-0 border border-primary/20"><span class="material-symbols-outlined text-3xl">help</span></div>
            <div>
                <p class="text-[11px] font-mono-jet text-primary uppercase tracking-[0.2em] mb-1">{{ $brandName ?? settings('app_name', 'Social Gaming') }}</p>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">{{ $content['title'] }}</h1>
                <p class="text-sm sm:text-base text-on-surface-muted mt-2 max-w-3xl leading-relaxed">{{ $content['intro'] }}</p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($content['sections'] as $section)
            <article class="rounded-2xl bg-[#121622] border border-white/[0.08] p-5 sm:p-6 flex gap-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-xl">{{ $section['icon'] }}</span></div>
                <div>
                    <h2 class="text-base font-extrabold text-white">{{ $section['title'] }}</h2>
                    <p class="text-sm text-on-surface-muted leading-relaxed mt-1.5">{{ $section['body'] }}</p>
                </div>
            </article>
        @endforeach
    </section>

    <div class="text-center pb-6">
        <a href="{{ route('frontend.game.list') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/[0.06] border border-white/[0.1] text-white hover:bg-white/[0.1] no-underline text-xs font-bold"><span class="material-symbols-outlined text-base">arrow_back</span>Voltar ao lobby</a>
    </div>
</div>
@endsection
