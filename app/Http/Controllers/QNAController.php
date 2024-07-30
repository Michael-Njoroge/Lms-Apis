<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\{
    AnswerResource,
    QnaCommentResource,
    QnaTagResource,
    QNAResource,
    QuestionResource
};
use App\Models\{
    Question,
    Answer,
    QnaSession,
    QnaTag,
    QnaComment,
    QnaVote
};

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

        try {
            DB::beginTransaction();

            $question = Question::create($data);
            $createdQuestion = Question::findOrFail($question->id);

            DB::commit();

            if (isset($data['tag_id'])) {
                $oldTag = $createdQuestion->tag;
                $newTag = QnaTag::findOrFail($data['tag_id']);

                $createdQuestion->tag()->associate($data['tag_id']);
                $createdQuestion->save();

                DB::commit();

                if ($newTag) {
                    $newTag->increment('total_questions');
                }

                if ($oldTag) {
                    $oldTag->decrement('total_questions');
                }
            }

            $qna_session = QnaSession::create([
                'user_id' => auth()->id(),
                'question_id' => $question->id,
            ]);

            DB::commit();

            $createdQnaSession = QnaSession::with([
                'question', 
                'answer',
                'user', 
                'question.tag', 
                'question.comments', 
                'question.answers.comments',
                'question.answers.votes',
                'question.answers.votes.user',
                'question.answers.comments.user',
                'question.comments.user'
            ])->findOrFail($qna_session->id);

            return $this->sendResponse(QNAResource::make($createdQnaSession)
                    ->response()
                    ->getData(true),'QNA post created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create post: ' . $e->getMessage());
        }
    }

    public function createAnswer(Request $request, QnaSession $post)
    {
        $data = $request->validate([
            'description' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $data['user_id'] = auth()->id();

            $answer = Answer::create([
                'user_id' => $data['user_id'],
                'question_id' => $post->question_id,
                'description' => $data['description']
            ]);

            $post->update([
                'answer_id' => $answer->id
            ]);

            DB::commit();

            $answeredQnaSession = QnaSession::with([
                'question', 
                'answer',
                'user', 
                'question.tag', 
                'question.comments', 
                'question.answers.comments',
                'question.answers.votes',
                'question.answers.votes.user',
                'question.answers.comments.user',
                'question.comments.user'
            ])->findOrFail($post->id);

            return $this->sendResponse(QNAResource::make($answeredQnaSession)
                    ->response()
                    ->getData(true),'QNA answered successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create answer: ' . $e->getMessage());
        }
    }

    public function getAllPosts()
    {
        $posts = QnaSession::with([
           'question', 
            'answer',
            'user', 
            'question.tag', 
            'question.comments', 
            'question.answers.comments',
            'question.answers.votes',
            'question.answers.votes.user',
            'question.answers.comments.user',
            'question.comments.user'
        ])->paginate(20);
        
        return $this->sendResponse(QNAResource::collection($posts)
                ->response()
                ->getData(true),'QNA posts retrieved successfully');
    }

    public function getPost(QnaSession $post)
    {
        $post->load([
            'question', 
            'answer',
            'user', 
            'question.tag', 
            'question.comments', 
            'question.answers.comments',
            'question.answers.votes',
            'question.answers.votes.user',
            'question.answers.comments.user',
            'question.comments.user'
        ]);
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

        try {
            DB::beginTransaction();

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

            $updatedPost = QnaSession::with([
                'question', 
                'answer',
                'user', 
                'question.tag', 
                'question.comments', 
                'question.answers.comments',
                'question.answers.votes',
                'question.answers.votes.user',
                'question.answers.comments.user',
                'question.comments.user'
            ])->findOrFail($post->id);

            DB::commit();

            return $this->sendResponse(QNAResource::make($updatedPost)
                    ->response()
                    ->getData(true),'QNA post updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to update tag: ' . $e->getMessage());
        }
    }

    public function deletePost(QnaSession $post)
    {
        try {
            DB::beginTransaction();

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

            DB::commit();

            return $this->sendResponse([], 'QNA post deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create tag: ' . $e->getMessage());
        }
    }



    ///////////////////////////////////////////////////*********QNA TAGS********/////////////////////////////////////////////////////
    public function createTag(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|unique:questions,title',
            'description' => 'required|string',
        ]);
        try {
            DB::beginTransaction();

            $data['slug'] = Str::slug($data['title']);
            $tag = QnaTag::create($data);

            $createdTag = QnaTag::findOrFail($tag->id);

            DB::commit();

            return $this->sendResponse(QnaTagResource::make($createdTag)
                ->response()
                ->getData(true), 'Tag created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to create tag: ' . $e->getMessage());
        }
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




    /////////////////////////////////////////////////////////********QNA COMMENTS*********////////////////////////////////////////////////
    public function postComment(Request $request)
    {
        $data = $request->validate([
            'comment' => 'required|string',
            'question_id' => 'required_without:answer_id|uuid|exists:questions,id',
            'answer_id' => 'required_without:question_id|uuid|exists:answers,id',
        ]);

        $data['user_id'] = auth()->id();

        // Ensure either question_id or answer_id is provided, but not both
        if (!isset($data['question_id']) && !isset($data['answer_id'])) {
            return $this->sendError('Either question_id or answer_id must be provided');
        }

        try {
            DB::beginTransaction();

            if (isset($data['question_id'])) {
                $question = Question::findOrFail($data['question_id']);
                $comment = QnaComment::create([
                    'comment' => $data['comment'],
                    'question_id' => $data['question_id'],
                    'user_id' => $data['user_id']
                ]);
                $createdComment = QnaComment::with(['question', 'user'])->findOrFail($comment->id);

                DB::commit();

                return $this->sendResponse(QnaCommentResource::make($createdComment)
                    ->response()
                    ->getData(true), 'Question commented successfully');
            }

            if (isset($data['answer_id'])) {
                $answer = Answer::findOrFail($data['answer_id']);
                $comment = QnaComment::create([
                    'comment' => $data['comment'],
                    'answer_id' => $data['answer_id'],
                    'user_id' => $data['user_id']
                ]);
                $createdComment = QnaComment::with(['answer', 'user'])->findOrFail($comment->id);

                DB::commit();

                return $this->sendResponse(QnaCommentResource::make($createdComment)
                    ->response()
                    ->getData(true), 'Answer commented successfully');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to post comment: ' . $e->getMessage());
        }
    }

    public function getAllComments(Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required_without:answer_id|uuid|exists:questions,id',
            'answer_id' => 'required_without:question_id|uuid|exists:answers,id'
        ]);

        if (isset($data['question_id'])) {
            $comments = QnaComment::with('user')->where('question_id',$data['question_id'])->paginate(20);

            return $this->sendResponse(QnaCommentResource::collection($comments)
                    ->response()
                    ->getData(true), 'Question comments retrieved successfully');

        }elseif (isset($data['answer_id'])) {
            $comments = QnaComment::with('user')->where('answer_id',$data['answer_id'])->paginate(20);

            return $this->sendResponse(QnaCommentResource::collection($comments)
                    ->response()
                    ->getData(true), 'Answer comments retrieved successfully');
        }

    }

    public function getAComment(Request $request, QnaComment $comment)
    {
        $data = $request->validate([
            'question_id' => 'required_without:answer_id|uuid|exists:questions,id',
            'answer_id' => 'required_without:question_id|uuid|exists:answers,id'
        ]);

        if (isset($data['question_id'])) {
            $question = Question::findOrFail($data['question_id']);
            $singleComment = $question->comments->where('id',$comment->id)->first();
            $singleComment ->load('user');

            return $this->sendResponse(QnaCommentResource::make($singleComment)
                    ->response()
                    ->getData(true), 'Question single comment retrieved successfully');

        }elseif (isset($data['answer_id'])) {
            $answer = Answer::findOrFail($data['answer_id']);
            $singleComment = $answer->comments->where('id', $comment->id)->first();
            $singleComment ->load('user');

            return $this->sendResponse(QnaCommentResource::make($singleComment)
                    ->response()
                    ->getData(true), 'Answer single comment retrieved successfully');
        }

    }

    public function deleteComment(Request $request)
    {
        $data = $request->validate([
            'question_id' => 'required_without:answer_id|uuid|exists:questions,id',
            'answer_id' => 'required_without:question_id|uuid|exists:answers,id'
        ]);

        try {
            DB::beginTransaction();

            if (isset($data['question_id'])) {
                $question = Question::findOrFail($data['question_id']);
                $comment = QnaComment::where('question_id', $data['question_id'])->first();
                if ($comment) {
                    $comment->delete();
                    DB::commit();
                    return $this->sendResponse([], 'Question comment deleted successfully');
                } else {
                    DB::commit();
                    return $this->sendError('Comment not found');
                }
            }

            if (isset($data['answer_id'])) {
                $answer = Answer::findOrFail($data['answer_id']);
                $comment = QnaComment::where('answer_id', $data['answer_id'])->first();
                if ($comment) {
                    $comment->delete();
                    DB::commit();
                    return $this->sendResponse([], 'Answer comment deleted successfully');
                } else {
                    DB::commit();
                    return $this->sendError('Comment not found');
                }
            }
             DB::rollBack();
            return $this->sendError('Either question_id or answer_id must be provided');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to post comment: ' . $e->getMessage());
        }

    }


    

    /////////////////////////////////////////////////////**********QNA VOTES**********/////////////////////////////////////////////////
    public function castVote(Request $request)
    {
        $data = $request->validate([
            'vote_type' => 'required|in:up_vote,down_vote',
            'question_id' => 'required_without:answer_id|uuid|exists:questions,id',
            'answer_id' => 'required_without:question_id|uuid|exists:answers,id',
        ]);

        $data['user_id'] = auth()->id();

        if (!isset($data['question_id']) && !isset($data['answer_id'])) {
            return $this->sendError('Either question_id or answer_id must be provided');
        }

        try {
            DB::beginTransaction();

            if (isset($data['question_id'])) {
                $question = Question::findOrFail($data['question_id']);
                $alreadyVoted = QnaVote::where('user_id', $data['user_id'])
                        ->where('question_id', $data['question_id'])
                        ->first();

                if ($alreadyVoted) {
                    $alreadyVoted->update(['vote_type' => $data['vote_type']]);
                }else{
                    QnaVote::create([
                        'user_id' => $data['user_id'],
                        'question_id' => $data['question_id']
                    ]);
                }

                $upVotes = QnaVote::where('question_id', $data['question_id'])
                    ->where('vote_type', 'up_vote')
                    ->count();

                $downVotes =QnaVote::where('question_id', $data['question_id'])
                    ->where('vote_type', 'down_vote')
                    ->count();

                $voteCount = $upVotes - $downVotes;
                $question->vote_count = max(0, $voteCount);
                $question->save();

                DB::commit();

                return $this->sendResponse(QuestionResource::make($question)
                    ->response()
                    ->getData(true),'Vote cast successfully');

            }elseif (isset($data['answer_id'])) {
                $answer = Answer::findOrFail($data['answer_id']);
                $alreadyVoted = QnaVote::where('user_id', $data['user_id'])
                    ->where('answer_id',$data['answer_id'])
                    ->first();
                if ($alreadyVoted) {
                    $alreadyVoted->update(['vote_type',$data['vote_type']]);
                }else{
                    QnaVote::create([
                        'user_id' => $data['user_id'],
                        'answer_id' => $data['answer_id']
                    ]);
                }

                $upVotes = QnaVote::where('answer_id',$data['answer_id'])
                    ->where('vote_type', 'up_vote')
                    ->count();

                $downVotes = QnaVote::where('answer_id', $data['answer_id'])
                    ->where('vote_type', 'down_vote')
                    ->count();

                $voteCount = $upVotes - $downVotes;
                $answer->vote_count = max(0, $voteCount);
                $answer->save();

                DB::commit();

                return $this->sendResponse(AnswerResource::make($answer)
                    ->response()
                    ->getData(true),'Vote cast successfully');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Failed to cast vote: ' . $e->getMessage());
        }
    }
}
