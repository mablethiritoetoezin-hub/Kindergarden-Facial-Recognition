<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['child_id', 'path', 'uploaded_by'];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorite_photos')->withTimestamps();
    }
}
