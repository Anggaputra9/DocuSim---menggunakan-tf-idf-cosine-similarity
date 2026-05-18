<?php

namespace App\Http\Controllers;

use App\Models\CorpusDocument;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CorpusController extends Controller
{
    public function index(Request $request)
    {
        $q       = $request->get('q');
        $corpus  = CorpusDocument::query()
            ->when($q, fn ($qq) => $qq->where('title', 'like', "%$q%")
                ->orWhere('author', 'like', "%$q%")
                ->orWhere('abstract', 'like', "%$q%"))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('corpus.index', compact('corpus', 'q'));
    }

    public function create()
    {
        return view('corpus.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        CorpusDocument::create($data);
        return redirect()->route('corpus.index')->with('ok', 'Dokumen ditambahkan ke corpus.');
    }

    public function edit(CorpusDocument $corpus)
    {
        return view('corpus.edit', compact('corpus'));
    }

    public function update(Request $request, CorpusDocument $corpus)
    {
        $data = $this->validateData($request);
        $corpus->update($data);
        return redirect()->route('corpus.index')->with('ok', 'Dokumen diperbarui.');
    }

    public function destroy(CorpusDocument $corpus)
    {
        $corpus->delete();
        return back()->with('ok', 'Dokumen dihapus dari corpus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title'    => 'required|string|max:255',
            'author'   => 'nullable|string|max:255',
            'year'     => 'nullable|integer|min:1900|max:2100',
            'category' => 'nullable|string|max:100',
            'abstract' => 'required|string|min:30',
        ]);
    }

    /**
     * Import corpus dari file CSV.
     * Format yang didukung:
     *   - Kolom minimal: no, judul, abstrak
     *   - Kolom opsional: penulis/author, tahun/year, kategori/category
     *   - Delimiter: koma (,) atau titik koma (;) — auto detect
     *   - Encoding: UTF-8
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path  = $request->file('file')->getRealPath();
        $rows  = $this->readCsv($path);

        if (empty($rows)) {
            return back()->withErrors(['file' => 'File CSV kosong atau tidak bisa dibaca.']);
        }

        // Normalisasi header -> snake case Indonesia/Inggris.
        $header = array_map(fn ($h) => strtolower(trim($h, "\xEF\xBB\xBF \t\n\r")), array_shift($rows));

        $map = [
            'title'    => $this->indexOf($header, ['judul', 'title']),
            'abstract' => $this->indexOf($header, ['abstrak', 'abstract']),
            'author'   => $this->indexOf($header, ['penulis', 'author']),
            'year'     => $this->indexOf($header, ['tahun', 'year']),
            'category' => $this->indexOf($header, ['kategori', 'category']),
        ];

        if ($map['title'] === null || $map['abstract'] === null) {
            return back()->withErrors([
                'file' => 'Header CSV harus minimal mengandung kolom "judul" dan "abstrak". '
                    . 'Header terdeteksi: ' . implode(', ', $header),
            ]);
        }

        $imported = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            $title    = trim($row[$map['title']] ?? '');
            $abstract = trim($row[$map['abstract']] ?? '');

            if ($title === '' || strlen($abstract) < 30) {
                $skipped++;
                continue;
            }

            CorpusDocument::create([
                'title'    => $title,
                'abstract' => $abstract,
                'author'   => $map['author']   !== null ? (trim($row[$map['author']]   ?? '') ?: null) : null,
                'year'     => $map['year']     !== null ? ((int) ($row[$map['year']]   ?? 0) ?: null) : null,
                'category' => $map['category'] !== null ? (trim($row[$map['category']] ?? '') ?: null) : null,
            ]);
            $imported++;
        }

        $msg = "Berhasil import {$imported} dokumen.";
        if ($skipped > 0) {
            $msg .= " {$skipped} baris dilewati (judul kosong atau abstrak < 30 karakter).";
        }

        return redirect()->route('corpus.index')->with('ok', $msg);
    }

    /** Download template CSV. */
    public function template(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            // BOM agar Excel mendeteksi UTF-8 dengan benar
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['no', 'judul', 'abstrak', 'penulis', 'tahun', 'kategori']);
            fputcsv($out, [
                1,
                'Contoh Penelitian TF-IDF untuk Deteksi Plagiarisme',
                'Penelitian ini menerapkan algoritma TF-IDF dan cosine similarity untuk mendeteksi kemiripan antar abstrak skripsi dengan akurasi yang baik pada dataset 200 abstrak.',
                'Nama Penulis',
                2024,
                'NLP',
            ]);
            fputcsv($out, [
                2,
                'Contoh Klasifikasi Sentimen Naive Bayes',
                'Studi ini mengklasifikasikan ulasan produk e-commerce menjadi positif dan negatif menggunakan algoritma Naive Bayes dengan akurasi 85% pada 1000 data uji.',
                'Penulis Lain',
                2023,
                'Machine Learning',
            ]);
            fclose($out);
        }, 'corpus_template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** Baca CSV dengan auto-detect delimiter koma/titik-koma. */
    private function readCsv(string $path): array
    {
        $content = file_get_contents($path);
        // strip BOM
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        // Auto-detect delimiter di baris pertama
        $firstLine  = strtok($content, "\n");
        $delimiter  = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        $rows = [];
        $fh   = fopen('php://memory', 'r+');
        fwrite($fh, $content);
        rewind($fh);
        while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
            // skip baris benar-benar kosong
            if (count($row) === 1 && trim((string) $row[0]) === '') continue;
            $rows[] = $row;
        }
        fclose($fh);

        return $rows;
    }

    private function indexOf(array $header, array $candidates): ?int
    {
        foreach ($candidates as $c) {
            $i = array_search($c, $header, true);
            if ($i !== false) return $i;
        }
        return null;
    }
}
