<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\NewsLetterResource;
use App\Models\NewsLetter;

class NewsLetterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:news_letters,email'
        ]);

        $newsLetter = NewsLetter::create($data);
        $createdNewsLetter = NewsLetter::where('email',$newsLetter->email)->first();
        return $this->sendResponse(NewsLetterResource::make($createdNewsLetter)
            ->response()
            ->getData(true),'Subscribed to NewsLetter');
    }

    public function unsubscribe(NewsLetter $news_letter)
    {
        $news_letter->delete();
        return $this->sendResponse([],'UnSubscribed to NewsLetter');
    }
}