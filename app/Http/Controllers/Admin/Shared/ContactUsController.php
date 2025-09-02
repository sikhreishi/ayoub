<?php

namespace App\Http\Controllers\Admin\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Yajra\DataTables\DataTables;
use App\Services\AudiLogsService;

class ContactUsController extends Controller
{
    /**
     * Display user contacts page
     */
    public function index()
    {
        return view('admin.contacts.users');
    }

    /**
     * Display driver contacts page
     */
    public function driverPage()
    {
        return view('admin.contacts.drivers');
    }

    /**
     * Display all contacts page
     */
    public function allContacts()
    {
        return view('admin.contacts.all');
    }

    /**
     * Get user contacts data for DataTable (only users with 'user' role)
     */
    public function getUserContacts(Request $request)
    {
        $contacts = ContactMessage::with(['user' => function ($query) {
            $query->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'user');
            });
        }])->whereHas('user.roles', function ($query) {
            $query->where('name', 'user');
        })->get();

        return DataTables::of($contacts)
            ->addColumn('user_name', function ($contact) {
                return $contact->user ? $contact->user->name : $contact->name;
            })
            ->addColumn('user_role', function ($contact) {
                return $contact->user ? 'User' : 'Guest';
            })
            ->addColumn('actions', function ($contact) {
                return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-info" onclick="viewContact(' . $contact->id . ')">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteContact(' . $contact->id . ')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Get driver contacts data for DataTable (only users with 'driver' role)
     */
    public function getDriverContacts(Request $request)
    {
        $contacts = ContactMessage::with(['user' => function ($query) {
            $query->whereHas('roles', function ($roleQuery) {
                $roleQuery->where('name', 'driver');
            });
        }])->whereHas('user.roles', function ($query) {
            $query->where('name', 'driver');
        })->get();

        return DataTables::of($contacts)
            ->addColumn('driver_name', function ($contact) {
                return $contact->user ? $contact->user->name : $contact->name;
            })
            ->addColumn('driver_role', function ($contact) {
                return $contact->user ? 'Driver' : 'Guest';
            })
            ->addColumn('actions', function ($contact) {
                return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-info" onclick="viewContact(' . $contact->id . ')">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteContact(' . $contact->id . ')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Display specific contact details
     */
    public function show($id)
    {
        $contact = ContactMessage::with('user.roles')->findOrFail($id);

        $role = null;
        if ($contact->user && $contact->user->roles->count() > 0) {
            $role = $contact->user->roles->pluck('name')->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'message' => $contact->message,
                'created_at' => $contact->created_at,
                'user' => $contact->user,
                'role' => $role,
            ]
        ]);
    }


    public function destroy($id)
    {
        try {
            $contact = ContactMessage::findOrFail($id);
            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact message deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred while deleting the message'
            ], 500);
        }
    }


    public function getStats()
    {
        $stats = [
            'total' => ContactMessage::count(),
            'user_messages' => ContactMessage::whereHas('user.roles', function ($query) {
                $query->where('name', 'user');
            })->count(),
            'driver_messages' => ContactMessage::whereHas('user.roles', function ($query) {
                $query->where('name', 'driver');
            })->count(),
            'guest_messages' => ContactMessage::whereNull('user_id')->count(),
            'recent' => ContactMessage::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }


    public function getAllContacts(Request $request)
    {
        $contacts = ContactMessage::with(['user' => function ($query) {
            $query->with('roles');
        }])->get();

        return DataTables::of($contacts)
            ->addColumn('sender_name', function ($contact) {
                return $contact->user ? $contact->user->name : $contact->name;
            })
            ->addColumn('sender_role', function ($contact) {
                if (!$contact->user) {
                    return 'Guest';
                }

                $role = $contact->user->roles->first();
                return $role ? ucfirst($role->name) : 'No Role';
            })
            ->addColumn('actions', function ($contact) {
                return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-info" onclick="viewContact(' . $contact->id . ')">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteContact(' . $contact->id . ')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
