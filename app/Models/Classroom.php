<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'teacher_id',
        'age_group',
        'room_number',
        'capacity',
        'description',
    ];

    /**
     * Get the teacher that owns the classroom.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get the children for the classroom.
     */
    public function children()
    {
        return $this->hasMany(Child::class);
    }

    /**
     * Get the schedules for the classroom.
     */
    public function schedules()
    {
        return $this->hasMany(ClassroomSchedule::class);
    }

    /**
     * Get the photos for the classroom.
     */
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}   