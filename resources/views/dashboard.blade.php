@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <!-- Page header -->
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="nb-tag">// dashboard</span>
            <h1 class="nb-display text-4xl md:text-5xl mt-3">Halo, Selamat Datang.</h1>
            <p class="font-semibold text-black/70 mt-2">Pantau performa sistem deteksi kemiripan abstrak.</p>
        </div>
        <a href="{{ route('similarity.create') }}"
           class="nb-bg-ink text-white nb-border px-5 py-3 font-bold nb-shadow nb-btn inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            CEK BARU
        </a>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="nb-bg-pink nb-border nb-shadow nb-card-hover p-6">
            <div class="flex items-start justify-between">
                <div class="nb-bg-ink p-3 nb-border-2">
                    <svg class="w-6 h-6 text-[var(--nb-pink)]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"/></svg>
                </div>
                <span class="nb-badge bg-white">CORPUS</span>
            </div>
            <p class="nb-display text-5xl mt-6 leading-none">{{ number_format($stats['corpus_count']) }}</p>
            <p class="text-sm font-semibold mt-2">Dokumen referensi siap dibandingkan</p>
        </div>

        <div class="nb-bg-lime nb-border nb-shadow nb-card-hover p-6">
            <div class="flex items-start justify-between">
                <div class="nb-bg-ink p-3 nb-border-2">
                    <svg class="w-6 h-6 text-[var(--nb-lime)]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
                <span class="nb-badge bg-white">CHECKS</span>
            </div>
            <p class="nb-display text-5xl mt-6 leading-none">{{ number_format($stats['check_count']) }}</p>
            <p class="text-sm font-semibold mt-2">Abstrak yang sudah pernah dianalisis</p>
        </div>

        <div class="nb-bg-sky nb-border nb-shadow nb-card-hover p-6">
            <div class="flex items-start justify-between">
                <div class="nb-bg-ink p-3 nb-border-2">
                    <svg class="w-6 h-6 text-[var(--nb-sky)]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <span class="nb-badge bg-white">AVG SCORE</span>
            </div>
            <p class="nb-display text-5xl mt-6 leading-none">{{ $stats['avg_score'] }}<span class="text-2xl">%</span></p>
            <p class="text-sm font-semibold mt-2">Rata-rata skor tertinggi seluruh pengecekan</p>
        </div>
    </div>

    <!-- CTA + recent -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 nb-bg-yellow nb-border nb-shadow-lg p-6 relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-32 h-32 nb-dots opacity-40"></div>
            <span class="nb-tag">// quick action</span>
            <h3 class="nb-display text-3xl mt-3 leading-tight">Cek Abstrak Baru.</h3>
            <p class="text-sm font-semibold mt-3">Tempel abstrak skripsi atau paper, sistem akan menghitung kemiripan terhadap seluruh corpus dengan TF-IDF + Cosine Similarity.</p>
            <a href="{{ route('similarity.create') }}"
               class="mt-6 inline-flex items-center gap-2 nb-bg-ink text-white nb-border px-5 py-3 font-bold nb-btn nb-shadow">
                MULAI CEK
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="lg:col-span-2 bg-white nb-border nb-shadow p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-black">
                <h3 class="nb-display text-xl">Pengecekan Terbaru</h3>
                <a href="{{ route('similarity.history') }}" class="text-sm font-bold underline decoration-2 underline-offset-2 hover:bg-[var(--nb-yellow)]">Lihat semua →</a>
            </div>
            @if($stats['last_checks']->isEmpty())
                <div class="text-center py-12">
                    <p class="nb-display text-lg">// kosong</p>
                    <p class="text-sm font-semibold mt-2 text-black/60">Belum ada pengecekan. Mulai dari menu "Cek Similarity".</p>
                </div>
            @else
                <ul class="divide-y-2 divide-black">
                    @foreach($stats['last_checks'] as $c)
                        @php
                            $pct = round($c->highest_score * 100, 1);
                            $tone = $pct >= 70 ? ['nb-bg-pink','PLAGIAT?'] : ($pct >= 40 ? ['nb-bg-orange','SEDANG'] : ['nb-bg-lime','AMAN']);
                        @endphp
                        <li class="py-3 flex items-center justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('similarity.show', $c) }}" class="font-bold hover:bg-[var(--nb-yellow)] line-clamp-1">
                                    {{ $c->input_title ?: 'Tanpa judul' }}
                                </a>
                                <p class="text-xs font-semibold text-black/60 mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="nb-badge {{ $tone[0] }}">{{ $tone[1] }}</span>
                                <span class="nb-display text-lg">{{ $pct }}%</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- How it works -->
    <div class="mt-10 bg-white nb-border nb-shadow p-6">
        <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-black">
            <h3 class="nb-display text-xl">Bagaimana Sistem Bekerja</h3>
            <span class="nb-tag">// pipeline</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach([
                ['nb-bg-pink',   '01', 'Input Abstrak',   'User menempel abstrak melalui form Laravel.'],
                ['nb-bg-yellow', '02', 'Kirim ke Python', 'Service mengirim corpus + query JSON via stdin.'],
                ['nb-bg-lime',   '03', 'TF-IDF + Cosine', 'scikit-learn membangun vector & menghitung kemiripan.'],
                ['nb-bg-sky',    '04', 'Visualisasi',     'Hasil ranking + top-terms ditampilkan dalam chart.'],
            ] as $step)
                <div class="{{ $step[0] }} nb-border nb-shadow-sm p-4 nb-card-hover">
                    <p class="nb-display text-3xl">{{ $step[1] }}</p>
                    <p class="font-bold mt-2">{{ $step[2] }}</p>
                    <p class="text-xs font-semibold mt-1">{{ $step[3] }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection
