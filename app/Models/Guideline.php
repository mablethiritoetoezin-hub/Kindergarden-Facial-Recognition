<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guideline extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title', 
        'description',
        'content'
    ];

    protected $casts = [
        'content' => 'array'
    ];
}