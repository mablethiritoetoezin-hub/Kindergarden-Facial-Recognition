@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Conversations</h2>
                @if(auth()->user()->role === 'teacher')
                    <a href="{{ route('chats.start') }}" class="btn btn-primary">Start New Chat</a>
                @endif
            </div>

            @if($chats->isEmpty())
                <div class="text-center py-5">
                    <h4>No conversations yet</h4>
                    <p class="text-muted">
                        @if(auth()->user()->role === 'teacher')
                            Start a new conversation with a parent.
                        @else  
                            Wait for a teacher to start a conversation with you.
                        @endif
                    </p>
                    @if(auth()->user()->role === 'teacher')
                        <a href="{{ route('chats.start') }}" class="btn btn-primary">Start New Chat</a>
                    @endif
                </div>
            @else
                @foreach($chats as $chat)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">
                                        @if(auth()->user()->role === 'teacher')
                                            Chat with {{ $chat->parent->name }}
                                        @else
                                            Chat with {{ $chat->teacher->name }}
                                        @endif
                                    </h5>
                                    
                                    @if($chat->latestMessage)
                                        <p class="mb-1">
                                            <small class="text-muted">
                                                @if($chat->latestMessage->hasPhotos())
                                                    📷 {{ $chat->latestMessage->getPhotosCount() }} Photo(s)
                                                @endif
                                                @if($chat->latestMessage->message)
                                                    {{ Str::limit($chat->latestMessage->message, 50) }}
                                                @endif
                                            </small>
                                        </p>
                                        <small class="text-muted">{{ $chat->latestMessage->sent_at->diffForHumans() }}</small>
                                    @endif
                                </div>
                                
                                <div class="text-end">
                                    @php
                                        $unreadCount = $chat->unreadCount(auth()->id());
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="badge bg-primary rounded-pill mb-2">{{ $unreadCount }}</span>
                                    @endif
                                    <div>
                                        <a href="{{ route('chats.show', $chat) }}" class="btn btn-outline-primary btn-sm">
                                            Open Chat
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection