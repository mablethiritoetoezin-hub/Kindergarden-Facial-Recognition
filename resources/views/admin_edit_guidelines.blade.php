{{-- resources/views/admin_edit_guidelines.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Guidelines')
@section('header', 'Edit Facial Recognition Guidelines')

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Facial Recognition Guidelines</h5>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.guidelines.update') }}" method="POST" id="guidelinesForm">
                @csrf
                @method('PUT')
                
                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label">Guidelines Title</label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $guidelines->title ?? 'Guidelines for Using Facial Recognition in Compliance with Privacy Laws') }}"
                           required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" 
                              name="description" 
                              rows="3" 
                              required>{{ old('description', $guidelines->description ?? 'The use of facial recognition technology in our application is designed to enhance security and streamline photo management. Below are the guidelines to ensure compliance with privacy laws and protect the privacy of all users, including students, parents, and staff.') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Guidelines Items -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label">Guidelines</label>
                        <button type="button" class="btn btn-success btn-sm" onclick="addGuideline()">
                            <i class="fas fa-plus"></i> Add Guideline
                        </button>
                    </div>
                    
                    <div id="guidelines-container">
                        @if($guidelines && $guidelines->content)
                            @foreach($guidelines->content as $index => $item)
                                <div class="guideline-item border rounded p-3 mb-3" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">Guideline {{ $index + 1 }}</h6>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeGuideline(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Title</label>
                                        <input type="text" 
                                               class="form-control @error('guidelines.'.$index.'.title') is-invalid @enderror" 
                                               name="guidelines[{{ $index }}][title]" 
                                               value="{{ old('guidelines.'.$index.'.title', $item['title']) }}"
                                               required>
                                        @error('guidelines.'.$index.'.title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control @error('guidelines.'.$index.'.description') is-invalid @enderror" 
                                                  name="guidelines[{{ $index }}][description]" 
                                                  rows="2" 
                                                  required>{{ old('guidelines.'.$index.'.description', $item['description']) }}</textarea>
                                        @error('guidelines.'.$index.'.description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Default guidelines if none exist --}}
                            <div class="guideline-item border rounded p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">Guideline 1</h6>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeGuideline(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="guidelines[0][title]" value="Consent" required>
                                </div>
                                
                                <div class="mb-0">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="guidelines[0][description]" rows="2" required>Obtain explicit, written consent from parents or guardians before using facial recognition for any child's photos. Consent forms must clearly explain how the technology is used and stored.</textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Guidelines
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let guidelineIndex = {{ $guidelines && $guidelines->content ? count($guidelines->content) : 1 }};

        function addGuideline() {
            const container = document.getElementById('guidelines-container');
            const newGuideline = document.createElement('div');
            newGuideline.className = 'guideline-item border rounded p-3 mb-3';
            newGuideline.setAttribute('data-index', guidelineIndex);
            
            newGuideline.innerHTML = `
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="mb-0">Guideline ${guidelineIndex + 1}</h6>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeGuideline(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="mb-2">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="guidelines[${guidelineIndex}][title]" required>
                </div>
                
                <div class="mb-0">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="guidelines[${guidelineIndex}][description]" rows="2" required></textarea>
                </div>
            `;
            
            container.appendChild(newGuideline);
            guidelineIndex++;
            updateGuidelineNumbers();
        }

        function removeGuideline(button) {
            const container = document.getElementById('guidelines-container');
            if (container.children.length > 1) {
                button.closest('.guideline-item').remove();
                updateGuidelineNumbers();
            } else {
                alert('You must have at least one guideline.');
            }
        }

        function updateGuidelineNumbers() {
            const guidelines = document.querySelectorAll('.guideline-item');
            guidelines.forEach((guideline, index) => {
                const title = guideline.querySelector('h6');
                title.textContent = `Guideline ${index + 1}`;
            });
        }
    </script>

@endsection