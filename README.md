# DocuSim — Dokumen Similarity (Laravel + Python TF-IDF)

Sistem berbasis web untuk mendeteksi kemiripan **abstrak** terhadap **corpus dokumen referensi** menggunakan algoritma **TF-IDF + Cosine Similarity** (scikit-learn). UI dibangun dengan **Laravel 11 + Tailwind + Chart.js**, sementara perhitungan ML dilakukan oleh **Python**.

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

Pendekatan yang dipakai: **Laravel memanggil Python sebagai child process**, bukan REST API terpisah. Lebih ringan untuk skripsi/portofolio dan tidak butuh infra tambahan (Flask/FastAPI/uvicorn).

Alur detail satu siklus pengecekan:

1. **User submit form** abstrak → `POST /similarity/check` (controller `SimilarityController@check`).
2. **Controller** validasi input lalu panggil `SimilarityService->check($abstract, $topK)`.
3. **Service** mengambil seluruh corpus dari DB (Eloquent), lalu meng-encode payload JSON:
   ```json
   {"query": "...abstrak input...", "corpus": [...], "top_k": 5}
   ```
4. **Symfony Process** mengeksekusi `python python/similarity.py` dan mengirim payload via **stdin**.
5. **Python** preprocessing (lowercase, hilangkan tanda baca, hapus stopword Bahasa Indonesia), build matrix TF-IDF (`TfidfVectorizer` dengan `ngram_range=(1,2)` & `sublinear_tf=True`), hitung `cosine_similarity` antara query dan setiap dokumen corpus, dan kembalikan ranking + bobot kata terpenting via **stdout** dalam format JSON.
6. **Service** men-decode output, **Controller** menyimpan ke tabel `similarity_checks` dan redirect ke halaman hasil.
7. **View** `similarity/show.blade.php` me-render visualisasi (gauge, bar chart, word importance) dengan Chart.js.

Komponen kunci di kode:

| Tugas | File |
|---|---|
| Model TF-IDF + Cosine | `python/similarity.py` |
| Jembatan Laravel ↔ Python | `app/Services/SimilarityService.php` |
| Konfigurasi binary Python | `config/similarity.php` (env `PYTHON_BIN`) |
| Persisten corpus | `app/Models/CorpusDocument.php` + migration |
| Persisten history | `app/Models/SimilarityCheck.php` (kolom `results` & `top_terms` dicast `array`) |
| Visualisasi | `resources/views/similarity/show.blade.php` (Chart.js) |

### Mengganti Model

Karena kontraknya **JSON di stdin/stdout**, kamu bisa mengganti `python/similarity.py` dengan:

- Sentence-BERT / IndoBERT (`sentence-transformers`) — cukup ubah cara membentuk embedding sebelum cosine similarity.
- Word2Vec / FastText — load model `.bin` di Python, hitung average embedding per dokumen.
- Algoritma Indonesia-specific (Sastrawi stemmer, dst) — tinggal preprocess di `preprocess()`.

Selama format input/output JSON-nya sama, **tidak ada perubahan apapun** yang dibutuhkan di sisi Laravel.

---

## Persyaratan

- PHP ≥ 8.2, Composer
- Python ≥ 3.10
- SQLite (default Laravel) — sudah otomatis ter-create

## Instalasi

```bash
# 1. Install Laravel deps (sudah dilakukan saat create-project)
composer install

# 2. Install Python deps untuk model
pip install -r python/requirements.txt

# 3. Migrasi & seed corpus contoh (10 dokumen)
php artisan migrate:fresh --seed

# 4. (Opsional) atur path python di .env jika python tidak ada di PATH
# PYTHON_BIN="C:/Python312/python.exe"

# 5. Jalankan server
php artisan serve
```

Buka `http://127.0.0.1:8000`.

## Konfigurasi

`.env` (opsional):

```dotenv
PYTHON_BIN=python                # default. Bisa diubah ke python3 / venv path absolut
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
