<?php

namespace App\Http\Controllers\Notification;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\Notification;
use App\Models\EmailNotfiction;
use App\Models\EmailUserNotfiction;
use App\Services\FirebaseNotificationService;
use App\Notifications\SystemWideNotification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\AudiLogsService;
use App\Http\Resources\Shared\NotificationResource;

class NotificationController extends Controller
{

    public function sendToAll(Request $request, FirebaseNotificationService $firebaseService)
    {
        $request->validate([
            'title_ar' => 'required|string',
            'body_ar' => 'required|string',
            'title_en' => 'required|string',
            'body_en' => 'required|string',
        ]);

        $deviceTokens = DeviceToken::all();
        if ($deviceTokens->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No device tokens found.']);
        }

        $tokens = $deviceTokens->pluck('token')->toArray();
        $auth = Auth::user();
        $senderRoles = $auth->roles->pluck('name')->toArray();
        $image = Notification::getRoleImage($senderRoles);

        foreach ($deviceTokens as $deviceToken) {
            $notification = Notification::create([
                'user_id' => $deviceToken->user_id,
                'sender_id' => 3,
                'title_en' => $request->title_en,
                'body_en' => $request->body_en,
                'title_ar' => $request->title_ar,
                'body_ar' => $request->body_ar,
                'image' => $image,
            ]);
        }

        $result = $firebaseService->sendToTokens($tokens, $request->title_ar, $request->body_ar, $request->title_en, $request->body_en, $notification->id);
        return response()->json($result);
    }
    public function sendToAllUserInEmail(Request $request)
    {
        $title = $request->title;
        $body = $request->body;
        $users = User::all();
        $email = EmailNotfiction::create([
            'title' => 'Notification Title',
            'body' => 'Notification Body',
        ]);
        foreach ($users as $user) {
            EmailUserNotfiction::created([
                "user_id" => $user->id,
                "email_id" => $email->id,

            ]);
            $user->notify(new SystemWideNotification($email->id, $title, $body));
        }
    }

