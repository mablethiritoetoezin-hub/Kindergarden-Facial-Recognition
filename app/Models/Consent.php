<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;

    protected $fillable = [
        'consent_form_id',
        'child_id',
        'parent_id',
        'status',
        'consented_at',
        'notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'consented_at' => 'datetime',
    ];

    /**
     * Get the consent form
     */
    public function consentForm()
    {
        return $this->belongsTo(ConsentForm::class);
    }

    /**
     * Get the child
     */
    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * Get the parent who gave consent
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Check if consent is approved
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if consent is declined
     */
    public function isDeclined()
    {
        return $this->status === 'declined';
    }

    /**
     * Check if consent is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}