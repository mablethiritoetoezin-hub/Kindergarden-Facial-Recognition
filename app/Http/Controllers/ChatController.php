<?php
// App/Http/Controllers/ChatController.php
namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $chats = Chat::with(['teacher', 'parent', 'latestMessage'])
            ->where(function($query) use ($user) {
                if ($user->role === 'teacher') {
                    $query->where('teacher_id', $user->id);
                } else {
                    $query->where('parent_id', $user->id);
                }
            })
            ->orderBy('last_message_at', 'desc')
            ->get();

        return view('chats.index', compact('chats'));
    }

    // Only teachers can access this
    public function startChat()
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Only teachers can start new chats.');
        }

        // Get all parent users
        $parents = User::where('role', 'parent')->get();
        
        return view('chats.start', compact('parents'));
    }

    // Only teachers can create new chats
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'teacher') {
            abort(403, 'Only teachers can start new chats.');
        }

        $request->validate([
            'parent_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Check if parent role is correct
        $parent = User::findOrFail($request->parent_id);
        if ($parent->role !== 'parent') {
            return back()->withErrors(['parent_id' => 'Selected user is not a parent.']);
        }

        // Check if chat already exists
        $existingChat = Chat::where('teacher_id', Auth::id())
            ->where('parent_id', $request->parent_id)
            ->first();

        if ($existingChat) {
            return redirect()->route('chats.show', $existingChat)
                ->with('info', 'Chat already exists with this parent.');
        }

        // Create new chat
        $chat = Chat::create([
            'teacher_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'title' => 'Chat with ' . $parent->name,
            'last_message_at' => now(),
        ]);

        // Send initial message if provided
        if ($request->message || $request->hasFile('photos')) {
            $this->createMessage($chat, $request);
        }

        return redirect()->route('chats.show', $chat)
            ->with('success', 'Chat started successfully!');
    }

    public function show(Chat $chat)
    {
        $user = Auth::user();
        
        // Check if user has access to this chat
        if ($chat->teacher_id !== $user->id && $chat->parent_id !== $user->id) {
            abort(403, 'You do not have access to this chat.');
        }

        // Load messages with sender
        $messages = $chat->messages()->with('sender')->get();
        
        // Mark messages as read for current user
        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $otherParticipant = $chat->getOtherParticipant($user->id);

        return view('chats.show', compact('chat', 'messages', 'otherParticipant'));
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $user = Auth::user();
        
        // Check if user has access to this chat
        if ($chat->teacher_id !== $user->id && $chat->parent_id !== $user->id) {
            abort(403, 'You do not have access to this chat.');
        }

        $request->validate([
            'message' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Must have either message or photos
        if (empty($request->message) && !$request->hasFile('photos')) {
            return back()->withErrors(['message' => 'Please provide a message or photos.']);
        }

        $this->createMessage($chat, $request);

        // Update chat's last message time
        $chat->update(['last_message_at' => now()]);

        return back()->with('success', 'Message sent successfully!');
    }

    public function deleteMessage(ChatMessage $message)
    {
        $user = Auth::user();
        
        // Check if user can delete this message
        // Only the sender or admin can delete messages
        if ($message->sender_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'You do not have permission to delete this message.');
        }

        // Also check if user has access to the chat
        $chat = $message->chat;
        if ($chat->teacher_id !== $user->id && $chat->parent_id !== $user->id && $user->role !== 'admin') {
            abort(403, 'You do not have access to this chat.');
        }

        // Delete associated photos from storage
        if ($message->hasPhotos()) {
            foreach ($message->photos as $photo) {
                if (Storage::disk('public')->exists($photo)) {
                    Storage::disk('public')->delete($photo);
                }
            }
        }

        // Delete the message
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully!');
    }

    private function createMessage(Chat $chat, Request $request)
    {
        $photos = [];
        
        // Handle photo uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('chat_photos', 'public');
                $photos[] = $path;
            }
        }
        
        ChatMessage::create([
            'chat_id' => $chat->id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
            'photos' => $photos,
            'sent_at' => now(),
        ]);
    }

    public function markAsRead(Chat $chat)
    {
        $user = Auth::user();
        
        if ($chat->teacher_id !== $user->id && $chat->parent_id !== $user->id) {
            abort(403);
        }

        $chat->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}