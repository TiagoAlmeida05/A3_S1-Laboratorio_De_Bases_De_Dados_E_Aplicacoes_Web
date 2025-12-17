<?php

namespace App\Http\Controllers;

use App\Events\PlatformAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $notifications = Notification::where('registered_user_id', $userId)
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('pages.notifications',[
            'notifications' => $notifications
        ]);
    }

    public function sendGeneralNotification(Request $request)
    {
        $senderID = Auth::id();
        $targetUserId = $request->input('target_user_id');
        $notificationTypeId = $request->input('notification_type_id');
        $messageContent = $request->input('message_content');

        if(!$senderID){
            return response()->json(['status' => 'Error: Sender must be logged in.'], 401);
        }
        if(!$targetUserId || !$notificationTypeId || !$messageContent){
            return response()->json(['status' => 'Error: Missing required notification parameters.'], 400);
        }

        try{
            DB::transaction(function () use ($senderID, $targetUserId, $notificationTypeId, $messageContent) {
                $notificationId = DB::table('notification')->insertGetId([
                    'content' => $messageContent,
                    'notification_type_id' => $notificationTypeId,
                    'registered_user_id' => $targetUserId,
                    'issue_date' => now(),
                ]);

                if($notificationTypeId == 1) {
                    if(!DB::table('administrator')->where('registered_user_id', $senderID)->exists()){
                        throw new \Exception("Notification Type 1 (PlatformAlert) requires an administrator sender.");
                    }
                    DB::table('notification_by_admin')->insert([
                        'notification_id' => $notificationId,
                        'admin_id' => $senderID
                    ]);
                }
                event(new PlatformAlert($messageContent, $targetUserId, $notificationId));        
            });
            return response()->json(['status' => "Notification Type {$notificationTypeId} broadcasted to User {$targetUserId}"]);    
        
        } catch(\Exception $e) {
            \Log::error("Notification Error: ". $e->getMessage());
            return response()->json(['status' => 'Error: ' . $e->getMessage()], 500);
        }        
    }

    public function getUserNotifications()
    {
        $userId = Auth::id();

        $notifications = Notification::where('registered_user_id', $userId)
            ->orderBy('issue_date', 'desc')
            ->take(10)
            ->get();
        
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        Notification::where('id', $id)
            ->where('registered_user_id', Auth::id()) 
            ->update(['read_date' => now()]);

        return response()->json(['status' => 'success']);
    }

    public function markAllAsRead()
    {
        Notification::where('registered_user_id', Auth::id())
            ->whereNull('read_date')
            ->update(['read_date' => now()]);

        return response() ->json(['status' => 'success']);
    }
}
