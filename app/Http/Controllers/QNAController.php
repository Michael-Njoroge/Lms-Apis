<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\QNAResource;
use App\Http\Resources\QnaTagResource;
use Illuminate\Support\Str;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QnaSession;
use App\Models\QnaTag;

class QNAController extends Controller
{
    public function createPost(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:questions,title',
            'description' => 'required|string',
            'tag_id' => 'sometimes|required|uuid|exists:qna_tags,id'
        ]);

        $data['slug'] = Str::slug($data['title']);

        $question = Question::create($data);

        if (isset($data['tag_id'])) {
            $tag = QnaTag::findOrFail($data['tag_id']);
            if ($tag) {
                $tag->increment('total_questions');
            }
        }

        $qna_session = QnaSession::create([
            'user_id' => auth()->id(),
            'question_id' => $question->id,
        ]);

        $createdQnaSession = QnaSession::with(['question', 'answer','user', 'question.tag', 'question.comments', 'question.votes', 'question.answers'])->findOrFail($qna_session->id);

        return $this->sendResponse(QNAResource::make($createdQnaSession)
                ->response()
                ->getData(true),'QNA post created successfully');
    }

    public function createAnswer(Request $request, QnaSession $post)
    {
        $data = $request->validate([
            'description' => 'required|string'
        ]);

        $data['user_id'] = auth()->id();

        $answer = Answer::create([
            'user_id' => $data['user_id'],
            'question_id' => $post->question_id,
            'description' => $data['description']
        ]);

        $post->update([
            'answer_id' => $answer->id
        ]);
        $answeredQnaSession = QnaSession::with(['question', 'answer','user', 'question.tag', 'question.comments', 'question.votes', 'question.answers'])->findOrFail($post->id);

        return $this->sendResponse(QNAResource::make($answeredQnaSession)
                ->response()
                ->getData(true),'QNA answered successfully');
    }

    public function getAllPosts()
    {
        $posts = QnaSession::with(['question', 'answer','user', 'question.tag', 'question.comments', 'question.votes', 'question.answers'])->paginate(20);
        return $this->sendResponse(QNAResource::collection($posts)
                ->response()
                ->getData(true),'QNA posts retrieved successfully');
    }

    public function getPost(QnaSession $post)
    {
        $post->load(['question', 'answer','user', 'question.tag', 'question.comments', 'question.votes', 'question.answers']);
        return $this->sendResponse(QNAResource::make($post)
                ->response()
                ->getData(true),'QNA post retrieved successfully');
    }

    public function  updatePost(Request $request, QnaSession $post)
    {
        $data = $request->validate([
            'question.title' => 'sometimes|required|string|unique:questions,title,' . $post->question->id,
            'question.description' => 'sometimes|required|string',
            'answer.description' => 'sometimes|required|string',
            'tag_id' => 'sometimes|required|uuid|exists:qna_tags,id'
        ]);
        $question = $post->question;
        $answer = $post->answer;

         if ($question && isset($data['question'])) {
            $question->update($data['question']);
        }

        if (isset($data['tag_id'])) {
            $oldTag = $question->tag;
            $newTag = QnaTag::findOrFail($data['tag_id']);

            $question->tag()->associate($data['tag_id']);
            $question->save();

            if ($newTag) {
                $newTag->increment('total_questions');
            }

            if ($oldTag) {
                $oldTag->decrement('total_questions');
            }
        }

        if ($answer && isset($data['answer'])) {
            $answer->update($data['answer']);
        }

        $updatedPost = QnaSession::with(['question', 'answer','user', 'question.tag', 'question.comments', 'question.votes', 'question.answers'])->findOrFail($post->id);

        return $this->sendResponse(QNAResource::make($updatedPost)
                ->response()
                ->getData(true),'QNA post updated successfully');
    }

    public function deletePost(QnaSession $post)
    {
        $question = $post->question;
        $answer = $post->answer;
        $tag = $question->tag;

        $post->delete();

        if ($question) {
            $question->delete();
        }
        if ($tag) {
            $tag->decrement('total_questions');
        }
        if ($answer) {
            $answer->delete();
        }

        return $this->sendResponse([], 'QNA post deleted successfully');
    }

    //////////////////////////TAGS/////////////////////////
    public function createTag(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:questions,title',
            'description' => 'required|string',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $tag = QnaTag::create($data);

        $createdTag = QnaTag::findOrFail($tag->id);

        return $this->sendResponse(QnaTagResource::make($createdTag)
            ->response()
            ->getData(true), 'Tag created successfully');
    }

    public function getAllTags()
    {
        $tags = QnaTag::paginate(20);

        return $this->sendResponse(QnaTagResource::collection($tags)
            ->response()
            ->getData(true), 'Tags retrieved successfully');
    }

    public function getATag(QnaTag $tag)
    {
        return $this->sendResponse(QnaTagResource::make($tag)
            ->response()
            ->getData(true), 'Tag retrieved successfully');
    }

    public function updateTag(Request $request, QnaTag $tag)
    {
        $data = $request->all();
        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->input('title'));
        }

        $tag->update($data);
        $updatedTag = QnaTag::findOrFail($tag->id);

        return $this->sendResponse(QnaTagResource::make($tag)
            ->response()
            ->getData(true), 'Tag updated successfully');
    }

    public function deleteTag(QnaTag $tag)
    {
        $tag->delete();
        return $this->sendResponse([], 'Tag deleted successfully');
    }

}
