<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'message',
        'photos',
        'is_read',
        'sent_at'
    ];

    protected $casts = [
        'photos' => 'array',
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function hasPhotos()
    {
        return !empty($this->photos);
    }

    public function getPhotosCount()
    {
        return is_array($this->photos) ? count($this->photos) : 0;
    }
}