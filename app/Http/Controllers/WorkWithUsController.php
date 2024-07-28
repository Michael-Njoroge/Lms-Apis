<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\WorkWithUsResource;
use App\Models\WorkWithUs;

class WorkWithUsController extends Controller
{
    public function postWorkDetails(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'mobile' => 'required|numeric|min:10',
            'profession' => 'required|string',
            'currentjob' => 'required|string',
            'resume' => 'required|string'
        ]);

        $detail = WorkWithUs::create($data);
        $createdDetail = WorkWithUs::findOrFail($detail->id);

        return $this->sendResponse(WorkWithUsResource::make($createdDetail)
                ->response()
                ->getData(true),'Work details created successfully');
    }

    public function getWorkDetails()
    {
        $details = WorkWithUs::paginate(20);
        return $this->sendResponse(WorkWithUsResource::collection($details)
                ->response()
                ->getData(true), 'Work details retrieved successfully');     
    }

    public function getAWorkDetail(WorkWithUs $workdetail)
    {
        return $this->sendResponse(WorkWithUsResource::make($workdetail)
            ->response()
            ->getData(true),'Work detail retrieved successfully');
    }

    public function updateWorkDetail(Request $request, WorkWithUs $workdetail)
    {
        $workdetail->update($request->all());
        $updatedDetail = WorkWithUs::findOrFail($workdetail->id);
        return $this->sendResponse(WorkWithUsResource::make($updatedDetail)
            ->response()
            ->getData(true),'Work detail updated successfully');
    }

    public function deleteWorkDetail(WorkWithUs $workdetail)
    {
        $workdetail->delete();
        return $this->sendResponse([],'Work detail deleted successfully');
    }
}
