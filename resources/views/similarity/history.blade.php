@extends('layouts.app')
@section('title', 'Riwayat Pengecekan')

@section('content')
    <div class="flex items-end justify-between mb-8 flex-wrap gap-4">
        <div>
            <span class="nb-tag">// history</span>
            <h1 class="nb-display text-4xl md:text-5xl mt-3">Riwayat Pengecekan.</h1>
            <p class="font-semibold text-black/70 mt-2">Daftar abstrak yang pernah dianalisis sistem.</p>
        </div>
        <a href="{{ route('similarity.create') }}"
           class="nb-bg-yellow nb-border px-5 py-3 font-bold nb-shadow nb-btn inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            CEK BARU
        </a>
    </div>

    <div class="bg-white nb-border nb-shadow overflow-hidden">
        @if($checks->isEmpty())
            <div class="text-center p-16">
                <p class="nb-display text-2xl">// kosong</p>
                <p class="font-semibold mt-2 text-black/60">Belum ada riwayat. Silakan lakukan pengecekan pertama.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="nb-table">
                    <thead>
                        <tr>
                            <th>Judul / Abstrak</th>
                            <th>Skor Tertinggi</th>
                            <th>Tanggal</th>
                            <th class="text-right" style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checks as $c)
                            @php
                                $pct = round($c->highest_score * 100, 1);
                                if ($pct >= 70)     { $tone = 'nb-bg-pink';   $fill = '#EF4444'; $tag = 'PLAGIAT?'; }
                                elseif ($pct >= 40) { $tone = 'nb-bg-orange'; $fill = '#FB923C'; $tag = 'SEDANG'; }
                                else                { $tone = 'nb-bg-lime';   $fill = '#10B981'; $tag = 'AMAN'; }
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('similarity.show', $c) }}" class="font-bold hover:bg-[var(--nb-yellow)] line-clamp-1">
                                        {{ $c->input_title ?: 'Tanpa judul' }}
                                    </a>
                                    <p class="text-xs font-medium text-black/60 line-clamp-1 mt-1">{{ \Illuminate\Support\Str::limit($c->input_abstract, 100) }}</p>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-28 nb-score-track">
                                            <div class="nb-score-fill" style="width: {{ $pct }}%; background: {{ $fill }}"></div>
                                        </div>
                                        <span class="nb-display text-base">{{ $pct }}%</span>
                                        <span class="nb-badge {{ $tone }}">{{ $tag }}</span>
                                    </div>
                                </td>
                                <td class="text-sm font-semibold">{{ $c->created_at->format('d M Y H:i') }}</td>
                                <td style="text-align:right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('similarity.show', $c) }}"
                                           class="nb-bg-sky nb-border-2 px-3 py-1.5 text-xs font-bold nb-btn">DETAIL</a>
                                        <form action="{{ route('similarity.destroy', $c) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                            @csrf @method('DELETE')
                                            <button class="nb-bg-pink nb-border-2 px-3 py-1.5 text-xs font-bold nb-btn">HAPUS</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t-2 border-black bg-[var(--nb-bg)]">{{ $checks->links() }}</div>
        @endif
    </div>
@endsection
