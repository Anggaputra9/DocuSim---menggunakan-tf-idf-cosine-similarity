@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-slate-500 mt-1">Pantau performa sistem deteksi kemiripan abstrak.</p>
    </div>

    <!-- Stats cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Corpus</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['corpus_count']) }}</p>
                </div>
                <div class="bg-indigo-100 text-indigo-600 rounded-xl p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"/></svg>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Dokumen referensi siap dibandingkan</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Total Pengecekan</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['check_count']) }}</p>
                </div>
                <div class="bg-emerald-100 text-emerald-600 rounded-xl p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Abstrak yang sudah pernah dianalisis</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm card-hover border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm">Rata-rata Kemiripan Tertinggi</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['avg_score'] }}<span class="text-lg text-slate-400">%</span></p>
                </div>
                <div class="bg-rose-100 text-rose-600 rounded-xl p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-3">Across seluruh pengecekan</p>
        </div>
    </div>

    <!-- CTA + recent -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 gradient-bg rounded-2xl p-6 text-white shadow-lg">
            <h3 class="text-xl font-bold mb-2">Cek Abstrak Baru</h3>
            <p class="text-white/80 text-sm mb-6">Tempel abstrak skripsi atau paper, sistem akan menghitung kemiripan terhadap seluruh corpus menggunakan TF-IDF + Cosine Similarity.</p>
            <a href="{{ route('similarity.create') }}" class="inline-flex items-center gap-2 bg-white text-indigo-600 font-semibold px-5 py-3 rounded-xl hover:bg-slate-100">
                Mulai Cek
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg">Pengecekan Terbaru</h3>
                <a href="{{ route('similarity.history') }}" class="text-indigo-600 text-sm hover:underline">Lihat semua</a>
            </div>
            @if($stats['last_checks']->isEmpty())
                <p class="text-slate-400 text-sm text-center py-10">Belum ada pengecekan. Mulai dari menu "Cek Similarity".</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($stats['last_checks'] as $c)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('similarity.show', $c) }}" class="font-medium text-slate-800 hover:text-indigo-600 line-clamp-1">
                                    {{ $c->input_title ?: 'Tanpa judul' }}
                                </a>
                                <p class="text-xs text-slate-400">{{ $c->created_at->diffForHumans() }}</p>
                            </div>
                            @php $pct = round($c->highest_score * 100, 1); @endphp
                            <span class="ml-4 text-sm font-semibold
                                {{ $pct >= 70 ? 'text-rose-600' : ($pct >= 40 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $pct }}%
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- How it works -->
    <div class="mt-10 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="font-bold text-lg mb-4">Bagaimana Sistem Bekerja</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="bg-indigo-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-2">1</div>
                <p class="font-semibold">Input Abstrak</p>
                <p class="text-slate-500 text-xs mt-1">User menempel abstrak melalui form Laravel.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="bg-indigo-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-2">2</div>
                <p class="font-semibold">Kirim ke Python</p>
                <p class="text-slate-500 text-xs mt-1">Service mengirim corpus + query JSON via stdin ke Python.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="bg-indigo-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-2">3</div>
                <p class="font-semibold">TF-IDF + Cosine</p>
                <p class="text-slate-500 text-xs mt-1">scikit-learn membangun vector & menghitung kemiripan.</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="bg-indigo-600 text-white w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold mb-2">4</div>
                <p class="font-semibold">Visualisasi Hasil</p>
                <p class="text-slate-500 text-xs mt-1">Hasil ranking + top-terms ditampilkan dalam chart.</p>
            </div>
        </div>
    </div>
@endsection
