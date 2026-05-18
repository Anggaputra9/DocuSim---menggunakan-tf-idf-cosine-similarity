<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimilarityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_title',
        'input_abstract',
        'highest_score',
        'results',
        'top_terms',
    ];

    protected $casts = [
        'results'   => 'array',
        'top_terms' => 'array',
    ];
}
