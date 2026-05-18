<?php

namespace App\Services;

use App\Models\CorpusDocument;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * SimilarityService
 *
 * Multi-driver service untuk dokumen similarity. Driver yang didukung:
 *
 *  - "php"    (default): implementasi TF-IDF + Cosine native PHP.
 *               Tidak butuh dependency eksternal. Hasilnya ekuivalen
 *               dengan sklearn TfidfVectorizer (smooth IDF) + cosine.
 *
 *  - "python": memanggil python/similarity.py via stdin/stdout JSON.
 *               Pakai jika ingin model lebih advanced (IndoBERT, dll).
 *
 * Pilih driver via env: SIMILARITY_DRIVER=php|python
 */
class SimilarityService
{
    public function __construct(
        protected ?TfIdfPhpService $php = null,
    ) {
        $this->php = $php ?? new TfIdfPhpService();
    }

    /**
     * @return array{ok: bool, results?: array, top_terms?: array, highest_score?: float, error?: string}
     */
    public function check(string $abstract, int $topK = 5): array
    {
        $driver = strtolower((string) config('similarity.driver', env('SIMILARITY_DRIVER', 'php')));

        return match ($driver) {
            'python' => $this->checkWithPython($abstract, $topK),
            default  => $this->php->check($abstract, $topK),
        };
    }

    /** Driver Python (TF-IDF scikit-learn). */
    protected function checkWithPython(string $abstract, int $topK): array
    {
        $corpus = CorpusDocument::select('id', 'title', 'author', 'year', 'category', 'abstract')
            ->get()
            ->toArray();

        if (empty($corpus)) {
            return ['ok' => false, 'error' => 'Corpus kosong. Tambahkan dokumen referensi dulu.'];
        }

        $payload = json_encode([
            'query'  => $abstract,
            'corpus' => $corpus,
            'top_k'  => $topK,
        ], JSON_UNESCAPED_UNICODE);

        $pythonBinary = config('similarity.python', env('PYTHON_BIN', 'python'));
        $scriptPath   = base_path('python/similarity.py');

        $process = new Process([$pythonBinary, $scriptPath]);
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
