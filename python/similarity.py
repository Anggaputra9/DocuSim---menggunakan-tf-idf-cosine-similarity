"""
similarity.py
TF-IDF + Cosine Similarity untuk dokumen abstrak.

Cara pakai (dipanggil oleh Laravel via Symfony Process):
    echo {JSON} | python similarity.py

Format input JSON (lewat stdin):
{
    "query": "abstrak yang ingin dicek ...",
    "corpus": [
        {"id": 1, "title": "...", "abstract": "..."},
        {"id": 2, "title": "...", "abstract": "..."}
    ],
    "top_k": 5
}

Format output JSON (lewat stdout):
{
    "ok": true,
    "results": [
        {"id": 1, "title": "...", "score": 0.83},
        ...
    ],
    "top_terms": [
        {"term": "machine", "weight": 0.45},
        ...
    ]
}

Pre-processing sederhana untuk Bahasa Indonesia:
- lowercase
- buang tanda baca & angka
- buang stopword Bahasa Indonesia (list manual)
"""

import sys
import json
import re
import traceback

try:
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.metrics.pairwise import cosine_similarity
    import numpy as np
except ImportError as e:
    print(json.dumps({
        "ok": False,
        "error": f"Dependency tidak ditemukan: {e}. Jalankan: pip install scikit-learn numpy"
    }))
    sys.exit(1)


# Stopword Bahasa Indonesia (subset dari Sastrawi). Bisa diperluas.
STOPWORDS_ID = {
    "yang", "untuk", "pada", "ke", "para", "namun", "menurut", "antara",
    "dia", "dua", "ia", "seperti", "jika", "jika", "sehingga", "kembali",
    "dan", "tidak", "ini", "karena", "kepada", "oleh", "saat", "harus",
    "sementara", "setelah", "belum", "kami", "sekitar", "bagi", "serta",
    "di", "dari", "telah", "sebagai", "masih", "hal", "ketika", "adalah",
    "itu", "dalam", "bisa", "bahwa", "atau", "hanya", "kita", "dengan",
    "akan", "juga", "ada", "mereka", "sudah", "saya", "terhadap", "secara",
    "agar", "lain", "anda", "begitu", "mengapa", "kenapa", "yaitu", "yakni",
    "daripada", "itulah", "lagi", "maka", "tentang", "demi", "dimana",
    "kemana", "pula", "sambil", "sebelum", "sesudah", "supaya", "guna",
    "kah", "pun", "sampai", "sedangkan", "selagi", "sehingga", "tetapi",
    "apakah", "kecuali", "sebab", "selain", "seolah", "seraya", "seterusnya",
    "tanpa", "agak", "boleh", "dapat", "dsb", "dst", "dll", "tsb", "tersebut",
    "saling", "sambil", "saja", "satu", "tiap", "tidaklah", "sangat", "lebih"
}


def preprocess(text: str) -> str:
    """Lowercase, hapus non-alfabet, hapus stopword."""
    text = text.lower()
    text = re.sub(r"[^a-z\s]", " ", text)
    text = re.sub(r"\s+", " ", text).strip()
    tokens = [t for t in text.split() if t not in STOPWORDS_ID and len(t) > 2]
    return " ".join(tokens)


def main():
    try:
        raw = sys.stdin.read()
        payload = json.loads(raw)

        query = payload.get("query", "").strip()
        corpus = payload.get("corpus", [])
        top_k = int(payload.get("top_k", 5))

        if not query:
            print(json.dumps({"ok": False, "error": "Query kosong."}))
            return
        if not corpus:
            print(json.dumps({"ok": False, "error": "Corpus kosong."}))
            return

        # Preprocess
        query_clean = preprocess(query)
        docs_clean = [preprocess(d.get("abstract", "")) for d in corpus]

        # Bentuk matrix TF-IDF: query digabung dengan corpus supaya vocab konsisten
        all_texts = [query_clean] + docs_clean
        vectorizer = TfidfVectorizer(
            ngram_range=(1, 2),
            min_df=1,
            max_df=0.95,
            sublinear_tf=True,
        )
        tfidf_matrix = vectorizer.fit_transform(all_texts)

        query_vec = tfidf_matrix[0]
        corpus_vec = tfidf_matrix[1:]

        # Hitung cosine similarity
        scores = cosine_similarity(query_vec, corpus_vec).flatten()

        # Ranking
        ranked_idx = np.argsort(-scores)
        results = []
        for idx in ranked_idx[:top_k]:
            doc = corpus[idx]
            results.append({
                "id": doc.get("id"),
                "title": doc.get("title", ""),
                "author": doc.get("author"),
                "year": doc.get("year"),
                "category": doc.get("category"),
                "abstract": doc.get("abstract", ""),
                "score": round(float(scores[idx]), 4),
                "score_percent": round(float(scores[idx]) * 100, 2),
            })

        # Top terms dari query (untuk visualisasi)
        feature_names = vectorizer.get_feature_names_out()
        query_array = query_vec.toarray().flatten()
        top_term_idx = np.argsort(-query_array)[:10]
        top_terms = [
            {"term": feature_names[i], "weight": round(float(query_array[i]), 4)}
            for i in top_term_idx if query_array[i] > 0
        ]

        print(json.dumps({
            "ok": True,
            "results": results,
            "top_terms": top_terms,
            "highest_score": results[0]["score"] if results else 0,
        }, ensure_ascii=False))

    except Exception as e:
        print(json.dumps({
            "ok": False,
            "error": str(e),
            "trace": traceback.format_exc(),
        }))


if __name__ == "__main__":
    main()
