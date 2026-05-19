@extends('layouts.app')
@section('title', 'Cek Similarity')

@section('content')
    <div class="mb-8">
        <span class="nb-tag">// new check</span>
        <h1 class="nb-display text-4xl md:text-5xl mt-3">Cek Kemiripan Abstrak.</h1>
        <p class="font-semibold text-black/70 mt-2">Tempel abstrak yang ingin diperiksa. Sistem akan menghitung kemiripan terhadap seluruh corpus.</p>
    </div>

    <form action="{{ route('similarity.check') }}" method="POST" class="bg-white nb-border nb-shadow p-6 md:p-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider mb-2">Judul (opsional)</label>
                <input type="text" name="input_title" value="{{ old('input_title') }}"
                    placeholder="cth. Klasifikasi Sentimen Ulasan E-Commerce"
                    class="nb-input">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider mb-2">Top-K</label>
                <input type="number" name="top_k" min="1" max="20" value="{{ old('top_k', 5) }}" class="nb-input">
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-xs font-bold uppercase tracking-wider mb-2">Abstrak <span class="nb-bg-pink nb-border-2 px-1.5 ml-1 text-[10px]">WAJIB</span></label>
            <textarea name="input_abstract" rows="10" required
                placeholder="Tempel abstrak Anda di sini..."
                class="nb-input resize-y"
                oninput="updateCount(this)">{{ old('input_abstract') }}</textarea>
            <div class="flex justify-between mt-2 text-xs font-bold">
                <span class="text-black/60">Min. 30 karakter</span>
                <span id="charCount" class="nb-badge bg-white">0 KARAKTER</span>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-end">
            <a href="{{ route('dashboard') }}" class="bg-white nb-border px-6 py-3 font-bold nb-shadow-sm nb-btn text-center">BATAL</a>
            <button type="submit" id="submitBtn"
                class="nb-bg-yellow nb-border px-8 py-3 font-bold nb-shadow nb-btn inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                ANALISIS SIMILARITY
            </button>
        </div>
    </form>

    <div class="mt-6 nb-bg-sky nb-border nb-shadow p-5">
        <div class="flex items-center gap-2 mb-2">
            <span class="nb-bg-ink text-[var(--nb-yellow)] px-2 py-0.5 nb-display text-xs">TIPS</span>
            <p class="font-bold uppercase tracking-wider text-sm">Biar hasil maksimal</p>
        </div>
        <ul class="list-disc pl-5 text-sm font-semibold space-y-1">
            <li>Abstrak idealnya 100-300 kata agar TF-IDF menghasilkan vector yang representatif.</li>
            <li>Skor mendekati 100% menandakan kemiripan tinggi (potensi plagiarisme).</li>
            <li>Sistem tidak menyimpan abstrak ke layanan eksternal — semua proses lokal.</li>
        </ul>
    </div>

    @push('scripts')
    <script>
        function updateCount(el){
            document.getElementById('charCount').innerText = el.value.length + ' KARAKTER';
        }
        const ta = document.querySelector('textarea[name=input_abstract]');
        if (ta) updateCount(ta);

        // loader saat submit
        document.querySelector('form').addEventListener('submit', function(){
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-opacity=".25"></circle><path d="M4 12a8 8 0 018-8"></path></svg> MENGANALISIS...';
        });
    </script>
    @endpush
@endsection
