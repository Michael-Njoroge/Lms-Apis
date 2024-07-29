<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\SessionResource;
use App\Models\Session;

class SessionController extends Controller
{
     public function bookSession(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'description' => 'required|string',
            'mobile' => 'required|numeric',
            'subject' => 'required|string',
            'date' => 'required|date',
            'timeslot' => 'required|date_format:H:i'
        ]);

        $session = Session::create($data);
        $createdSession = Session::findOrFail($session->id);

        return $this->sendResponse(SessionResource::make($createdSession)
                ->response()
                ->getData(true), 'Session booked successfully');
    }

    public function getSessions()
    {
        $session = Session::paginate(20);
        return $this->sendResponse(SessionResource::collection($session)
                ->response()
                ->getData(true), 'Sessions retrieved successfully');
    }

    public function getASession(Session $session)
    {
        return $this->sendResponse(SessionResource::make($session)
                ->response()
                ->getData(true),'Session retrieved successfully');
    }

    public function updateSession(Request $request, Session $session)
    {
        $session->update($request->all());
        $updatedSession = Session::findOrFail($session->id);

        return $this->sendResponse(SessionResource::make($updatedSession)
                ->response()
                ->getData(true),'Session updated successfully');
    }

    public function deleteSession(Session $session)
    {
        $session->delete();
        return $this->sendResponse([],'Session deleted successfully');
    }
}
