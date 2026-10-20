<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'age',
        'gender',
        'parent_id',
        'classroom_id',
        'medical_info',
        'notes',
    ];

    /**
     * Get the parent of the child.
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }


    public function parents()
    {
        return $this->belongsToMany(User::class, 'child_parent', 'child_id', 'parent_id');
    }

    /**
     * Get the classroom of the child.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get the photos of the child.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}