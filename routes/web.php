<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TrainingPhotoController;
use App\Http\Controllers\FeedbackController;
use Illuminate\Support\Facades\Auth;


// Home Route: Redirects authenticated users based on role or shows welcome page
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'teacher' => redirect()->route('teacher.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('home'),
        };
    }
    return view('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {

    Route::get('/photos/download/{photo}', [PhotoController::class, 'download'])->name('photo.download');
    Route::get('/parent/favorites', [ParentController::class, 'favorites'])->name('parent.favorites');
    Route::post('/photos/{photo}/favorite', [PhotoController::class, 'toggleFavorite'])->name('photo.toggleFavorite');
    Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.submit');
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.form');




    Route::prefix('admin')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/feedback', [AdminController::class, 'viewFeedback'])->name('admin.feedback.index');

        Route::prefix('users')->group(function () {

            Route::get('/', [AdminController::class, 'manageUsers'])->name('admin.users.index');
            Route::post('/', [AdminController::class, 'storeUser'])->name('admin.users.store');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
            Route::delete('/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

            Route::get('/child-parent-links', [AdminController::class, 'manageChildParentLinks'])->name('admin.child_parent_links');
            Route::post('/child-parent-links/attach', [AdminController::class, 'attachParentToChild'])->name('admin.attach_parent');
            Route::post('/child-parent-links/detach', [AdminController::class, 'detachParentFromChild'])->name('admin.detach_parent');
            Route::post('/admin/users/child-parent-links/attach', [AdminController::class, 'attachParentToChild'])->name('admin.attach_parent');
            Route::post('/admin/users/child-parent-links/detach', [AdminController::class, 'detachParentFromChild'])->name('admin.detach_parent');
        });
        
        Route::prefix('classrooms')->group(function () {
            Route::get('/', [AdminController::class, 'manageClassrooms'])->name('admin.classrooms.index');
            Route::post('/', [AdminController::class, 'storeClassroom'])->name('admin.classrooms.store');
            Route::get('/{classroom}/edit', [AdminController::class, 'editClassroom'])->name('admin.classrooms.edit');
            Route::put('/{classroom}', [AdminController::class, 'updateClassroom'])->name('admin.classrooms.update');
            Route::delete('/{classroom}', [AdminController::class, 'destroyClassroom'])->name('admin.classrooms.destroy');
        });


        Route::delete('/photos/{photo}', [AdminController::class, 'deletePhoto'])->name('admin.photos.destroy');

        Route::prefix('guidelines')->group(function () {
            Route::get('/edit', [AdminController::class, 'editGuidelines'])->name('admin.guidelines.edit');
            Route::put('/', [AdminController::class, 'updateGuidelines'])->name('admin.guidelines.update');
        });
    });

    Route::prefix('teacher')->group(function () {

        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');
        Route::get('/profile', [TeacherController::class, 'showProfile'])->name('teacher.profile');
        Route::post('/profile/update', [TeacherController::class, 'updateProfile'])->name('teacher.update_profile');
        Route::get('/classroom', [TeacherController::class, 'showClassroom'])->name('teacher.classroom');
        Route::get('/classroom/{id}', [TeacherController::class, 'classroomDetails'])->name('teacher.classroom_details');

        

        Route::prefix('training')->group(function () {
            Route::post('/photo/{childId}', [TrainingPhotoController::class, 'store'])->name('teacher.training.photo.store');
            Route::post('/train', [TrainingPhotoController::class, 'train'])->name('teacher.training.train');
        });

       
    });

    Route::get('/parent/dashboard', [ParentController::class, 'dashboard'])->name('parent.dashboard');

});

Route::middleware(['auth'])->group(function () {
    Route::prefix('chats')->group(function () {
        
        Route::get('/', [ChatController::class, 'index'])->name('chats.index');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('chats.show');
        Route::post('/{chat}/message', [ChatController::class, 'sendMessage'])->name('chats.send');
        Route::post('/{chat}/mark-read', [ChatController::class, 'markAsRead'])->name('chats.mark_read');
        
        // Teacher-only routes
        Route::middleware(['role:teacher'])->group(function () {
            Route::get('/start/new', [ChatController::class, 'startChat'])->name('chats.start');
            Route::post('/create', [ChatController::class, 'store'])->name('chats.store');
        });
    }); 

    Route::delete('/messages/{message}', [ChatController::class, 'deleteMessage'])->name('messages.delete');
    
});

Route::middleware(['auth'])->group(function () {
    Route::get('/upload', [PhotoController::class, 'showUploadForm'])->name('upload');
   Route::post('/upload', [PhotoController::class, 'upload'])->name('photo.upload');
});



require __DIR__.'/auth.php';