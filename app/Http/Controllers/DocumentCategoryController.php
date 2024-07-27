<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\DocumentCategoryResource;
use App\Models\DocumentCategory;

class DocumentCategoryController extends Controller
{
    public function postDocumentCategory(Request $request)
    {
         $data = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $document = DocumentCategory::create($data);
        $createdDocumentCat = DocumentCategory::findOrFail($document->id);
        return $this->sendResponse(DocumentCategoryResource::make($createdDocumentCat)
            ->response()
            ->getData(true),'Document category created successfully');
    }

    public function getAllDocumentCategories()
    {
        $documentCategories = DocumentCategory::paginate(20);
        return $this->sendResponse(DocumentCategoryResource::collection($documentCategories)
            ->response()
            ->getData(true),'Document categories retrieved successfully');
    }

    public function getADocumentCategory(DocumentCategory $document)
    {
        return $this->sendResponse(DocumentCategoryResource::make($document)
            ->response()
            ->getData(true),'Document category retrieved successfully');
    }

    public function updateDocumentCategory(DocumentCategory $document, Request $request)
    {
        $document->update($request->all());
        $updatedDocument = DocumentCategory::findOrFail($document->id);
        return $this->sendResponse(DocumentCategoryResource::make($updatedDocument)
            ->response()
            ->getData(true),'Document category updated successfully');
    }

     public function deleteDocumentCategory(DocumentCategory $document)
    {
        $document->delete();
        return $this->sendResponse([],'Document category deleted successfully');
    }
}
