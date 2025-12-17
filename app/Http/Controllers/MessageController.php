<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:registered_user,id',
            'content' => 'required|string|max:5000',
        ]);

        try {
            Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'content' => $request->content,
            ]);

            return back()->with('success', 'Message sent successfully.');
        } catch (\Illuminate\Database\QueryException $e) { 
            return back()->with('error', 'You are not allowed to message this user.');
        }
    }

    public function index($userId = null)
    {
        $authId = auth()->id();
        $userId = $userId ?? $authId;

        $conversations = Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->orderBy('date_sent', 'desc')
            ->get()
            ->groupBy(function($msg) use ($authId) {
                return $msg->sender_id == $authId ? $msg->receiver_id : $msg->sender_id;
            });

        $messages = collect();

        if ($userId) {
            $messages = Message::where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)
                ->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)
                ->where('receiver_id', $authId);
            })->orderBy('date_sent')->get();
        }
        return view('messages.messages', compact('conversations', 'messages', 'userId'));
    }
}
