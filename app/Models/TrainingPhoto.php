<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrainingPhoto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'training_photos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['child_id', 'photo_path'];

    /**
     * Get the child that the training photo belongs to.
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the full server path to the training photo.
     *
     * @return string
     */
    public function getFullPathAttribute()
    {
        return storage_path('app/' . $this->photo_path);
    }

    /**
     * Get the URL to the training photo.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->photo_path);
    }
}