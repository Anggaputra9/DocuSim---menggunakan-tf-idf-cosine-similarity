<?php

namespace App\Services;

use App\Models\CorpusDocument;

/**
 * TF-IDF + Cosine Similarity native PHP.
 *
 * Implementasi ini ekuivalen secara matematis dengan
 * scikit-learn TfidfVectorizer (smooth IDF) + cosine_similarity.
 *
 * Rumus:
 *   tf(t,d)    = jumlah kemunculan term t di doc d
 *   idf(t)     = ln((1 + N) / (1 + df(t))) + 1     // smooth IDF (sklearn default)
 *   tfidf(t,d) = tf(t,d) * idf(t)
 *   norm(d)    = vector tfidf yang dinormalisasi (l2)
 *   cosine(a,b)= dot(norm(a), norm(b))
 *
 * Tidak butuh Python.
 */
class TfIdfPhpService
{
    /** Stopword Bahasa Indonesia (subset Sastrawi). */
    private const STOPWORDS = [
        'yang','untuk','pada','ke','para','namun','menurut','antara','dia','dua','ia',
        'seperti','jika','sehingga','kembali','dan','tidak','ini','karena','kepada','oleh',
        'saat','harus','sementara','setelah','belum','kami','sekitar','bagi','serta','di',
        'dari','telah','sebagai','masih','hal','ketika','adalah','itu','dalam','bisa',
        'bahwa','atau','hanya','kita','dengan','akan','juga','ada','mereka','sudah','saya',
        'terhadap','secara','agar','lain','anda','begitu','mengapa','kenapa','yaitu','yakni',
        'daripada','itulah','lagi','maka','tentang','demi','dimana','kemana','pula','sambil',
        'sebelum','sesudah','supaya','guna','kah','pun','sampai','sedangkan','selagi',
        'tetapi','apakah','kecuali','sebab','selain','seolah','seraya','seterusnya','tanpa',
        'agak','boleh','dapat','dsb','dst','dll','tsb','tersebut','saling','saja','satu',
        'tiap','tidaklah','sangat','lebih','sebuah','suatu','adanya','lalu','sekali','pun',
    ];

    public function check(string $abstract, int $topK = 5): array
    {
        $corpus = CorpusDocument::select('id', 'title', 'author', 'year', 'category', 'abstract')
            ->get()
            ->toArray();

        if (empty($corpus)) {
            return ['ok' => false, 'error' => 'Corpus kosong. Tambahkan dokumen referensi dulu.'];
        }

        // 1. Preprocessing: tokenize semua dokumen + query
        $queryTokens = $this->tokenize($abstract);
        $docsTokens  = array_map(fn ($d) => $this->tokenize($d['abstract'] ?? ''), $corpus);

        // 2. Bangun vocabulary dari query + corpus (vocab konsisten)
        $allTokens = array_merge([$queryTokens], $docsTokens);
        $df        = [];
        foreach ($allTokens as $tokens) {
            foreach (array_unique($tokens) as $t) {
                $df[$t] = ($df[$t] ?? 0) + 1;
            }
        }
        $vocab = array_keys($df);
        $N     = count($allTokens);

        // 3. IDF (smooth) — formula sklearn default
        $idf = [];
        foreach ($vocab as $t) {
            $idf[$t] = log((1 + $N) / (1 + $df[$t])) + 1.0;
        }

        // 4. Bangun vector TF-IDF tiap dokumen, lalu l2-normalize
        $queryVec = $this->tfidfVector($queryTokens, $vocab, $idf);
        $docVecs  = array_map(fn ($tokens) => $this->tfidfVector($tokens, $vocab, $idf), $docsTokens);

        // 5. Cosine similarity (karena sudah dinormalisasi: dot product)
        $scores = [];
        foreach ($docVecs as $i => $v) {
            $scores[$i] = $this->dot($queryVec, $v);
        }

        // 6. Ranking
        arsort($scores);
        $topIdx  = array_slice(array_keys($scores), 0, $topK, true);
        $results = [];
        foreach ($topIdx as $i) {
            $doc = $corpus[$i];
            $s   = round($scores[$i], 4);
            $results[] = [
                'id'            => $doc['id'],
                'title'         => $doc['title'] ?? '',
                'author'        => $doc['author'] ?? null,
                'year'          => $doc['year'] ?? null,
                'category'      => $doc['category'] ?? null,
                'abstract'      => $doc['abstract'] ?? '',
                'score'         => $s,
                'score_percent' => round($s * 100, 2),
            ];
        }

        // 7. Top terms dari query (untuk visualisasi)
        $topTerms = $this->topTermsFromVector($queryVec, 10);

        return [
            'ok'            => true,
            'results'       => $results,
            'top_terms'     => $topTerms,
            'highest_score' => $results[0]['score'] ?? 0,
        ];
    }

    /** Tokenize: lowercase, hapus non-alfabet, stopword removal, min length 3. */
    private function tokenize(string $text): array
    {
        $text   = mb_strtolower($text);
        $text   = preg_replace('/[^a-z\s]/', ' ', $text);
        $text   = preg_replace('/\s+/', ' ', trim($text));
        $tokens = $text === '' ? [] : explode(' ', $text);

        $stopwords = array_flip(self::STOPWORDS);
        $unigrams  = [];
        foreach ($tokens as $t) {
            if (strlen($t) > 2 && ! isset($stopwords[$t])) {
                $unigrams[] = $t;
            }
        }

        // Tambahkan bigram seperti TfidfVectorizer ngram_range=(1,2)
        $bigrams = [];
        for ($i = 0; $i < count($unigrams) - 1; $i++) {
            $bigrams[] = $unigrams[$i] . ' ' . $unigrams[$i + 1];
        }

        return array_merge($unigrams, $bigrams);
    }

    /** Hitung vector TF-IDF dan l2-normalize. */
    private function tfidfVector(array $tokens, array $vocab, array $idf): array
    {
        $tf = array_count_values($tokens);
        $vec = [];
        foreach ($vocab as $t) {
            if (isset($tf[$t])) {
                $vec[$t] = $tf[$t] * $idf[$t];
            }
        }

        // l2 normalize
        $norm = 0.0;
        foreach ($vec as $v) $norm += $v * $v;
        $norm = sqrt($norm);
        if ($norm > 0) {
            foreach ($vec as $t => $v) $vec[$t] = $v / $norm;
        }
        return $vec;
    }

    /** Dot product 2 sparse vector (assoc array). */
    private function dot(array $a, array $b): float
    {
        $sum = 0.0;
        // Iterate vector lebih kecil
        if (count($a) > count($b)) [$a, $b] = [$b, $a];
        foreach ($a as $t => $v) {
            if (isset($b[$t])) $sum += $v * $b[$t];
        }
        return $sum;
    }

    /** Ambil top-N term dengan bobot tertinggi dari sebuah vector tfidf. */
    private function topTermsFromVector(array $vec, int $n): array
    {
        arsort($vec);
        $out = [];
        foreach (array_slice($vec, 0, $n, true) as $term => $weight) {
            if ($weight <= 0) break;
            $out[] = ['term' => $term, 'weight' => round($weight, 4)];
        }
        return $out;
    }
}
