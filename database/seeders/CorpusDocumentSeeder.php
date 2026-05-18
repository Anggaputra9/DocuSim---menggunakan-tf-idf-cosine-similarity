<?php

namespace Database\Seeders;

use App\Models\CorpusDocument;
use Illuminate\Database\Seeder;

class CorpusDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title'    => 'Penerapan Algoritma TF-IDF dan Cosine Similarity untuk Deteksi Plagiarisme Abstrak Skripsi',
                'author'   => 'Andi Wijaya',
                'year'     => 2022,
                'category' => 'NLP',
                'abstract' => 'Penelitian ini membahas penerapan algoritma Term Frequency Inverse Document Frequency (TF-IDF) yang dikombinasikan dengan cosine similarity untuk mendeteksi tingkat kemiripan antar abstrak skripsi mahasiswa. Sistem dibangun berbasis web menggunakan kerangka kerja Laravel dengan modul perhitungan dilakukan oleh Python scikit-learn. Hasil pengujian menunjukkan bahwa metode mampu mengidentifikasi kemiripan dengan akurasi yang baik pada dataset 200 abstrak.',
            ],
            [
                'title'    => 'Klasifikasi Sentimen Ulasan Produk E-Commerce Menggunakan Naive Bayes',
                'author'   => 'Budi Santoso',
                'year'     => 2021,
                'category' => 'Machine Learning',
                'abstract' => 'Studi ini bertujuan mengklasifikasikan sentimen ulasan produk pada platform e-commerce ke dalam kelas positif dan negatif menggunakan algoritma Naive Bayes. Tahapan meliputi case folding, tokenisasi, stopword removal, dan pembobotan TF-IDF. Akurasi yang dicapai sebesar 85% pada data uji sebanyak 1.000 ulasan berbahasa Indonesia.',
            ],
            [
                'title'    => 'Sistem Rekomendasi Buku Perpustakaan Berbasis Content-Based Filtering',
                'author'   => 'Citra Lestari',
                'year'     => 2023,
                'category' => 'Information Retrieval',
                'abstract' => 'Penelitian ini merancang sistem rekomendasi buku perpustakaan menggunakan pendekatan content-based filtering. Representasi dokumen dibentuk dengan TF-IDF dan kemiripan antar buku dihitung menggunakan cosine similarity. Sistem mampu memberikan rekomendasi top-N buku yang relevan dengan riwayat peminjaman pengguna.',
            ],
            [
                'title'    => 'Analisis Performa Algoritma Dijkstra pada Pencarian Rute Terpendek Transportasi Umum',
                'author'   => 'Dewi Kartika',
                'year'     => 2020,
                'category' => 'Algoritma',
                'abstract' => 'Penelitian ini menganalisis performa algoritma Dijkstra dalam menentukan rute terpendek pada jaringan transportasi umum di kota besar. Variabel yang dievaluasi meliputi waktu komputasi, jumlah node, dan kepadatan graf. Hasil menunjukkan algoritma Dijkstra efektif untuk graf berukuran sedang dengan waktu eksekusi di bawah 1 detik.',
            ],
            [
                'title'    => 'Implementasi Convolutional Neural Network untuk Klasifikasi Citra Daun Tanaman',
                'author'   => 'Eka Pratama',
                'year'     => 2023,
                'category' => 'Deep Learning',
                'abstract' => 'Penelitian ini mengimplementasikan Convolutional Neural Network (CNN) untuk mengklasifikasikan citra daun tanaman ke dalam beberapa jenis penyakit. Dataset terdiri atas 5.000 gambar yang dilatih menggunakan arsitektur ResNet-50. Akurasi validasi mencapai 92% dan model dapat membantu petani mendeteksi penyakit secara dini.',
            ],
            [
                'title'    => 'Perancangan Sistem Informasi Akademik Berbasis Web dengan Laravel',
                'author'   => 'Faisal Rahman',
                'year'     => 2022,
                'category' => 'Web Engineering',
                'abstract' => 'Penelitian ini merancang sistem informasi akademik perguruan tinggi berbasis web menggunakan kerangka kerja Laravel. Fitur utama meliputi manajemen mahasiswa, dosen, mata kuliah, jadwal, serta penilaian. Sistem diuji menggunakan metode black box dan menghasilkan tingkat keberhasilan fungsional 100%.',
            ],
            [
                'title'    => 'Deteksi Plagiarisme Dokumen Menggunakan Algoritma Rabin-Karp dan Jaccard Similarity',
                'author'   => 'Gita Permata',
                'year'     => 2021,
                'category' => 'NLP',
                'abstract' => 'Penelitian ini membahas deteksi plagiarisme pada dokumen teks dengan menggabungkan algoritma fingerprinting Rabin-Karp dan koefisien Jaccard. Sistem dievaluasi menggunakan dataset abstrak skripsi dan menunjukkan presisi yang lebih baik dibanding metode pencocokan string biasa pada kasus parafrase.',
            ],
            [
                'title'    => 'Peramalan Harga Saham Menggunakan Long Short-Term Memory (LSTM)',
                'author'   => 'Hadi Kurniawan',
                'year'     => 2023,
                'category' => 'Deep Learning',
                'abstract' => 'Penelitian ini meramalkan harga saham harian menggunakan jaringan saraf tiruan Long Short-Term Memory (LSTM). Data harga penutupan dilatih dengan window 60 hari dan menghasilkan RMSE sebesar 0.034. Model LSTM terbukti lebih akurat dibanding ARIMA pada data deret waktu yang dievaluasi.',
            ],
            [
                'title'    => 'Pengelompokan Topik Berita Online Menggunakan K-Means Clustering',
                'author'   => 'Indah Sari',
                'year'     => 2022,
                'category' => 'Machine Learning',
                'abstract' => 'Penelitian ini mengelompokkan berita online berbahasa Indonesia ke dalam topik-topik yang serupa menggunakan algoritma K-Means clustering. Representasi dokumen menggunakan TF-IDF dan jumlah cluster ditentukan melalui elbow method. Hasil clustering memiliki silhouette score 0.42 pada 1.500 artikel.',
            ],
            [
                'title'    => 'Sistem Pendukung Keputusan Pemilihan Laptop Menggunakan Metode SAW',
                'author'   => 'Joko Susilo',
                'year'     => 2020,
                'category' => 'Decision Support System',
                'abstract' => 'Penelitian ini membangun sistem pendukung keputusan untuk pemilihan laptop menggunakan metode Simple Additive Weighting (SAW). Kriteria yang digunakan meliputi harga, prosesor, RAM, penyimpanan, dan kartu grafis. Sistem berhasil memberikan rekomendasi laptop terbaik berdasarkan preferensi pengguna.',
            ],
        ];

        foreach ($items as $item) {
            CorpusDocument::create($item);
        }
    }
}
