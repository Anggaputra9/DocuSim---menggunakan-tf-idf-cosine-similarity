<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorpusDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'year',
        'category',
        'abstract',
    ];
}
