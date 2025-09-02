<?php

namespace App\Http\Controllers\Admin\Tickets;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Services\Firebase\FirebaseService;

class TicketController extends Controller
{
    protected FirebaseService $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function index()
    {
        return view('admin.tickets.index');
    }

    public function getData(Request $request)
    {
        $query = Ticket::with(['assignedTo', 'ticketCategory']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->whereHas('ticketCategory', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        $tickets = $query->get();

        return DataTables::of($tickets)
            ->addColumn('ticket_info', function ($ticket) {
                return '<div> <strong>' . $ticket->ticket_number . '</strong><br> <small class="text-muted">' . Str::limit($ticket->title, 30) . '</small> </div>';
            })
            ->addColumn('sender_info', function ($ticket) {
                return '<div> <strong>' . $ticket->sender_name . '</strong><br> <small class="text-muted">' . $ticket->sender_email . '</small> </div>';
            })
            ->addColumn('status_badge', function ($ticket) {
                $colors = [
                    'open' => 'success',
                    'pending' => 'warning',
                    'in_progress' => 'info',
                    'resolved' => 'primary',
                    'closed' => 'secondary'
                ];
                $color = $colors[$ticket->status] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst(str_replace('_', ' ', $ticket->status)) . '</span>';
            })
            ->addColumn('priority_badge', function ($ticket) {
                $colors = [
                    'low' => 'success',
                    'medium' => 'warning',
                    'high' => 'danger',
                    'urgent' => 'dark'
                ];
                $color = $colors[$ticket->priority] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst($ticket->priority) . '</span>';
            })
            ->addColumn('category', function ($ticket) {
                return $ticket->ticketCategory ? $ticket->ticketCategory->name : '<span class="text-muted">Uncategorized</span>';
            })
            ->addColumn('assigned_info', function ($ticket) {
                return $ticket->assignedTo ? $ticket->assignedTo->name : '<span class="text-muted">Unassigned</span>';
            })
            ->addColumn('created_at', function ($ticket) {
                return $ticket->created_at->format('d-m-Y H:i:s');
            })
            ->addColumn('actions', function ($ticket) {
                return '<div class="btn-group" role="group"> <a href="' . route('admin.tickets.show', $ticket->id) . '" class="btn btn-sm btn-info"> <i class="fas fa-eye"></i> View </a> <button type="button" class="btn btn-sm btn-danger" onclick="deleteTicket(' . $ticket->id . ')"> <i class="fas fa-trash"></i> Delete </button> </div>';
            })
            ->rawColumns(['ticket_info', 'sender_info', 'status_badge', 'priority_badge', 'category', 'assigned_info', 'created_at', 'actions'])
            ->make(true);
    }

    public function show($id)
    {
        $ticket = Ticket::with(['sender', 'assignedTo', 'replies.replier', 'ticketCategory'])->findOrFail($id);
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['admin', 'support']);
        })->get();
        $categories = TicketCategory::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.tickets.show', compact('ticket', 'users', 'categories'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,pending,in_progress,resolved,closed',
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldStatus = $ticket->status;
        $ticket->status = $request->status;

        if ($request->status === 'resolved') {
            $ticket->resolved_at = now();
        } elseif ($request->status === 'closed') {
            $ticket->closed_at = now();
        }

        $ticket->save();

