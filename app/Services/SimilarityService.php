<?php

namespace App\Services;

use App\Models\CorpusDocument;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * SimilarityService
 *
 * Jembatan antara Laravel <-> Python (TF-IDF + Cosine Similarity).
 *
 * Alur:
 *  1. Ambil semua corpus dari DB (Eloquent).
 *  2. Bentuk payload JSON {query, corpus, top_k}.
 *  3. Jalankan python script via Symfony Process, kirim JSON via stdin.
 *  4. Parse stdout (JSON) -> array hasil ranking + top terms.
 *
 * Keuntungan pendekatan ini:
 *  - Logika ML tetap di Python (memanfaatkan scikit-learn).
 *  - Laravel tetap mengelola data (CRUD corpus, history, user).
 *  - Tidak butuh REST API tambahan / Flask. Cukup proses lokal.
 */
class SimilarityService
{
    public function __construct(
        protected ?string $pythonBinary = null,
        protected ?string $scriptPath = null,
    ) {
        $this->pythonBinary = $pythonBinary ?? config('similarity.python', env('PYTHON_BIN', 'python'));
        $this->scriptPath   = $scriptPath ?? base_path('python/similarity.py');
    }

    /**
     * Cek similarity sebuah abstrak terhadap seluruh corpus.
     *
     * @param  string  $abstract  Abstrak yang ingin dicek.
     * @param  int     $topK      Jumlah dokumen mirip yang dikembalikan.
     * @return array{ok: bool, results?: array, top_terms?: array, highest_score?: float, error?: string}
     */
    public function check(string $abstract, int $topK = 5): array
    {
        $corpus = CorpusDocument::select('id', 'title', 'author', 'year', 'category', 'abstract')
            ->get()
            ->toArray();

        if (empty($corpus)) {
            return [
                'ok'    => false,
                'error' => 'Corpus kosong. Tambahkan dokumen referensi dulu.',
            ];
        }

        $payload = json_encode([
            'query'  => $abstract,
            'corpus' => $corpus,
            'top_k'  => $topK,
        ], JSON_UNESCAPED_UNICODE);

        $process = new Process([$this->pythonBinary, $this->scriptPath]);
        $process->setInput($payload);
        $process->setTimeout(120);

        try {
            $process->run();
        } catch (ProcessFailedException $e) {
            Log::error('Python process failed', ['error' => $e->getMessage()]);
            return ['ok' => false, 'error' => 'Gagal menjalankan Python: ' . $e->getMessage()];
        }

        if (! $process->isSuccessful()) {
            return [
                'ok'    => false,
                'error' => 'Python error: ' . trim($process->getErrorOutput() ?: $process->getOutput()),
            ];
        }

        $output  = trim($process->getOutput());
        $decoded = json_decode($output, true);

        if (! is_array($decoded)) {
            return [
                'ok'    => false,
                'error' => 'Output Python tidak valid JSON. Output: ' . substr($output, 0, 500),
            ];
        }

        return $decoded;
    }
}
