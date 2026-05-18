@extends('layouts.app')
@section('title', 'Corpus')

@section('content')
    <div class="flex items-center justify-between mb-8 flex-wrap gap-3">
        <div>
            <h1 class="text-3xl font-bold">Corpus Dokumen</h1>
            <p class="text-slate-500 mt-1">Dokumen referensi yang menjadi pembanding untuk setiap pengecekan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('corpus.template') }}" class="px-4 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 inline-flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                Template CSV
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="px-4 py-3 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 inline-flex items-center gap-2 text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M17 8l-5-5-5 5M12 3v12"/></svg>
                Import CSV
            </button>
            <a href="{{ route('corpus.create') }}" class="gradient-bg text-white px-5 py-3 rounded-xl font-semibold hover:opacity-95 inline-flex items-center gap-2">
                + Tambah Dokumen
            </a>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold">Import Corpus dari CSV</h3>
                    <p class="text-xs text-slate-500 mt-1">Upload file CSV untuk menambah dokumen secara batch.</p>
                </div>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('corpus.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">File CSV</label>
                    <input type="file" name="file" accept=".csv,text/csv" required
                           class="block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-slate-200 rounded-xl p-2">
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs text-slate-600 space-y-2">
                    <p class="font-semibold text-slate-700">Format yang didukung:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Header wajib: <code class="bg-white px-1 rounded">judul</code>, <code class="bg-white px-1 rounded">abstrak</code></li>
                        <li>Header opsional: <code class="bg-white px-1 rounded">no</code>, <code class="bg-white px-1 rounded">penulis</code>, <code class="bg-white px-1 rounded">tahun</code>, <code class="bg-white px-1 rounded">kategori</code></li>
                        <li>Delimiter: koma (,) atau titik koma (;) — auto-detect</li>
                        <li>Encoding: UTF-8. Abstrak min. 30 karakter.</li>
                    </ul>
                    <p class="pt-2">
                        Belum punya template?
                        <a href="{{ route('corpus.template') }}" class="text-indigo-600 hover:underline font-semibold">Download template di sini</a>.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-semibold">
                        Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form method="GET" class="mb-6">
        <div class="relative max-w-md">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, atau abstrak..."
                   class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none bg-white">
            <svg class="absolute left-3 top-3.5 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @if($corpus->isEmpty())
            <p class="text-slate-400 text-center p-12">Corpus kosong. Tambahkan dokumen untuk memulai.</p>
        @else
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-6 py-3">Judul</th>
                        <th class="text-left px-6 py-3">Penulis</th>
                        <th class="text-left px-6 py-3">Tahun</th>
                        <th class="text-left px-6 py-3">Kategori</th>
                        <th class="text-right px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($corpus as $doc)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 max-w-md">
                                <p class="font-medium text-slate-800 line-clamp-1">{{ $doc->title }}</p>
                                <p class="text-xs text-slate-400 line-clamp-2 mt-1">{{ \Illuminate\Support\Str::limit($doc->abstract, 130) }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $doc->author ?: '-' }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $doc->year ?: '-' }}</td>
                            <td class="px-6 py-4">
                                @if($doc->category)
                                    <span class="px-2 py-1 rounded-full text-xs bg-indigo-50 text-indigo-700">{{ $doc->category }}</span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('corpus.edit', $doc) }}" class="text-indigo-600 hover:underline text-sm">Edit</a>
                                <form action="{{ route('corpus.destroy', $doc) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen ini dari corpus?')">
                                    @csrf @method('DELETE')
                                    <button class="text-rose-600 hover:underline text-sm ml-3">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-100">{{ $corpus->links() }}</div>
        @endif
    </div>
@endsection
