@extends('layouts.app')
@section('title', 'Corpus')

@section('content')
    <div class="flex items-end justify-between mb-8 flex-wrap gap-4">
        <div>
            <span class="nb-tag">// corpus</span>
            <h1 class="nb-display text-4xl md:text-5xl mt-3">Corpus Dokumen.</h1>
            <p class="font-semibold text-black/70 mt-2">Dokumen referensi yang menjadi pembanding untuk setiap pengecekan.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('corpus.template') }}" class="bg-white nb-border-2 px-3 py-2 text-sm font-bold nb-shadow-sm nb-btn inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                TEMPLATE CSV
            </a>
            <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="nb-bg-lime nb-border-2 px-3 py-2 text-sm font-bold nb-shadow-sm nb-btn inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M17 8l-5-5-5 5M12 3v12"/></svg>
                IMPORT CSV
            </button>
            <a href="{{ route('corpus.create') }}" class="nb-bg-yellow nb-border px-5 py-2 font-bold nb-shadow nb-btn inline-flex items-center gap-2">
                + TAMBAH
            </a>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4">
        <div class="bg-white nb-border nb-shadow-xl w-full max-w-lg p-6">
            <div class="flex items-start justify-between mb-4 pb-3 border-b-2 border-black">
                <div>
                    <span class="nb-tag">// import</span>
                    <h3 class="nb-display text-xl mt-2">Import Corpus dari CSV</h3>
                    <p class="text-xs font-semibold text-black/60 mt-1">Upload file CSV untuk menambah dokumen secara batch.</p>
                </div>
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="nb-bg-pink nb-border-2 p-1.5 nb-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('corpus.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider mb-2">File CSV</label>
                    <input type="file" name="file" accept=".csv,text/csv" required
                           class="block w-full text-sm font-semibold file:mr-4 file:py-2 file:px-4 file:border-2 file:border-black file:bg-[var(--nb-yellow)] file:text-black file:font-bold file:cursor-pointer hover:file:bg-[var(--nb-pink)] nb-border-2 p-2 bg-white">
                </div>

                <div class="nb-bg-cream nb-border-2 p-4 text-xs font-semibold space-y-2">
                    <p class="nb-display text-sm">Format yang didukung:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        <li>Header wajib: <code class="bg-white nb-border-2 px-1.5 font-mono">judul</code>, <code class="bg-white nb-border-2 px-1.5 font-mono">abstrak</code></li>
                        <li>Header opsional: <code class="bg-white nb-border-2 px-1.5 font-mono">no</code>, <code class="bg-white nb-border-2 px-1.5 font-mono">penulis</code>, <code class="bg-white nb-border-2 px-1.5 font-mono">tahun</code>, <code class="bg-white nb-border-2 px-1.5 font-mono">kategori</code></li>
                        <li>Delimiter: koma (,) atau titik koma (;) — auto-detect</li>
                        <li>Encoding: UTF-8. Abstrak min. 30 karakter.</li>
                    </ul>
                    <p class="pt-2">
                        Belum punya template?
                        <a href="{{ route('corpus.template') }}" class="font-bold underline decoration-2 hover:bg-[var(--nb-yellow)]">Download template</a>.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                            class="bg-white nb-border-2 px-4 py-2 font-bold text-sm nb-btn">BATAL</button>
                    <button type="submit" class="nb-bg-lime nb-border px-5 py-2 font-bold text-sm nb-shadow-sm nb-btn">UPLOAD & IMPORT</button>
                </div>
            </form>
        </div>
    </div>

    <form method="GET" class="mb-6">
        <div class="relative max-w-md">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, penulis, atau abstrak..." class="nb-input pl-11">
            <svg class="absolute left-3 top-3.5 w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
    </form>

    <div class="bg-white nb-border nb-shadow overflow-hidden">
        @if($corpus->isEmpty())
            <div class="text-center p-16">
                <p class="nb-display text-2xl">// kosong</p>
                <p class="font-semibold mt-2 text-black/60">Corpus kosong. Tambahkan dokumen untuk memulai.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="nb-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Tahun</th>
                            <th>Kategori</th>
                            <th style="text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($corpus as $doc)
                            <tr>
                                <td class="max-w-md">
                                    <p class="font-bold line-clamp-1">{{ $doc->title }}</p>
                                    <p class="text-xs font-medium text-black/60 line-clamp-2 mt-1">{{ \Illuminate\Support\Str::limit($doc->abstract, 130) }}</p>
                                </td>
                                <td class="font-semibold">{{ $doc->author ?: '-' }}</td>
                                <td class="font-semibold">{{ $doc->year ?: '-' }}</td>
                                <td>
                                    @if($doc->category)
                                        <span class="nb-badge nb-bg-yellow">{{ $doc->category }}</span>
                                    @else
                                        <span class="text-black/40 text-xs font-bold">-</span>
                                    @endif
                                </td>
                                <td style="text-align:right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('corpus.edit', $doc) }}"
                                           class="nb-bg-sky nb-border-2 px-3 py-1.5 text-xs font-bold nb-btn">EDIT</a>
                                        <form action="{{ route('corpus.destroy', $doc) }}" method="POST" onsubmit="return confirm('Hapus dokumen ini dari corpus?')">
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
            <div class="px-6 py-4 border-t-2 border-black bg-[var(--nb-bg)]">{{ $corpus->links() }}</div>
        @endif
    </div>
@endsection
