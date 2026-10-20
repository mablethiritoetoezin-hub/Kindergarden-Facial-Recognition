@extends('layouts.app')

@section('content') 

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Start New Chat</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('chats.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Select Parent</label>
                            <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror" required>
                                <option value="">Choose a parent...</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }} ({{ $parent->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Initial Message (Optional)</label>
                            <textarea name="message" id="message" rows="4" 
                                class="form-control @error('message') is-invalid @enderror" 
                                placeholder="Type your message here...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="photos" class="form-label">Photos (Optional)</label>
                            <input type="file" name="photos[]" id="photos" multiple accept="image/*" 
                                class="form-control @error('photos.*') is-invalid @enderror">
                            <small class="form-text text-muted">You can select multiple photos</small>
                            @error('photos.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('chats.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Start Chat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection