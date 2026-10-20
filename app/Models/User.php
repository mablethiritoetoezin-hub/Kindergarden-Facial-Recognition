<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
   

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'consent_given',
        'consent_given_at',
        'consent_form_id'
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'consent_given' => 'boolean',
            'consent_given_at' => 'datetime',
        ];
    }

    public function children()
    {
        return $this->hasMany(Child::class, 'parent_id');
    }

    public function linkedChildren()
    {
    return $this->belongsToMany(Child::class, 'child_parent', 'parent_id', 'child_id');
    }   

    public function uploadedPhotos()
    {
        return $this->hasMany(Photo::class, 'uploaded_by');
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function consentForm()
    {
        return $this->belongsTo(ConsentForm::class);
    }

    public function favoritePhotos()
    {
        return $this->belongsToMany(Photo::class, 'favorite_photos')->withTimestamps();
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
