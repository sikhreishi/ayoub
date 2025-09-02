<?php

namespace App\Http\Controllers\Admin\Profile;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Services\AudiLogsService;


class ContactController extends Controller
{
    // Contacts from registered users with role 'user'
    public function getUserContacts(Request $request)
    {
        $query = ContactMessage::whereHas('user.roles', function ($q) {
            $q->where('name', 'user');
        })->with('user');

        return DataTables::of($query)
            ->addColumn('user_name', fn($contact) => $contact->user ? $contact->user->name : 'N/A')
            ->addColumn('email', fn($contact) => $contact->email)
            ->addColumn('phone', fn($contact) => $contact->phone)
            ->addColumn('message', fn($contact) => $contact->message)
            ->addColumn('actions', function ($contact) {
                return '<button class="btn btn-sm btn-danger delete-item" data-id="' . $contact->id . '" data-url="' . route('admin.contacts.destroy', $contact->id) . '" data-table="#user-contacts-table">
                            <i class="fas fa-trash"></i> Delete
                        </button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // Contacts from registered users with role 'driver'
    public function getDriverContacts(Request $request)
    {
        $query = ContactMessage::whereHas('user.roles', function ($q) {
            $q->where('name', 'driver');
        })->with('user');

        return DataTables::of($query)
            ->addColumn('driver_name', fn($contact) => $contact->user ? $contact->user->name : 'N/A')
            ->addColumn('email', fn($contact) => $contact->email)
            ->addColumn('phone', fn($contact) => $contact->phone)
            ->addColumn('message', fn($contact) => $contact->message)
            ->addColumn('actions', function ($contact) {
                return '<button class="btn btn-sm btn-danger delete-item" data-id="' . $contact->id . '" data-url="' . route('admin.contacts.destroy', $contact->id) . '" data-table="#driver-contacts-table">
                            <i class="fas fa-trash"></i> Delete
                        </button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    // Delete contact
    public function destroy($id)
    {
        $contact = ContactMessage::findOrFail($id);
        $old = $contact->toArray();
        $contact->delete();

        AudiLogsService::storeLog('delete', 'profile->contact', $contact->id, $old, null);
        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully!',
            'id' => $id,
        ]);
    }
}
