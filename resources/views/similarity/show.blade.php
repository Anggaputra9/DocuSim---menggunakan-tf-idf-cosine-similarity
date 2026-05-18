@extends('layouts.app')
@section('title', 'Hasil Similarity')

@section('content')
    @php
        $results   = $check->results ?? [];
        $topTerms  = $check->top_terms ?? [];
        $highest   = round(($check->highest_score ?? 0) * 100, 2);
        $level     = $highest >= 70 ? ['Tinggi', 'rose'] : ($highest >= 40 ? ['Sedang', 'amber'] : ['Rendah', 'emerald']);
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('similarity.history') }}" class="text-sm text-slate-500 hover:text-indigo-600 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Riwayat
            </a>
            <h1 class="text-3xl font-bold mt-2">{{ $check->input_title ?: 'Hasil Pengecekan' }}</h1>
            <p class="text-slate-500 mt-1 text-sm">Diproses {{ $check->created_at->format('d M Y, H:i') }}</p>
        </div>
        <form action="{{ route('similarity.destroy', $check) }}" method="POST" onsubmit="return confirm('Hapus pengecekan ini?')">
            @csrf @method('DELETE')
            <button class="px-4 py-2 rounded-lg border border-rose-200 text-rose-600 hover:bg-rose-50 text-sm">Hapus</button>
        </form>
    </div>

    <!-- Hero summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Gauge -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center">
            <p class="text-slate-500 text-sm">Skor Kemiripan Tertinggi</p>
            <div class="relative my-4">
                <canvas id="gaugeChart" width="220" height="160"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pt-4">
                    <span class="text-5xl font-extrabold text-{{ $level[1] }}-600">{{ $highest }}%</span>
                    <span class="text-xs uppercase tracking-wide text-slate-400 mt-1">Cosine Similarity</span>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-{{ $level[1] }}-100 text-{{ $level[1] }}-700">
                Tingkat {{ $level[0] }}
            </span>
        </div>

        <!-- Bar chart of all results -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">Top {{ count($results) }} Dokumen Paling Mirip</h3>
                <span class="text-xs text-slate-400">% kemiripan terhadap query</span>
            </div>
            <div style="height: 260px"><canvas id="rankChart"></canvas></div>
        </div>
    </div>

    <!-- Top terms (word importance) -->
    @if(!empty($topTerms))
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 mb-8">
        <h3 class="font-bold mb-4">Kata Kunci Penting dari Abstrak Anda</h3>
        <p class="text-sm text-slate-500 mb-4">Bobot TF-IDF teratas dari kata pada abstrak yang dianalisis. Semakin tinggi bobot, semakin dominan kata tersebut.</p>
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($topTerms as $t)
                @php $size = 0.85 + min($t['weight'] * 1.5, 0.9); @endphp
                <span class="px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100"
                      style="font-size: {{ $size }}rem">
                    {{ $t['term'] }} <span class="text-indigo-400 text-xs">({{ $t['weight'] }})</span>
                </span>
            @endforeach
        </div>
        <div style="height: 220px"><canvas id="termChart"></canvas></div>
    </div>
    @endif

    <!-- Detail dokumen mirip -->
    <div class="space-y-4">
        <h3 class="font-bold text-lg">Rincian Dokumen</h3>
        @foreach($results as $i => $r)
            @php $pct = $r['score_percent'] ?? round(($r['score'] ?? 0) * 100, 2); @endphp
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                            <span class="bg-slate-100 px-2 py-0.5 rounded">#{{ $i + 1 }}</span>
                            @if(!empty($r['category']))<span>{{ $r['category'] }}</span>@endif
                            @if(!empty($r['year']))<span>· {{ $r['year'] }}</span>@endif
                            @if(!empty($r['author']))<span>· {{ $r['author'] }}</span>@endif
                        </div>
                        <p class="font-semibold text-slate-800">{{ $r['title'] ?? 'Tanpa judul' }}</p>
                    </div>
                    <span class="text-xl font-bold
                        {{ $pct >= 70 ? 'text-rose-600' : ($pct >= 40 ? 'text-amber-600' : 'text-emerald-600') }}">
                        {{ $pct }}%
                    </span>
                </div>

                <div class="mt-3 w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full
                        {{ $pct >= 70 ? 'bg-rose-500' : ($pct >= 40 ? 'bg-amber-500' : 'bg-emerald-500') }}"
                        style="width: {{ $pct }}%"></div>
                </div>

                <details class="mt-4 text-sm text-slate-600">
                    <summary class="cursor-pointer text-indigo-600 hover:underline">Lihat abstrak</summary>
                    <p class="mt-2 leading-relaxed">{{ $r['abstract'] ?? '' }}</p>
                </details>
            </div>
        @endforeach
    </div>

    <!-- Input abstrak -->
    <div class="mt-8 bg-slate-50 border border-slate-200 rounded-2xl p-6">
        <h3 class="font-bold mb-2">Abstrak yang Dianalisis</h3>
        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $check->input_abstract }}</p>
    </div>

    @push('scripts')
    <script>
        const results  = @json($results);
        const topTerms = @json($topTerms);
        const highest  = {{ $highest }};

        // ===== Gauge chart =====
        new Chart(document.getElementById('gaugeChart'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [highest, 100 - highest],
                    backgroundColor: [
                        highest >= 70 ? '#e11d48' : (highest >= 40 ? '#d97706' : '#10b981'),
                        '#e2e8f0'
                    ],
                    borderWidth: 0,
                    circumference: 180,
                    rotation: 270,
                }]
            },
            options: {
                cutout: '75%',
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                responsive: false,
            }
        });

        // ===== Bar chart ranking =====
        new Chart(document.getElementById('rankChart'), {
            type: 'bar',
            data: {
                labels: results.map(r => (r.title || '').substring(0, 40) + ((r.title || '').length > 40 ? '…' : '')),
                datasets: [{
                    label: 'Kemiripan (%)',
                    data: results.map(r => r.score_percent ?? Math.round((r.score || 0) * 10000) / 100),
                    backgroundColor: results.map(r => {
                        const p = r.score_percent ?? (r.score || 0) * 100;
                        return p >= 70 ? '#e11d48' : (p >= 40 ? '#d97706' : '#10b981');
                    }),
                    borderRadius: 8,
                }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, max: 100, grid: { color: '#f1f5f9' }, ticks: { callback: v => v + '%' } },
                    y: { grid: { display: false } },
                },
            }
        });

        // ===== Term importance chart =====
        if (topTerms && topTerms.length) {
            new Chart(document.getElementById('termChart'), {
                type: 'bar',
                data: {
                    labels: topTerms.map(t => t.term),
                    datasets: [{
                        label: 'Bobot TF-IDF',
                        data: topTerms.map(t => t.weight),
                        backgroundColor: '#6366f1',
                        borderRadius: 6,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } },
                    }
                }
            });
        }
    </script>
    @endpush
@endsection
