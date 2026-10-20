@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Admin Dashboard')

@section('content')

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4>Welcome, {{ Auth::user()->name }}!</h4>
            <p>Manage users, classrooms, photos, and review facial recognition guidelines below.</p>
        </div>
    </div>

    <!-- Facial Recognition Guidelines -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Facial Recognition Guidelines</h5>
            <a href="{{ route('admin.guidelines.edit') }}" class="btn btn-light btn-sm">
                <i class="fas fa-edit"></i> Edit Guidelines
            </a>
        </div>
        <div class="card-body">
            @if($guidelines)
                <h6>{{ $guidelines->title }}</h6>
                <p>{{ $guidelines->description }}</p>
                <ul class="list-group list-group-flush">
                    @foreach($guidelines->content as $item)
                        <li class="list-group-item">
                            <strong>{{ $item['title'] }}:</strong> {{ $item['description'] }}
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="alert alert-info">
                    <h6>No guidelines found</h6>
                    <p>Click "Edit Guidelines" to add facial recognition guidelines.</p>
                </div>
            @endif
            <p class="mt-3">For questions or concerns about these guidelines, please contact the school administration.</p>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">User Management</h5>
    </div>
    <div class="card-body">
        <!-- Create New User Form -->
        <div class="mb-4">
            <h5>Create New User</h5>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <select name="role" class="form-select @error('role') is-invalid @enderror" id="roleSelect" required>
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                            <option value="parent" {{ old('role') == 'parent' ? 'selected' : '' }}>Parent</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <select name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" 
                                id="classroomSelect" style="display: {{ old('role') == 'teacher' ? 'block' : 'none' }};">
                            <option value="">Assign to Classroom (Teachers Only)</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                    {{ $classroom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('classroom_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100">Create User</button>
                    </div>
                </div>
            </form>
        </div>



            <!-- Users List -->
        @if ($users->isEmpty())
            <p class="text-muted">No users found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>
                                    <!-- Fixed: This now links to the edit page -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Classroom Management Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Classroom Management</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.classrooms.store') }}" method="POST" class="mb-4">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="name" class="form-control" placeholder="Classroom Name" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="age_group" class="form-control" placeholder="Age Group (e.g., 3-5)" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="room_number" class="form-control" placeholder="Room Number" required>
                    </div>
                    <div class="col-md-3">
                        <select name="teacher_id" class="form-select" required>
                            <option value="">Assign Teacher</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">Create Classroom</button>
                    </div>
                </div>
            </form>

            @if($classrooms->isEmpty())
                <p class="text-muted">No classrooms found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age Group</th>
                                <th>Room</th>
                                <th>Teacher</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($classrooms as $classroom)
                                <tr>
                                    <td>{{ $classroom->name }}</td>
                                    <td>{{ $classroom->age_group }}</td>
                                    <td>{{ $classroom->room_number }}</td>
                                    <td>
                                        {{ $classroom->teacher->name ?? 'Unassigned' }}
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#editClassroomRow{{ $classroom->id }}">Modify</a>
                                        <form action="{{ route('admin.classrooms.destroy', $classroom->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this classroom?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="collapse" id="editClassroomRow{{ $classroom->id }}">
                                <td colspan="5">
                                    <form action="{{ route('admin.classrooms.update', $classroom->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <input type="text" name="name" class="form-control" value="{{ $classroom->name }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="age_group" class="form-control" value="{{ $classroom->age_group }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" name="room_number" class="form-control" value="{{ $classroom->room_number }}" required>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="teacher_id" class="form-select">
                                                    <option value="">Unassigned</option>
                                                    @foreach($teachers as $t)
                                                        <option value="{{ $t->id }}" {{ $classroom->teacher_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <button type="submit" class="btn btn-success w-100">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Child-Parent Relationship Management -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Manage Child-Parent Relationships</h5>
    </div>
    <div class="card-body">
        <!-- List children and their parents -->
        <h6>Children and Their Linked Parents</h6>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Child Name</th>
                        <th>Parents</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($children as $child)
                    <tr>
                        <td>{{ $child->name }}</td>
                        <td>
                            @if($child->parents->isEmpty())
                                <em>No linked parents</em>
                            @else
                                @foreach($child->parents as $parent)
                                    <div>
                                        {{ $parent->name }} ({{ $parent->email }})
                                        <form method="POST" action="{{ route('admin.detach_parent') }}" class="d-inline ms-2">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                                            <input type="hidden" name="child_id" value="{{ $child->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Unlink this parent?')">Unlink</button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif
                        </td>
                        <td><!-- You could add bulk actions here if needed --></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Form to Link Parent to Child -->
        <h6>Link Parent to Child</h6>
        <form method="POST" action="{{ route('admin.attach_parent') }}">
            @csrf
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <label for="parent_id" class="form-label">Select Parent</label>
                    <select name="parent_id" id="parent_id" class="form-select" required>
                        <option value="">-- Choose Parent --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="child_id" class="form-label">Select Child</label>
                    <select name="child_id" id="child_id" class="form-select" required>
                        <option value="">-- Choose Child --</option>
                        @foreach($children as $child)
                            <option value="{{ $child->id }}">{{ $child->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Link</button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- Photo Management -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Photo Management</h5>
        </div>
        <div class="card-body">
            @if ($photos->isEmpty())
                <p class="text-muted">No photos found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Child</th>
                                <th>Uploaded By</th>
                                <th>Photo</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($photos as $photo)
                                <tr>
                                    <td>{{ $photo->child->name }}</td>
                                    <td>{{ $photo->uploader->name }} ({{ $photo->uploader->role }})</td>
                                    <td>
                                        <img src="{{ asset('storage/' . $photo->path) }}" alt="Photo" style="max-width: 100px; height: auto;">
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.photos.destroy', $photo->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this photo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show classroom dropdown only for teachers
        const roleSelect = document.getElementById('roleSelect');
        const classroomSelect = document.getElementById('classroomSelect');

        roleSelect.addEventListener('change', function() {
            classroomSelect.style.display = (this.value === 'teacher') ? 'block' : 'none';
        });
    });
</script>
@endsection