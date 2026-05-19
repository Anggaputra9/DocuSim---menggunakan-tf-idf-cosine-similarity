@extends('layouts.app')
@section('title', 'Hasil Similarity')

@section('content')
    @php
        $results   = $check->results ?? [];
        $topTerms  = $check->top_terms ?? [];
        $highest   = round(($check->highest_score ?? 0) * 100, 2);

        if ($highest >= 70)      { $levelLabel = 'TINGGI';  $levelBg = 'nb-bg-pink';   $levelText = '#EF4444'; }
        elseif ($highest >= 40)  { $levelLabel = 'SEDANG';  $levelBg = 'nb-bg-orange'; $levelText = '#FB923C'; }
        else                     { $levelLabel = 'RENDAH';  $levelBg = 'nb-bg-lime';   $levelText = '#10B981'; }
    @endphp

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('similarity.history') }}" class="inline-flex items-center gap-1 text-sm font-bold hover:bg-[var(--nb-yellow)] px-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                KEMBALI KE RIWAYAT
            </a>
            <span class="nb-tag mt-3">// hasil analisis</span>
            <h1 class="nb-display text-3xl md:text-4xl mt-3">{{ $check->input_title ?: 'Hasil Pengecekan' }}</h1>
            <p class="text-sm font-semibold text-black/60 mt-1">Diproses {{ $check->created_at->format('d M Y, H:i') }}</p>
        </div>
        <form action="{{ route('similarity.destroy', $check) }}" method="POST" onsubmit="return confirm('Hapus pengecekan ini?')">
            @csrf @method('DELETE')
            <button class="nb-bg-pink nb-border px-4 py-2 font-bold text-sm nb-shadow-sm nb-btn">HAPUS</button>
        </form>
    </div>

    <!-- Hero summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Gauge -->
        <div class="bg-white nb-border nb-shadow p-6 flex flex-col items-center text-center">
            <span class="nb-tag">// score</span>
            <p class="text-xs font-bold uppercase tracking-wider mt-3">Skor Kemiripan Tertinggi</p>
            <div class="relative my-4">
                <canvas id="gaugeChart" width="240" height="170"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pt-6">
                    <span class="nb-display text-5xl" style="color: {{ $levelText }}">{{ $highest }}%</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest mt-1">Cosine Similarity</span>
                </div>
            </div>
            <span class="{{ $levelBg }} nb-border-2 px-4 py-1.5 nb-display text-sm">
                TINGKAT {{ $levelLabel }}
            </span>
        </div>

        <!-- Bar chart of all results -->
        <div class="lg:col-span-2 bg-white nb-border nb-shadow p-6">
            <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-black">
                <h3 class="nb-display text-lg">Top {{ count($results) }} Dokumen Paling Mirip</h3>
                <span class="nb-badge bg-white">% TERHADAP QUERY</span>
            </div>
            <div style="height: 280px"><canvas id="rankChart"></canvas></div>
        </div>
    </div>

    <!-- Top terms (word importance) -->
    @if(!empty($topTerms))
    <div class="bg-white nb-border nb-shadow p-6 mb-8">
        <div class="flex items-center justify-between mb-4 pb-3 border-b-2 border-black">
            <h3 class="nb-display text-lg">Kata Kunci Penting</h3>
            <span class="nb-tag">// tf-idf</span>
        </div>
        <p class="text-sm font-semibold mb-4">Bobot TF-IDF teratas dari kata pada abstrak yang dianalisis. Semakin tinggi bobot, semakin dominan kata tersebut.</p>
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($topTerms as $i => $t)
                @php
                    $size = 0.85 + min($t['weight'] * 1.5, 0.9);
                    $palette = ['nb-bg-yellow', 'nb-bg-pink', 'nb-bg-lime', 'nb-bg-sky', 'nb-bg-orange'];
                    $bg = $palette[$i % count($palette)];
                @endphp
                <span class="{{ $bg }} nb-border-2 px-3 py-1.5 font-bold"
                      style="font-size: {{ $size }}rem">
                    {{ $t['term'] }} <span class="opacity-70 text-xs">({{ $t['weight'] }})</span>
                </span>
            @endforeach
        </div>
        <div style="height: 240px"><canvas id="termChart"></canvas></div>
    </div>
    @endif

    <!-- Detail dokumen mirip -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="nb-display text-2xl">Rincian Dokumen</h3>
            <span class="nb-tag">// detail</span>
        </div>
        @foreach($results as $i => $r)
            @php
                $pct = $r['score_percent'] ?? round(($r['score'] ?? 0) * 100, 2);
                if ($pct >= 70)      { $rowBg = 'nb-bg-pink';   $rowFill = '#EF4444'; }
                elseif ($pct >= 40)  { $rowBg = 'nb-bg-orange'; $rowFill = '#FB923C'; }
                else                 { $rowBg = 'nb-bg-lime';   $rowFill = '#10B981'; }
            @endphp
            <div class="bg-white nb-border nb-shadow p-6 nb-card-hover">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 text-xs font-bold mb-2 flex-wrap">
                            <span class="nb-bg-ink text-[var(--nb-yellow)] px-2 py-0.5 nb-display">#{{ $i + 1 }}</span>
                            @if(!empty($r['category']))<span class="nb-badge bg-white">{{ $r['category'] }}</span>@endif
                            @if(!empty($r['year']))<span class="nb-badge bg-white">{{ $r['year'] }}</span>@endif
                            @if(!empty($r['author']))<span class="nb-badge bg-white">{{ $r['author'] }}</span>@endif
                        </div>
                        <p class="font-bold text-lg leading-snug">{{ $r['title'] ?? 'Tanpa judul' }}</p>
                    </div>
                    <span class="{{ $rowBg }} nb-border-2 px-4 py-2 nb-display text-2xl">
                        {{ $pct }}%
                    </span>
                </div>

                <div class="mt-4 nb-score-track">
                    <div class="nb-score-fill" style="width: {{ $pct }}%; background: {{ $rowFill }}"></div>
                </div>

                <details class="mt-4 text-sm">
                    <summary class="cursor-pointer font-bold inline-block hover:bg-[var(--nb-yellow)] px-1">▸ Lihat abstrak</summary>
                    <p class="mt-3 leading-relaxed font-medium border-l-4 border-black pl-4">{{ $r['abstract'] ?? '' }}</p>
                </details>
            </div>
        @endforeach
    </div>

    <!-- Input abstrak -->
    <div class="mt-8 nb-bg-yellow nb-border nb-shadow p-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="nb-bg-ink text-[var(--nb-yellow)] px-2 py-0.5 nb-display text-xs">QUERY</span>
            <h3 class="nb-display text-lg">Abstrak yang Dianalisis</h3>
        </div>
        <p class="text-sm leading-relaxed font-medium whitespace-pre-line bg-white nb-border-2 p-4">{{ $check->input_abstract }}</p>
    </div>

    @push('scripts')
    <script>
        const results  = @json($results);
        const topTerms = @json($topTerms);
        const highest  = {{ $highest }};

        // Brutalist palette helpers
        const NB = { ink: '#000', yellow: '#FACC15', pink: '#F472B6', lime: '#A3E635', sky: '#60A5FA', orange: '#FB923C', red: '#EF4444', emerald: '#10B981' };
        const colorFor = p => p >= 70 ? NB.red : (p >= 40 ? NB.orange : NB.emerald);

        // Default chart styling
        Chart.defaults.font.family = "'Space Grotesk', sans-serif";
        Chart.defaults.font.weight = '600';
        Chart.defaults.color = '#000';

        // ===== Gauge chart =====
        new Chart(document.getElementById('gaugeChart'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [highest, 100 - highest],
                    backgroundColor: [colorFor(highest), '#fff'],
                    borderColor: '#000',
                    borderWidth: 3,
                    circumference: 180,
                    rotation: 270,
                }]
            },
            options: {
                cutout: '70%',
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
                    backgroundColor: results.map(r => colorFor(r.score_percent ?? (r.score || 0) * 100)),
                    borderColor: '#000',
                    borderWidth: 2,
                    borderRadius: 0,
                }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, max: 100,
                         grid: { color: '#000', lineWidth: 1, drawTicks: false },
                         border: { color: '#000', width: 2 },
                         ticks: { callback: v => v + '%', font: { weight: '700' } } },
                    y: { grid: { display: false }, border: { color: '#000', width: 2 },
                         ticks: { font: { weight: '700' } } },
                },
            }
        });

        // ===== Term importance chart =====
        if (topTerms && topTerms.length) {
            const termPalette = [NB.yellow, NB.pink, NB.lime, NB.sky, NB.orange];
            new Chart(document.getElementById('termChart'), {
                type: 'bar',
                data: {
                    labels: topTerms.map(t => t.term),
                    datasets: [{
                        label: 'Bobot TF-IDF',
                        data: topTerms.map(t => t.weight),
                        backgroundColor: topTerms.map((_, i) => termPalette[i % termPalette.length]),
                        borderColor: '#000',
                        borderWidth: 2,
                        borderRadius: 0,
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true,
                             grid: { color: '#000', lineWidth: 1, drawTicks: false },
                             border: { color: '#000', width: 2 },
                             ticks: { font: { weight: '700' } } },
                        x: { grid: { display: false }, border: { color: '#000', width: 2 },
                             ticks: { font: { weight: '700' } } },
                    }
                }
            });
        }
    </script>
    @endpush
@endsection
