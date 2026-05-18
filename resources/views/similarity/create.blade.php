@extends('layouts.app')
@section('title', 'Cek Similarity')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Cek Kemiripan Abstrak</h1>
        <p class="text-slate-500 mt-1">Tempel abstrak yang ingin diperiksa. Sistem akan menghitung kemiripan terhadap seluruh corpus.</p>
    </div>

    <form action="{{ route('similarity.check') }}" method="POST" class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-100">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Judul (opsional)</label>
                <input type="text" name="input_title" value="{{ old('input_title') }}"
                    placeholder="cth. Klasifikasi Sentimen Ulasan E-Commerce"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tampilkan Top-K</label>
                <input type="number" name="top_k" min="1" max="20" value="{{ old('top_k', 5) }}"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium text-slate-700 mb-2">Abstrak <span class="text-rose-500">*</span></label>
            <textarea name="input_abstract" rows="10" required
                placeholder="Tempel abstrak Anda di sini..."
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-y"
                oninput="updateCount(this)">{{ old('input_abstract') }}</textarea>
            <div class="flex justify-between mt-2 text-xs text-slate-400">
                <span>Min. 30 karakter</span>
                <span id="charCount">0 karakter</span>
            </div>
        </div>

        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-end">
            <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-center">Batal</a>
            <button type="submit" id="submitBtn" class="gradient-bg text-white px-8 py-3 rounded-xl font-semibold hover:opacity-95 inline-flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Analisis Similarity
            </button>
        </div>
    </form>

    <div class="mt-6 bg-indigo-50 border border-indigo-100 rounded-2xl p-5 text-sm text-indigo-900">
        <p class="font-semibold mb-1">💡 Tips</p>
        <ul class="list-disc pl-5 text-indigo-800/80 space-y-1">
            <li>Abstrak idealnya 100-300 kata agar TF-IDF menghasilkan vector yang representatif.</li>
            <li>Skor mendekati 100% menandakan kemiripan tinggi (potensi plagiarisme).</li>
            <li>Sistem tidak menyimpan abstrak ke layanan eksternal — semua proses lokal.</li>
        </ul>
    </div>

    @push('scripts')
    <script>
        function updateCount(el){
            document.getElementById('charCount').innerText = el.value.length + ' karakter';
        }
        const ta = document.querySelector('textarea[name=input_abstract]');
        if (ta) updateCount(ta);

        // loader saat submit
        document.querySelector('form').addEventListener('submit', function(){
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-opacity=".25" stroke-width="4"></circle><path d="M4 12a8 8 0 018-8" stroke-width="4"></path></svg> Menganalisis...';
        });
    </script>
    @endpush
@endsection
