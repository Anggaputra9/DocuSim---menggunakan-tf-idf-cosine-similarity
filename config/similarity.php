<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Pilih engine perhitungan similarity:
    |  - "php"    : TF-IDF + Cosine native PHP (default, tanpa dependency).
    |  - "python" : panggil python/similarity.py (scikit-learn).
    |
    */
    'driver' => env('SIMILARITY_DRIVER', 'php'),

    /*
    |--------------------------------------------------------------------------
    | Python Binary (hanya dipakai jika driver = python)
    |--------------------------------------------------------------------------
    |
    | Path / nama executable Python. Pada Windows biasanya "python", pada
    | Linux/Mac sering "python3". Bisa juga path absolut ke virtualenv,
    | mis: "C:/python/venv/Scripts/python.exe".
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
