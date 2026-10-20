@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Upload Photos</h1>
        
        <!-- Display success message -->
        

        <!-- Display warnings -->
        @if(session('warnings'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Some issues occurred:</strong>
                <ul class="mb-0 mt-2">
                    @foreach(session('warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Display validation errors -->
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('photo.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="child_id" class="form-label">Select Child (Optional)</label>
                <select name="child_id" id="child_id" class="form-select">
                    <option value="">Choose a child or leave blank for AI detection</option>
                    @foreach (\App\Models\Child::all() as $child)
                        <option value="{{ $child->id }}" {{ old('child_id') == $child->id ? 'selected' : '' }}>
                            {{ $child->name }}
                        </option>
                    @endforeach
                </select>
                @error('child_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    If you select a child, all photos will be assigned to this child. 
                    Leave blank to use AI detection for each photo individually.
                </div>
            </div>

            <div class="mb-3">
                <label for="photos" class="form-label">Select Photos</label>
                <input type="file" name="photos[]" id="photos" class="form-control" 
                       accept="image/*" multiple required>
                @error('photos')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                @error('photos.*')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    You can select multiple photos. Maximum file size: 10MB per photo.
                </div>
            </div>

            <div class="mb-3">
                <label for="caption" class="form-label">Caption (Optional)</label>
                <textarea name="caption" id="caption" class="form-control" rows="3" 
                          placeholder="Add a caption for the photos (optional)">{{ old('caption') }}</textarea>
                @error('caption')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
                <div class="form-text">
                    If no caption is provided, a default message will be sent to parents.
                </div>
            </div>

            <!-- Photo preview area -->
            <div class="mb-3">
                <div id="photo-preview" class="row g-2" style="display: none;">
                    <div class="col-12">
                        <label class="form-label">Photo Preview:</label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="upload-btn">
                <span class="spinner-border spinner-border-sm d-none" id="upload-spinner"></span>
                Upload Photos
            </button>
        </form>
    </div>

    <script>
        document.getElementById('photos').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('photo-preview');
            const files = e.target.files;
            
            // Clear previous previews
            const existingPreviews = previewContainer.querySelectorAll('.photo-preview-item');
            existingPreviews.forEach(item => item.remove());
            
            if (files.length > 0) {
                previewContainer.style.display = 'block';
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'col-md-3 col-sm-4 col-6 photo-preview-item';
                            previewDiv.innerHTML = `
                                <div class="card">
                                    <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <small class="text-muted">Photo ${index + 1}</small>
                                    </div>
                                </div>
                            `;
                            previewContainer.appendChild(previewDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewContainer.style.display = 'none';
            }
        });

        // Show loading spinner on form submit
        document.querySelector('form').addEventListener('submit', function() {
            const uploadBtn = document.getElementById('upload-btn');
            const spinner = document.getElementById('upload-spinner');
            
            uploadBtn.disabled = true;
            spinner.classList.remove('d-none');
            uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        });
    </script>
@endsection