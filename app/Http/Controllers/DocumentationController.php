<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Resources\DocumentationResource;
use App\Models\Documentation;

class DocumentationController extends Controller
{
    public function postDocumentation(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:documentations,title',
            'type' => 'required|string',
            'content' => 'required|string',
            'author' => 'required|string',
            'category' => 'required|uuid|exists:document_categories,id',
            'keywords' => 'required|array',
            'keywords.*' => 'required|string',
        ]);
        $data['slug'] = Str::slug($data['title']);

        $documentation = Documentation::create($data);
        $createdDocumentation = Documentation::with('docCategory')->findOrFail($documentation->id);

        return $this->sendResponse(DocumentationResource::make($createdDocumentation)
                ->response()
                ->getData(true), 'Documentation created successfully');
    }

    public function getDocumentations()
    {
        $documentations = Documentation::with('docCategory')->paginate(20);
        return $this->sendResponse(DocumentationResource::collection($documentations)
                ->response()
                ->getData(true), 'Documentations retrieved successfully');
    }

    public function getADocumentation(Documentation $documentation)
    {
        $documentation->load('docCategory');
        return $this->sendResponse(DocumentationResource::make($documentation)
                ->response()
                ->getData(true),'Documentation retrieved successfully');
    }

    public function updateDocumentation(Request $request, Documentation $documentation)
    {
        $data = $request->all();

        if ($request->has('title')) {
            $data['slug'] = Str::slug($data['title']);
        }
        $documentation->update($data);
        $updatedDocumentation = Documentation::with('docCategory')->findOrFail($documentation->id);

        return $this->sendResponse(DocumentationResource::make($updatedDocumentation)
                ->response()
                ->getData(true),'Documentation updated successfully');
    }

    public function deleteDocumentation(Documentation $documentation)
    {
        $documentation->delete();
        return $this->sendResponse([],'Documentation deleted successfully');
    }
}
