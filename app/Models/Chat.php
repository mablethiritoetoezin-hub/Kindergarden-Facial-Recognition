<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'parent_id',
        'title',
        'last_message_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('sent_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest('sent_at');
    }

    public function unreadCount($userId)
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    // Get the other participant (not the current user)
    public function getOtherParticipant($currentUserId)
    {
        if ($this->teacher_id == $currentUserId) {
            return $this->parent;
        }
        return $this->teacher;
    }

    public function consents()
    {
        return $this->hasMany(Consent::class);
    }

    public function approvedConsents()
    {
        return $this->hasMany(Consent::class)->where('status', 'approved');
    }

    public function hasPhotoConsent()
    {
        return $this->consents()
                    ->whereHas('consentForm', function($query) {
                        $query->where('type', 'photo')
                            ->where('is_active', true);
                    })
                    ->where('status', 'approved')
                    ->exists();
    }

    public function getConsentStatus($formType = 'photo')
    {
        $consent = $this->consents()
                    ->whereHas('consentForm', function($query) use ($formType) {
                        $query->where('type', $formType)
                                ->where('is_active', true);
                    })
                    ->first();
        
        return $consent ? $consent->status : 'no_form';
    }
}