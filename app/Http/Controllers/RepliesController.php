<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReplyRequest;

class RepliesController extends Controller
{
    protected $at_expire_in_seconds = 60*5;
    protected $field_prefix = 'user_';

    public $test;
    public function __construct()
    {
        $this->middleware('auth');
    }

	public function store(ReplyRequest $request, Reply $reply)
	{
	    $reply->body = $request->body;
	    $reply->user_id = \Auth::id();
	    $reply->topic_id = $request->topic_id;
	    $reply->save();
		return redirect()->to($reply->topic->link())->with('success', '评论发布成功！');
	}

	public function destroy(Reply $reply)
	{
		$this->authorize('destroy', $reply);
		$reply->delete();

		return redirect()->to($reply->topic->link())->with('success', '评论已被删除！');
	}

	public function atUsers(Request $request)
    {
        $name = $request->q;
        $users = User::where('name', 'like', $name.'%')->pluck('name')->toArray();
        return response()->json($users);
    }

    public function test()
    {
        $this->test = 100;
    }

    public function test2()
    {
        echo $this->test;
    }
}
