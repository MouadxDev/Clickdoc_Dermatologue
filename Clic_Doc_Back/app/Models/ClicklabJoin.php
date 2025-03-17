<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clicklabJoin extends Model
{
    use HasFactory;
    protected $table = 'clilcklab_join';

    protected $fillable = [
        'consultation_id',
        'unique_key',
    ];
}
