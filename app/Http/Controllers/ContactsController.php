<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ContactsResource;
use App\Models\Contacts;

class ContactsController extends Controller
{
    public function createContact(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|numeric|min:10',
            'subject' => 'required|string',
            'profession' => 'required|string',
            'comment' => 'required|string',
        ]);
        $contact = Contacts::create($data);
        $createdContact = Contacts::findOrFail($contact->id);

        return $this->sendResponse(ContactsResource::make($createdContact)
                ->response()
                ->getData(true),'Contact added successfully');
    }

    public function getContacts()
    {
        $contacts = Contacts::paginate(20);
        return $this->sendResponse(ContactsResource::collection($contacts)
                ->response()
                ->getData(true), 'Contacts retrieved successfully');
    }

    public function getAContact(Contacts $contact)
    {
        return $this->sendResponse(ContactsResource::make($contact)
                ->response()
                ->getData(true), 'Contact retrieved successfully');
    }

    public function updateContact(Request $request, Contacts $contact)
    {
        $contact->update($request->all());
        $updatedContact = Contacts::findOrFail($contact->id);
        return $this->sendResponse(ContactsResource::make($updatedContact)
                ->response()
                ->getData(true), 'Contact updated successfully');
    }

    public function deleteContact(Contacts $contact)
    {
        $contact->delete();
        return $this->sendResponse([], 'Contact deleted successfully');
    }
}
