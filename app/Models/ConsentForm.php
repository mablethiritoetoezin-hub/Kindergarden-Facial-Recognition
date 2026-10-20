<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the active consent form
     * 
     * @return ConsentForm|null
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}