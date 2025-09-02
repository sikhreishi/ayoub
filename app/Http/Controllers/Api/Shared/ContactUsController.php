<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactUsRequest;
use App\Models\ContactMessage;

class ContactUsController extends Controller
{
    public function store(ContactUsRequest $request)
    {
        $contact = ContactMessage::create($request->validated());
        return response()->json([
            'message' => 'Your message has been sent successfully.',
            'data' => $contact
        ], 201);
    }
}