    public function getAllNotification(Request $request, $page = 1)
    {
        $perPage = 10;

        $notifications = Auth::user()->notifications()
            ->with(['user', 'sender.roles'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully.',
            'data' => NotificationResource::collection($notifications),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'has_more' => $notifications->hasMorePages(),
            ]
        ]);
    }

    public function sendToUser(Request $request, FirebaseNotificationService $firebaseService)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'title_ar' => 'required|string',
            'body_ar' => 'required|string',
            'title_en' => 'required|string',
            'body_en' => 'required|string',
        ]);

        $deviceTokens = DeviceToken::where('user_id', $request->user_id)->pluck('token')->toArray();
        if (empty($deviceTokens)) {
            return response()->json(['success' => false, 'message' => 'No device tokens found for this user.']);
        }

        $auth = Auth::user();
        $senderRoles = $auth->roles->pluck('name')->toArray();
        $image = Notification::getRoleImage($senderRoles);

        $notification = Notification::create([
            'user_id' => $request->user_id,
            'sender_id' => 3,
            'title_en' => $request->title_en,
            'body_en' => $request->body_en,
            'title_ar' => $request->title_ar,
            'body_ar' => $request->body_ar,
            'image' => $image,
        ]);

        $result = $firebaseService->sendToTokens($deviceTokens, $request->title_ar, $request->body_ar, $request->title_en, $request->body_en, $notification->id);
        return response()->json($result);
    }

    public function readNotficion(Request $request)
    {
        $notification = Notification::find($request->id);
        if ($notification && $notification->user_id == Auth::id()) {
            $notification->read = 1;
            $notification->save();
        }
        $countUnreadMessages = Notification::countUnreadMessages(Auth::id());
        return response()->json([
            "countUnreadMessages" => $countUnreadMessages,
        ]);
    }

    public function sendToUsersFirebase(Request $request, FirebaseNotificationService $firebaseService)
    {
        $request->validate([
            'title_ar' => 'required|string',
            'body_ar' => 'required|string',
            'title_en' => 'required|string',
            'body_en' => 'required|string',
            'users' => 'required|array',
        ]);

        $userIds = $request->input('users', []);
        if (empty($userIds)) {
            $deviceTokens = DeviceToken::all();
        } else {
            $deviceTokens = DeviceToken::whereIn('user_id', $userIds)->get();
        }

        if ($deviceTokens->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No device tokens found.']);
        }

        $tokens = $deviceTokens->pluck('token')->toArray();
        $auth = Auth::user();
        $senderRoles = $auth->roles->pluck('name')->toArray();
        $image = Notification::getRoleImage($senderRoles);

        $notifiedUsers = [];

        foreach ($deviceTokens as $deviceToken) {
            if (!in_array($deviceToken->user_id, $notifiedUsers)) {
                $notifiedUsers[] = $deviceToken->user_id;

                Notification::create([
                    'user_id' => $deviceToken->user_id,
                    'sender_id' => Auth::id() ?? 3,
                    'title_en' => $request->title_en,
                    'body_en' => $request->body_en,
                    'title_ar' => $request->title_ar,
                    'body_ar' => $request->body_ar,
                    'image' => $image,
                ]);
            }
        }

        AudiLogsService::storeLog(
            'create',
            'notifications',
            null,
            null,
            [
                'type' => 'firebase',
                'title_ar' => $request->title_ar,
                'body_ar' => $request->body_ar,
                'title_en' => $request->title_en,
                'body_en' => $request->body_en,
                'user_ids' => $userIds,
            ]
        );

        $notification = $deviceTokens->last() ? Notification::latest()->first() : null;
        $result = $firebaseService->sendToTokens($tokens, $request->title_ar, $request->body_ar, $request->title_en, $request->body_en, $notification?->id);

        return response()->json($result);
    }


    public function sendToUsersEmail(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
            'users' => 'required|array',

        ]);
        $title = $request->title;
        $body = $request->body;
        $userIds = $request->input('users', []);
        $users = empty($userIds) ? User::all() : User::whereIn('id', $userIds)->get();
        $email = EmailNotfiction::create([
            'title' => $title,
            'body' => $body,
        ]);
        foreach ($users as $user) {
            EmailUserNotfiction::create([
                "user_id" => $user->id,
                "email_id" => $email->id,
            ]);
            $user->notify(new SystemWideNotification($email->id, $title, $body));
        }
        // Audit log
        AudiLogsService::storeLog(
            'create',
            'notifications',
            null,
            null,
            [
                'type' => 'email',
                'title' => $title,
                'body' => $body,
                'user_ids' => $userIds,
            ]
        );
        return response()->json(['success' => true, 'message' => 'Sending emails is working!']);
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.notfiction.index', ['users' => $users]);
    }

    public function getPaginatedNotifications(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        $lang = session('notificationLang', 'ar');

        $notifications = Auth::user()->notifications()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $formattedNotifications = $notifications->map(function ($notification) use ($lang) {
            $title = $lang == 'ar' ? $notification->title_ar : $notification->title_en;
            $body = $lang == 'ar' ? $notification->body_ar : $notification->body_en;

            return [
                'id' => $notification->id,
                'title' => $title,
                'body' => $body,
                'sender_name' => $notification->sender->name ?? 'System',
                'read' => $notification->read,
                'created_at_human' => $notification->created_at->diffForHumans(),
                'created_at' => $notification->created_at->toISOString(),
            ];
        });

        return response()->json([
            'success' => true,
            'notifications' => $formattedNotifications,
            'current_page' => $notifications->currentPage(),
            'last_page' => $notifications->lastPage(),
            'per_page' => $notifications->perPage(),
            'total' => $notifications->total(),
            'has_more' => $notifications->hasMorePages(),
        ]);
    }

}
