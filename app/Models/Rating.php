<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'json_id',
        'article_id',
        'article_title',
        'type',
        'relevance',
        'clarity_and_coherence',
        'visualization_quality',
        'narrative_quality',
        'factual_correctness',
        'ip'
    ];
}