        $this->firebaseService->updateTicketStatus($ticket->id, $request->status, auth()->user()->name);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'replier_type' => User::class,
            'replier_id' => auth()->id(),
            'message' => "Status changed from '{$oldStatus}' to '{$request->status}'",
            'is_internal' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully'
        ]);
    }

    public function assign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldAssignee = $ticket->assignedTo;
        $ticket->assigned_to = $request->assigned_to;
        $ticket->assigned_at = $request->assigned_to ? now() : null;
        $ticket->save();

        $message = $request->assigned_to
            ? "Ticket assigned to " . User::find($request->assigned_to)->name
            : "Ticket unassigned" . ($oldAssignee ? " from " . $oldAssignee->name : "");

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'replier_type' => User::class,
            'replier_id' => auth()->id(),
            'message' => $message,
            'is_internal' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket assignment updated successfully'
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'is_internal' => 'nullable|in:0,1,true,false',
        ]);

        $ticket = Ticket::findOrFail($id);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'replier_type' => User::class,
            'replier_id' => auth()->id(),
            'replier_name' => auth()->user()->name,
            'message' => $request->message,
            'is_internal' => $request->boolean('is_internal', false),
        ]);

        $this->firebaseService->storeTicketReply($ticket->id, [
            'id' => $reply->id,
            'replier_name' => auth()->user()->name,
            'replier_id' => auth()->id(),
            'message' => $request->message,
            'is_internal' => $request->boolean('is_internal', false),
            'created_at' => $reply->created_at->timestamp,
            'created_at_human' => $reply->created_at->diffForHumans(),
            'timestamp' => time()
        ]);

        if (!$request->boolean('is_internal', false)) {
            $this->sendTicketReplyNotificationToSender($ticket, $reply);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reply added successfully',
            'data' => [
                'replier_name' => auth()->user()->name,
                'reply_id' => $reply->id
            ]
        ]);
    }

    private function sendTicketReplyNotificationToSender($ticket, $reply)
    {
        try {
            if ($ticket->sender_type === 'App\Models\User' && $ticket->sender_id) {
                $sender = User::find($ticket->sender_id);
                if ($sender) {
                    $deviceTokens = $sender->deviceTokens()->pluck('token')->toArray();
                    if (!empty($deviceTokens)) {
                        $titleEn = "Reply on Your Ticket #{$ticket->ticket_number}";
                        $titleAr = "رد على تذكرتك #{$ticket->ticket_number}";
                        $bodyEn = "New reply from support: " . Str::limit($reply->message, 50);
                        $bodyAr = "رد جديد من الدعم: " . Str::limit($reply->message, 50);

                        $notification = Notification::create([
                            'user_id' => $sender->id,
                            'sender_id' => auth()->id(),
                            'title_en' => $titleEn,
                            'body_en' => $bodyEn,
                            'title_ar' => $titleAr,
                            'body_ar' => $bodyAr,
                        ]);

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
            }
            Log::info("Ticket reply notification sent to sender for ticket: {$ticket->id}");
        } catch (\Exception $e) {
            Log::error("Error sending ticket reply notification to sender: " . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting the ticket'
            ], 500);
        }
    }

    public function getStats()
    {
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'pending' => Ticket::where('status', 'pending')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
            'high_priority' => Ticket::where('priority', 'high')->count(),
            'urgent' => Ticket::where('priority', 'urgent')->count(),
            'unassigned' => Ticket::whereNull('assigned_to')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldPriority = $ticket->priority;
        $ticket->priority = $request->priority;
        $ticket->save();

        $this->firebaseService->updateTicketPriority($ticket->id, $request->priority, auth()->user()->name);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'replier_type' => User::class,
            'replier_id' => auth()->id(),
            'message' => "Priority changed from '{$oldPriority}' to '{$request->priority}'",
            'is_internal' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket priority updated successfully'
        ]);
    }


    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'nullable|exists:ticket_categories,id',
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldCategory = $ticket->ticketCategory;
        $ticket->ticket_category_id = $request->category_id;
        $ticket->save();

        $newCategory = $ticket->ticketCategory;
        $oldCategoryName = $oldCategory ? $oldCategory->name : 'Uncategorized';
        $newCategoryName = $newCategory ? $newCategory->name : 'Uncategorized';

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'replier_type' => User::class,
            'replier_id' => auth()->id(),
            'message' => "Category changed from '{$oldCategoryName}' to '{$newCategoryName}'",
            'is_internal' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket category updated successfully'
        ]);
    }
}