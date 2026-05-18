# DocuSim — Dokumen Similarity (Laravel + Python TF-IDF)

Sistem berbasis web untuk mendeteksi kemiripan **abstrak** terhadap **corpus dokumen referensi** menggunakan algoritma **TF-IDF + Cosine Similarity**. UI dibangun dengan **Laravel 11 + Tailwind + Chart.js**. Perhitungan model bisa pakai dua driver: **PHP native** (default, tanpa dependency) atau **Python scikit-learn**.

---

## Fitur

- 🎯 **Cek kemiripan abstrak** terhadap seluruh corpus dengan ranking top-K.
- 📊 **Visualisasi**: gauge skor tertinggi, bar chart ranking, word cloud + bar chart bobot TF-IDF.
- 🗂️ **CRUD Corpus**: kelola dokumen referensi langsung dari UI (judul, penulis, tahun, kategori, abstrak).
- � **Import CSV**: tambah corpus secara batch dengan format minimal `no, judul, abstrak` (kolom opsional: `penulis`, `tahun`, `kategori`).
- �🕘 **Riwayat pengecekan**: setiap analisis tersimpan beserta hasilnya.
- 🎨 **UI modern**: sidebar gradient, kartu, badge level kemiripan (rendah/sedang/tinggi), responsif.

---

## Arsitektur Integrasi Model

```
┌────────────┐    HTTP    ┌──────────────────┐   stdin (JSON)   ┌─────────────────┐
│  Browser   │ ─────────▶ │ Laravel          │ ───────────────▶ │ python/         │
│  (Blade +  │            │  - Controller    │                  │  similarity.py  │
│   Chart.js)│ ◀───────── │  - Service       │ ◀─────────────── │  (scikit-learn) │
└────────────┘    HTML    │  - Eloquent (DB) │   stdout (JSON)  └─────────────────┘
                          └──────────────────┘
                                  │
                                  ▼
                          ┌──────────────────┐
                          │ SQLite           │
                          │  corpus_documents│
                          │  similarity_     │
                          │     checks       │
                          └──────────────────┘
```

### Bagaimana Model Terintegrasi

Sistem mendukung **dua driver** yang bisa di-switch dari `.env`:

#### Driver 1: PHP native (default — `SIMILARITY_DRIVER=php`)

TF-IDF + Cosine ditulis ulang di PHP dengan formula yang **sama persis** dengan sklearn `TfidfVectorizer` (smooth IDF: `ln((1+N)/(1+df)) + 1`, l2-normalize, ngram 1-2). Tidak ada child process, tidak ada Python — anti-error winsock/firewall di Windows. Semua jalan di proses Laravel sendiri.

Alur:
1. `SimilarityController@check` validasi input → `SimilarityService->check()`.
2. Service pilih driver (default `php`) → delegasi ke `TfIdfPhpService->check()`.
3. `TfIdfPhpService` ambil corpus dari DB, tokenize (lowercase, hapus tanda baca, stopword Bahasa Indonesia, ngram 1-2), hitung DF, IDF, vector TF-IDF, l2-normalize, lalu cosine similarity = dot product.
4. Hasil ranking + top terms dikembalikan ke controller, disimpan di `similarity_checks`, di-render Chart.js.

#### Driver 2: Python (`SIMILARITY_DRIVER=python`)

Pakai jika ingin model lebih advanced (IndoBERT/Sastrawi/Word2Vec). Laravel meng-eksekusi `python/similarity.py` via Symfony Process, kirim JSON via stdin, terima hasil via stdout. Kontrak JSON: `{query, corpus, top_k}` ↔ `{ok, results, top_terms, highest_score}`.

Komponen kunci di kode:

| Tugas | File |
|---|---|
| Model TF-IDF native PHP | `app/Services/TfIdfPhpService.php` |
| Router driver | `app/Services/SimilarityService.php` |
| Model TF-IDF Python (opsional) | `python/similarity.py` |
| Konfigurasi driver & python binary | `config/similarity.php` (env `SIMILARITY_DRIVER`, `PYTHON_BIN`) |
| Persisten corpus | `app/Models/CorpusDocument.php` + migration |
| Persisten history | `app/Models/SimilarityCheck.php` (kolom `results` & `top_terms` dicast `array`) |
| Visualisasi | `resources/views/similarity/show.blade.php` (Chart.js) |

### Mengganti Model

- Tetap pakai PHP, ubah preprocessing/stopword/n-gram di `app/Services/TfIdfPhpService.php`.
- Pindah ke Python untuk model deep learning: set `SIMILARITY_DRIVER=python` di `.env`, edit `python/similarity.py`, contoh: ganti `TfidfVectorizer` dengan `SentenceTransformer('paraphrase-multilingual-MiniLM-L12-v2')`.

Selama format JSON-nya tetap sama, **tidak ada perubahan apapun** yang dibutuhkan di sisi Laravel.

---

## Persyaratan

- PHP ≥ 8.2, Composer
- SQLite (default Laravel) — sudah otomatis ter-create
- (Opsional) Python ≥ 3.10 + scikit-learn — hanya jika pakai driver `python`

## Instalasi

```bash
# 1. Install Laravel deps
composer install

# 2. Migrasi & seed corpus contoh (10 dokumen)
php artisan migrate:fresh --seed

# 3. Jalankan server
php artisan serve
```

Buka `http://127.0.0.1:8000`. Driver PHP aktif by default — siap pakai tanpa dependency tambahan.

### (Opsional) Aktifkan driver Python

```bash
pip install -r python/requirements.txt
```

Lalu di `.env`:

```dotenv
SIMILARITY_DRIVER=python
PYTHON_BIN=python                # atau path absolut python.exe
```

## Konfigurasi `.env`

```dotenv
SIMILARITY_DRIVER=php            # php (default) | python
PYTHON_BIN=python                # binary python (jika driver=python)
SIMILARITY_TOP_K=5               # default jumlah dokumen yang ditampilkan
```

## Import Corpus dari CSV

Selain CRUD manual, corpus bisa di-import via file CSV.

1. Buka menu **Corpus** ▶ klik **Template CSV** untuk download contoh.
2. Format CSV (UTF-8, delimiter koma atau titik koma):

   ```csv
   no,judul,abstrak,penulis,tahun,kategori
   1,Judul dokumen,Isi abstrak minimal 30 karakter...,Nama Penulis,2024,NLP
   ```

   Kolom **wajib**: `judul`, `abstrak` (alias inggris `title`, `abstract` juga didukung).
   Kolom **opsional**: `no`, `penulis`/`author`, `tahun`/`year`, `kategori`/`category`.

3. Klik **Import CSV** ▶ pilih file ▶ Upload. Sistem akan menampilkan jumlah baris yang berhasil dan dilewati (jika judul kosong atau abstrak < 30 karakter).

## Struktur Database

**corpus_documents**
- `id`, `title`, `author`, `year`, `category`, `abstract`, timestamps

**similarity_checks**
- `id`, `input_title`, `input_abstract`, `highest_score` (float 0..1)
- `results` (JSON array dokumen mirip + score)
- `top_terms` (JSON array bobot kata penting)
- timestamps

## Catatan Performa

- TF-IDF di-fit ulang setiap pengecekan supaya vocab konsisten antara query dan corpus. Untuk corpus < 10.000 dokumen ini masih cepat (sub-detik).
- Bila corpus besar, pertimbangkan caching matrix TF-IDF (pickle) atau pindah ke service Python persistent (FastAPI) — kontrak service tetap sama.

## Lisensi

MIT — bebas dipakai untuk skripsi, tugas, atau portofolio.
