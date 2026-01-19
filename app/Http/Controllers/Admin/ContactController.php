<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $contacts = Contact::latest()->get();
    //     return response()->json(['data' => $contacts]);
    // }

    public function index()
{
    $contacts = Contact::latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $contacts->items(),
        'pagination' => [
            'current_page' => $contacts->currentPage(),
            'per_page' => $contacts->perPage(),
            'total' => $contacts->total(),
            'last_page' => $contacts->lastPage(),
            'has_more_pages' => $contacts->hasMorePages(),
        ],
    ]);
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return response()->json(['data' => $contact]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'sometimes|string',
            'is_read' => 'sometimes|boolean',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Contact updated successfully',
            'data' => $contact,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully',
        ]);
    }

    /**
     * Mark contact as read
     */
    public function markAsRead($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['is_read' => true]);

        return response()->json([
            'message' => 'Contact marked as read',
            'data' => $contact,
        ]);
    }
}

