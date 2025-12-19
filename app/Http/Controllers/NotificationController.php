<?php

namespace App\Http\Controllers;

use App\Events\PlatformAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\RegisteredUser;

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

    public function create()
    {
        return view('admin.notifications');
    }

    public function storeAdminNotification(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:registered_user,email',
            'message' => 'required|string|max:1000'
        ]);

        $senderID = Auth::id();
        $targetUser = RegisteredUser::where('email', $validated['email'])->first();

        try{
            DB::transaction(function () use ($senderID, $targetUser, $validated) {
                $notificationId = DB::table('notification')->insertGetId([
                    'content' => $validated['message'],
                    'notification_type_id' => 1,
                    'registered_user_id' => $targetUser->id,
                    'issue_date' => now(),
                ]);

                if (!DB::table('administrator')->where('registered_user_id', $senderID)->exists()) {
                        throw new \Exception("Only Administrators can send Platform Alerts.");
                    }
                
                DB::table('notification_by_admin')->insert([
                    'notification_id' => $notificationId,
                    'admin_id' => $senderID
                ]);

                event(new PlatformAlert($validated['message'], $targetUser->id, $notificationId, 1));        
            });
            return back()->with('success', "Notification sent to {$targetUser->name} successfully!");
        } catch(\Exception $e) {
            \Log::error("Notification Error: ". $e->getMessage());
            return back()->with('error', 'Error: '.$e->getMessage());
        }        
    }

    public function getUserNotifications()
    {
        $userId = Auth::id();

        $notifications = Notification::where('registered_user_id', $userId)
            ->where('notification_type_id', '!=', 2)
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
