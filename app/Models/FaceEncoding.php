<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FaceEncoding extends Model
{
    protected $fillable = ['child_id', 'encoding', 'photo_path'];
    
    public function child()
    {
        return $this->belongsTo(Child::class);
    }
    
    public function getEncodingAttribute($value)
    {
        return json_decode($value, true);
    }
    
    public function setEncodingAttribute($value)
    {
        $this->attributes['encoding'] = json_encode($value);
    }
}