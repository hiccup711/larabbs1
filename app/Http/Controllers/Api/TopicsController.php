<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Http\Resources\TopicResource;
use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\User;
use App\Http\Queries\TopicQuery;

class TopicsController extends Controller
{
    public function index(Request $request, TopicQuery $query)
    {
        $topics = $query->paginate();

        return TopicResource::collection($topics);
    }

    public function userIndex(Request $request, User $user, TopicQuery $query)
    {
        $topics = $query->where('user_id', $user->id)->paginate();

        return TopicResource::collection($topics);
    }

    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;
        $topic->save();

        return new TopicResource($topic);
    }

    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);
        $topic->update($request->all());

        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);
        $topic->delete();

        return response(null, 204);
    }

    public function show($topicId)
    {
        $topic = QueryBuilder::for(Topic::class)->allowedIncludes(['user','category'])->findOrFail($topicId);
        return new TopicResource($topic);
    }
}
