<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    protected $table = 'stock';

    // Specify the fillable attributes to allow mass assignment
    protected $fillable = ['name', 'stock', 'expiration_date'];

    // Optionally, define the timestamps if they are used
    public $timestamps = true; 
}
