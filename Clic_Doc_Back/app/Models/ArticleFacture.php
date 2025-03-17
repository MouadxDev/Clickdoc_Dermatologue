<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleFacture extends Model
{
    use HasFactory;
    protected $table = 'article_factures';

    protected $fillable = [
        'facture_id',
        'libelle',
        'prix',
        'type',
        'created_at',
        'updated_at',
    ];
    
}
