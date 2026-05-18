@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-2">Judul <span class="text-rose-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $corpus->title ?? '') }}" required
            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Penulis</label>
        <input type="text" name="author" value="{{ old('author', $corpus->author ?? '') }}"
            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tahun</label>
            <input type="number" name="year" value="{{ old('year', $corpus->year ?? '') }}"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $corpus->category ?? '') }}"
                placeholder="cth. NLP"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
        </div>
    </div>
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-2">Abstrak <span class="text-rose-500">*</span></label>
        <textarea name="abstract" rows="8" required
            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-y">{{ old('abstract', $corpus->abstract ?? '') }}</textarea>
    </div>
</div>

<div class="mt-8 flex flex-col sm:flex-row gap-3 justify-end">
    <a href="{{ route('corpus.index') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-center">Batal</a>
    <button type="submit" class="gradient-bg text-white px-8 py-3 rounded-xl font-semibold hover:opacity-95">
        Simpan
    </button>
</div>
