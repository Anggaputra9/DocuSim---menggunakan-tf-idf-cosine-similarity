@extends('layouts.app')
@section('title', 'Riwayat Pengecekan')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold">Riwayat Pengecekan</h1>
            <p class="text-slate-500 mt-1">Daftar abstrak yang pernah dianalisis sistem.</p>
        </div>
        <a href="{{ route('similarity.create') }}" class="gradient-bg text-white px-5 py-3 rounded-xl font-semibold hover:opacity-95 inline-flex items-center gap-2">
            + Cek Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @if($checks->isEmpty())
            <p class="text-slate-400 text-center p-12">Belum ada riwayat. Silakan lakukan pengecekan pertama.</p>
        @else
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-6 py-3">Judul / Abstrak</th>
                        <th class="text-left px-6 py-3">Skor Tertinggi</th>
                        <th class="text-left px-6 py-3">Tanggal</th>
                        <th class="text-right px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($checks as $c)
                        @php $pct = round($c->highest_score * 100, 1); @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <a href="{{ route('similarity.show', $c) }}" class="font-medium text-slate-800 hover:text-indigo-600 line-clamp-1">
                                    {{ $c->input_title ?: 'Tanpa judul' }}
                                </a>
                                <p class="text-xs text-slate-400 line-clamp-1 mt-1">{{ \Illuminate\Support\Str::limit($c->input_abstract, 100) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-2 {{ $pct >= 70 ? 'bg-rose-500' : ($pct >= 40 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="font-semibold {{ $pct >= 70 ? 'text-rose-600' : ($pct >= 40 ? 'text-amber-600' : 'text-emerald-600') }}">{{ $pct }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $c->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('similarity.show', $c) }}" class="text-indigo-600 hover:underline text-sm">Detail</a>
                                <form action="{{ route('similarity.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline text-sm ml-3">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-100">{{ $checks->links() }}</div>
        @endif
    </div>
@endsection
