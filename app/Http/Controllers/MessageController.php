<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Events\PlatformAlert;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:registered_user,id',
            'content' => 'required|string|max:5000',
        ]);

        $message = null;

        try {
            DB::beginTransaction();

            $message = Message::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $request->receiver_id,
                'content' => $request->content,
            ]);

            DB::commit();

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return back()->with('error', 'You are not allowed to message this user.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while sending the message.');
        }
        try{
            $senderName = Auth::user()->name;
            $preview = substr($request->content, 0, 30) . (strlen($request->content) > 30 ? '...' : '');
            $notifContent = "New message from {$senderName}: \"{$preview}\"";
            $notifId = DB::table('notification')->insertGetId([
                'content' => $notifContent,
                'notification_type_id' => 2,
                'registered_user_id' => $request->receiver_id,
                'issue_date' => now(),
            ]);

            DB::table('message_notification')->insert([
                'message_id' => $message->id,
                'notification_id' => $notifId
            ]);

            event(new PlatformAlert($notifContent, $request->receiver_id, $notifId, 2));
        } catch (\Eception $e) {}
        return back()->with('success', 'Message sent successfully.');
    }

    public function index($userId = null)
    {
        $authId = auth()->id();

        if($userId && $userId != $authId){
            Message::where('sender_id', $userId)
                ->where('receiver_id', $authId)
                ->whereNull('date_read')
                ->update(['date_read' => now()]);
        }

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
        } else {
            $userId = null;
        }
        
        return view('messages.messages', compact('conversations', 'messages', 'userId'));
    }

    public function messagesJson($userId)
    {
        $authId = auth()->id();

        $messages = Message::where(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $authId);
        })->orderBy('date_sent')->get();

        $messages->load('sender');

       return response()->json([
            'messages' => $messages->map(fn($m) => [
                'id' => $m->id,
                'content' => $m->content,
                'date_sent' => $m->date_sent,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender?->name ?? 'Deleted User'
            ])
        ]);
    }

    public function conversationsJson()
    {
        $authId = auth()->id();

        $conversations = Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->get()
            ->groupBy(function($msg) use ($authId) {
                return $msg->sender_id == $authId ? $msg->receiver_id : $msg->sender_id;
            });

        $data = [];
        foreach ($conversations as $otherUserId => $msgs) {
            $otherUser = \App\Models\RegisteredUser::find($otherUserId);
            $hasUnread = $msgs->where('receiver_id', $authId)->whereNull('date_read')->isNotEmpty();
            $data[] = [
                'otherUserId' => $otherUserId,
                'name' => $otherUser ? $otherUser->name : 'Deleted User',
                'count' => $msgs->count(),
                'hasUnread' => $hasUnread
            ];
        }

        return response()->json($data);
    }
}
