@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!-- Chat Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Chat with {{ $otherParticipant->name }}</h4>
                    <a href="{{ route('chats.index') }}" class="btn btn-outline-secondary btn-sm">Back to Chats</a>
                </div>
            </div>

            <!-- Messages -->
            <div class="card mb-4" style="height: 500px;">
                <div class="card-body overflow-auto" id="messages-container">
                    @forelse($messages as $message)
                        <div class="mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}" id="message-{{ $message->id }}">
                            <div class="d-inline-block p-3 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                                 style="max-width: 70%; position: relative;">
                                
                                <!-- Delete Button (only for message sender or admin) -->
                                @if($message->sender_id === auth()->id() || auth()->user()->role === 'admin')
                                    <div class="position-absolute" style="top: 5px; right: 5px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete({{ $message->id }})" 
                                                title="Delete message">
                                            <i class="fas fa-trash" style="font-size: 12px;"></i>
                                        </button>
                                    </div>
                                @endif
                                
                                <!-- Message Text -->
                                @if($message->message)
                                    <div class="mb-2" style="margin-right: 30px;">{{ $message->message }}</div>
                                @endif
                                
                                <!-- Photos -->
                                @if($message->hasPhotos())
                                <div class="row g-2" style="margin-right: 30px;">
                                    @foreach($message->photos as $photoPath)
                                        @php
                                            $photoId = \App\Models\Photo::where('path', $photoPath)->value('id');
                                        @endphp
                                        <div class="col-6">
                                            <div class="position-relative">
                                                <img src="{{ Storage::url($photoPath) }}" 
                                                    class="img-fluid rounded" 
                                                    style="cursor: pointer;"
                                                    onclick="openImageModal('{{ Storage::url($photoPath) }}')">

                                                @if($photoId)
                                                    <div class="dropdown mt-1">
                                                        <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                            style="background-color: #001f3f; color: white; border-color: #001f3f;">
                                                            Options
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('photo.download', $photoId) }}" target="_blank">
                                                                    Download Photo
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                                
                                <!-- Message Info -->
                                <small class="d-block mt-2 {{ $message->sender_id === auth()->id() ? 'text-light' : 'text-muted' }}">
                                    {{ $message->sender->name }} • {{ $message->sent_at->format('M j, g:i A') }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">
                            <p>No messages yet. Start the conversation!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Message Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('chats.send', $chat) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <textarea name="message" rows="3" 
                                class="form-control @error('message') is-invalid @enderror" 
                                placeholder="Type your message...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(auth()->user()->role === 'teacher')
                            <div class="mb-3">
                                <label for="photos" class="form-label">Add Photos</label>
                                <input type="file" name="photos[]" id="photos" multiple accept="image/*" 
                                    class="form-control @error('photos.*') is-invalid @enderror">
                                <small class="form-text text-muted">You can select multiple photos</small>
                                @error('photos.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this message? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="deleteMessage()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for message deletion -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
let messageToDelete = null;

function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function confirmDelete(messageId) {
    messageToDelete = messageId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function deleteMessage() {
    if (messageToDelete) {
        const form = document.getElementById('deleteForm');
        form.action = `/messages/${messageToDelete}`;
        form.submit();
    }
}

// Auto scroll to bottom
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('messages-container');
    container.scrollTop = container.scrollHeight;
});

// Show success/error messages
@if(session('success'))
    alert('{{ session('success') }}');
@endif

@if(session('error'))
    alert('{{ session('error') }}');
@endif
</script>
@endsection