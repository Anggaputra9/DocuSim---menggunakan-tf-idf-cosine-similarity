<?php

namespace App\Http\Controllers;

use App\Models\CorpusDocument;
use App\Models\SimilarityCheck;
use App\Services\SimilarityService;
use Illuminate\Http\Request;

class SimilarityController extends Controller
{
    public function __construct(protected SimilarityService $service) {}

    /** Halaman dashboard. */
    public function dashboard()
    {
        $stats = [
            'corpus_count' => CorpusDocument::count(),
            'check_count'  => SimilarityCheck::count(),
            'avg_score'    => round((float) SimilarityCheck::avg('highest_score') * 100, 2),
            'last_checks'  => SimilarityCheck::latest()->take(5)->get(),
        ];
        return view('dashboard', compact('stats'));
    }

    /** Form input abstrak. */
    public function create()
    {
        return view('similarity.create');
    }

    /** Proses pengecekan. */
    public function check(Request $request)
    {
        $data = $request->validate([
            'input_title'    => 'nullable|string|max:255',
            'input_abstract' => 'required|string|min:30',
            'top_k'          => 'nullable|integer|min:1|max:20',
        ]);

        $topK   = $data['top_k'] ?? config('similarity.top_k', 5);
        $result = $this->service->check($data['input_abstract'], (int) $topK);

        if (! ($result['ok'] ?? false)) {
            return back()
                ->withInput()
                ->withErrors(['input_abstract' => $result['error'] ?? 'Gagal memproses similarity.']);
        }

        $check = SimilarityCheck::create([
            'input_title'    => $data['input_title'] ?? null,
            'input_abstract' => $data['input_abstract'],
            'highest_score'  => $result['highest_score'] ?? 0,
            'results'        => $result['results'] ?? [],
            'top_terms'      => $result['top_terms'] ?? [],
        ]);

        return redirect()->route('similarity.show', $check);
    }

    /** Tampilkan hasil + visualisasi. */
    public function show(SimilarityCheck $similarity)
    {
        return view('similarity.show', ['check' => $similarity]);
    }

    /** Riwayat pengecekan. */
    public function history()
    {
        $checks = SimilarityCheck::latest()->paginate(10);
        return view('similarity.history', compact('checks'));
    }

    public function destroy(SimilarityCheck $similarity)
    {
        $similarity->delete();
        return redirect()->route('similarity.history')->with('ok', 'Riwayat dihapus.');
    }
}
