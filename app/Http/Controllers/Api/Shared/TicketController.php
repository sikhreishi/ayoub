<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketCategory;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Requests\Api\CreateTicketRequest;
use App\Http\Requests\Api\ReplyTicketRequest;
use App\Services\Firebase\FirebaseService;
use App\Services\Firebase\FirebaseNotificationService;

class TicketController extends Controller
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Get user's tickets
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $tickets = Ticket::where('sender_type', get_class($user))
            ->where('sender_id', $user->id)
            ->with([
                'replies' => function ($query) {
                    $query->latest()->limit(1);
                },
                'ticketCategory'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    /**
     * Create new ticket
     */
    public function store(CreateTicketRequest $request)
    {
        $user = Auth::user();

        $ticket = Ticket::create([
            'ticket_number' => $this->generateTicketNumber(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'ticket_category_id' => $request->category,
            'sender_type' => get_class($user),
            'sender_id' => $user->id,
            'sender_name' => $user->name,
            'sender_email' => $user->email,
            'sender_phone' => $user->phone,
            'status' => 'open'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    /**
     * Show specific ticket
     */
    public function show($id)
    {
        $user = Auth::user();

        $ticket = Ticket::where('sender_type', get_class($user))
            ->where('sender_id', $user->id)
            ->where('id', $id)
            ->with([
                'replies' => function ($query) {
                    $query->where('is_internal', false)->orderBy('created_at', 'asc');
                },
                'ticketCategory'
            ])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    /**
     * Reply to ticket
     */
    public function reply(ReplyTicketRequest $request, $id)
    {
        $user = Auth::user();

        $ticket = Ticket::where('sender_type', get_class($user))
            ->where('sender_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($ticket->status === 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot reply to closed ticket'
            ], 400);
        }

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'replier_type' => get_class($user),
            'replier_id' => $user->id,
            'replier_name' => $user->name,
            'replier_email' => $user->email,
            'message' => $request->message,
            'is_internal' => false
        ]);
        // Store in Firebase for real-time updates
        $this->firebaseService->storeTicketReply($ticket->id, [
            'id' => $reply->id,
            'replier_name' => $user->name,
            'replier_id' => $user->id,
            'message' => $request->message,
            'is_internal' => false,
            'created_at' => $reply->created_at->timestamp,
            'created_at_human' => $reply->created_at->diffForHumans(),
            'timestamp' => time()
        ]);

        // Send notification to admin users
        $this->sendTicketReplyNotificationToAdmins($ticket, $reply, $user);

        // Update ticket status to pending if it was resolved
        if ($ticket->status === 'resolved') {
            $ticket->update(['status' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data' => $reply
        ], 201);
    }

    /**
     * Send notification to admin users when user replies
     */
    private function sendTicketReplyNotificationToAdmins($ticket, $reply, $sender)
    {
        try {
            // Get admin and support users
            $adminUsers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'support']);
            })->get();

            foreach ($adminUsers as $admin) {
                // Get admin's device tokens
                $deviceTokens = $admin->deviceTokens()->pluck('token')->toArray();

                if (!empty($deviceTokens)) {
                    $titleEn = "New Reply on Ticket #{$ticket->ticket_number}";
                    $titleAr = "رد جديد على التذكرة #{$ticket->ticket_number}";
                    $bodyEn = "New message from {$sender->name}: " . Str::limit($reply->message, 50);
                    $bodyAr = "رسالة جديدة من {$sender->name}: " . Str::limit($reply->message, 50);

                    // Create notification record
                    $notification = Notification::create([
                        'user_id' => $admin->id,
                        'sender_id' => $sender->id,
                        'title_en' => $titleEn,
                        'body_en' => $bodyEn,
                        'title_ar' => $titleAr,
                        'body_ar' => $bodyAr,
                    ]);

                    // Send Firebase notification if service exists
                    if (class_exists('App\Services\Firebase\FirebaseNotificationService')) {
                        $firebaseNotificationService = app('App\Services\Firebase\FirebaseNotificationService');
                        $firebaseNotificationService->sendToTokens(
                            $deviceTokens,
                            $titleAr,
                            $bodyAr,
                            $titleEn,
                            $bodyEn,
                            $notification->id
                        );
                    } else {
                        // Fallback to basic Firebase messaging
                        foreach ($deviceTokens as $token) {
                            $this->firebaseService->sendNotification(
                                $token,
                                $titleEn,
                                $bodyEn,
                                [
                                    'type' => 'ticket_reply',
                                    'ticket_id' => $ticket->id,
                                    'notification_id' => $notification->id
                                ]
                            );
                        }
                    }
                }
            }

            Log::info("Ticket reply notifications sent to admins for ticket: {$ticket->id}");
        } catch (\Exception $e) {
            Log::error("Error sending ticket reply notification to admins: " . $e->getMessage());
        }
    }

    /**
     * Get ticket categories
     */
    public function categories()
    {
        $categories = TicketCategory::where('is_active', true)
            ->select('id', 'name','name_en','name_ar', 'slug', 'description', 'color')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get ticket statistics
     */
    public function stats()
    {
        $user = Auth::user();

        $stats = [
            'total' => Ticket::where('sender_type', get_class($user))
                ->where('sender_id', $user->id)->count(),
            'open' => Ticket::where('sender_type', get_class($user))
                ->where('sender_id', $user->id)
                ->where('status', 'open')->count(),
            'pending' => Ticket::where('sender_type', get_class($user))
                ->where('sender_id', $user->id)
                ->where('status', 'pending')->count(),
            'in_progress' => Ticket::where('sender_type', get_class($user))
                ->where('sender_id', $user->id)
                ->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('sender_type', get_class($user))
                ->where('sender_id', $user->id)
                ->where('status', 'resolved')->count(),
            'closed' => Ticket::where('sender_type', get_class($user))
                ->where('sender_id', $user->id)
                ->where('status', 'closed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Generate unique ticket number
     */
    private function generateTicketNumber()
    {
        do {
            $number = 'TKT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (Ticket::where('ticket_number', $number)->exists());

        return $number;
    }
}

