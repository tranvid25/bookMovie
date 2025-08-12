<?php

namespace App\Http\Controllers\Api;

use App\Events\MessagePosted;
use App\Events\MessageSent;
use App\Events\UserOnline;
use App\Events\VideoSignalEvent;
use App\Events\VoiceCallEvent;
use App\Http\Controllers\Controller;
use App\Models\Chatroom;
use App\Models\Message;
use App\Models\MessagePrivate;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

class ChatController extends Controller
{
    // Lấy lịch sử chat, trả về kèm user
      public function index()
    {
        $messages = Message::with('user')->orderBy('id')->get();
        return response()->json([
            'messages' => $messages
        ]);
    }

    // Gửi tin nhắn, lưu userId, message, broadcast event
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        $fileUrl = null;
        $fileName = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $fileDirectory = 'images/message/';
            $file->move(public_path($fileDirectory), $fileName);
            $fileUrl = url($fileDirectory . $fileName);
        }
        // Cho phép gửi tin nhắn chỉ có file hoặc chỉ có text hoặc cả hai
        if (!$request->filled('message') && !$fileUrl) {
            return response()->json(['error' => 'Message or file is required'], 422);
        }
        $msg = Message::create([
            'userId' => $user->id,
            'message' => $request->message ?? '',
            'file_path' => $fileUrl
        ]);
        // Broadcast event UserOnline (chuẩn BE FE)
        broadcast(new \App\Events\UserOnline($user, $msg->message, $fileUrl))->toOthers();
        return response()->json($msg);
    }

    // Gửi tin nhắn private
    public function ChatPrivate(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Validate request
        if (!$request->filled('content') && !$request->hasFile('file')) {
            return response()->json(['error' => 'Message or file is required'], 422);
        }

        $message = new MessagePrivate();
        $message->room_id = $request->input('room_id', -1);
        $message->user_id = $user->id;
        $message->content = $request->input('content', '');

        // Xử lý file nếu có
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::random(12) . '.' . $file->getClientOriginalExtension();
            $fileDirectory = 'images/chat/file/';
            $file->move(public_path($fileDirectory), $fileName);
            $message->file_url = url($fileDirectory . $fileName);
            $message->file_goc=$file->getClientOriginalName();
        }

        $message->save();

        // Broadcast event
        broadcast(new MessagePosted($message->load('user')))->toOthers();

        return response()->json(['message' => $message->load('user')]);
    }

    // Lấy tin nhắn private theo room_id
    public function getPrivateMessages($roomId)
    {
        $user = request()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $messages = MessagePrivate::with('user')
            ->where('room_id', $roomId)
            ->orderBy('created_at')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    // Lấy danh sách phòng chat
    public function getRooms()
    {
        try {
            $user = request()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $rooms = Chatroom::orderBy('created_at', 'desc')->get();

            return response()->json(['rooms' => $rooms]);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    // Tạo phòng chat mới
    public function createRoom(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $room = Chatroom::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['room' => $room]);
    }
    public function voiceCall(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'room_id' => 'required|integer',
            'signal' => 'required',
            'type' => 'required|in:offer,answer,candidate'
        ]);

        $data = [
            'user' => $user,
            'room_id' => $request->room_id,
            'signal' => $request->signal,
            'type' => $request->type
        ];

        broadcast(new VoiceCallEvent($data))->toOthers();

        return response()->json(['success' => true]);
    }
}
