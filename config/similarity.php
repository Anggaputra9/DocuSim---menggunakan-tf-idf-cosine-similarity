<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Python Binary
    |--------------------------------------------------------------------------
    |
    | Path / nama executable Python yang akan dipanggil oleh Laravel untuk
    | menjalankan script TF-IDF + Cosine Similarity. Pada Windows biasanya
    | "python", pada Linux/Mac sering "python3". Bisa juga path absolut ke
    | virtualenv, mis: "C:/python/venv/Scripts/python.exe".
    |
    */
    'python' => env('PYTHON_BIN', 'python'),

    /*
    |--------------------------------------------------------------------------
    | Default top-K
    |--------------------------------------------------------------------------
    */
    'top_k' => env('SIMILARITY_TOP_K', 5),
];
