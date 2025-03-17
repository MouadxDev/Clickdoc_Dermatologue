<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordonnance extends Model
{
    use HasFactory;

    // Allow mass assignment for these fields
    protected $fillable = [
        'consultation_id',
        'medicament_id',
        'commentaire',
        'administration_mode',
        'duration_value',
        'duration_unit',
        'frequency',
        'contraindications',
        'matin',
        'midi',
        'soir',
        'au_coucher',
    ];
}
