@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <label class="block text-xs font-bold uppercase tracking-wider mb-2">Judul <span class="nb-bg-pink nb-border-2 px-1.5 ml-1 text-[10px]">WAJIB</span></label>
        <input type="text" name="title" value="{{ old('title', $corpus->title ?? '') }}" required class="nb-input">
    </div>
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider mb-2">Penulis</label>
        <input type="text" name="author" value="{{ old('author', $corpus->author ?? '') }}" class="nb-input">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2">Tahun</label>
            <input type="number" name="year" value="{{ old('year', $corpus->year ?? '') }}" class="nb-input">
        </div>
        <div>
            <label class="block text-xs font-bold uppercase tracking-wider mb-2">Kategori</label>
            <input type="text" name="category" value="{{ old('category', $corpus->category ?? '') }}"
                placeholder="cth. NLP" class="nb-input">
        </div>
    </div>
    <div class="md:col-span-2">
        <label class="block text-xs font-bold uppercase tracking-wider mb-2">Abstrak <span class="nb-bg-pink nb-border-2 px-1.5 ml-1 text-[10px]">WAJIB</span></label>
        <textarea name="abstract" rows="8" required class="nb-input resize-y">{{ old('abstract', $corpus->abstract ?? '') }}</textarea>
    </div>
</div>

<div class="mt-8 flex flex-col sm:flex-row gap-3 justify-end">
    <a href="{{ route('corpus.index') }}" class="bg-white nb-border px-6 py-3 font-bold nb-shadow-sm nb-btn text-center">BATAL</a>
    <button type="submit" class="nb-bg-yellow nb-border px-8 py-3 font-bold nb-shadow nb-btn">SIMPAN</button>
</div>
