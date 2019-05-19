<?php

namespace App\Http\Controllers\TradingView;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\CommentRequest;
use App\Models\Backend\Post;
use App\Repositories\User\Interfaces\CommentInterface;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TradingViewController extends Controller
{
    public function index(){
        $searchFields = [
            ['posts.title', __('Title')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];

        $orderFields = [
            ['posts.title', __('Title')],
            ['posts.created_at', __('Date')],
        ];
        $where = ['is_published'=>ACTIVE_STATUS_ACTIVE];
        $groupBy = ['posts.id','posts.title','posts.content', 'posts.featured_image', 'posts.created_at','users.avatar', 'first_name', 'last_name'];

        $select = ['posts.id','posts.title','posts.content', 'posts.featured_image', 'posts.created_at', 'users.avatar', 'first_name', 'last_name',DB::raw('count(comments.id) as comments')];
        $joinArray = [
            ['users', 'users.id', '=', 'posts.user_id'],
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
            ['comments', 'comments.commentable_id', '=', 'posts.id',['commentable_type'=>get_class(new Post())]
            ],
        ];

        $query = app(PostInterface::class)->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray,$groupBy,6);
        $data['posts'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Trading Views');
        return view('frontend.trade_analysis.lists',$data);
    }

    public function show($id){
        $data['post'] = app(PostInterface::class)->findOrFailByConditions(['id'=>$id, 'is_published'=>ACTIVE_STATUS_ACTIVE]);
        $data['title'] = __('Trading View');
        return view('frontend.trade_analysis.show',$data);
    }

    public function comment(CommentRequest $request, $id)
    {
        $post = app(PostInterface::class)->getFirstById($id);

        if (empty($post)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Post could not found.'));
        }

        $attributes = $request->only('content');
        $attributes['user_id'] = Auth::id();


        if (app(CommentInterface::class)->save($attributes, $post))
        {
            return redirect()->route('trading-views.show', $post->id)->with(SERVICE_RESPONSE_SUCCESS, __('Comment has been submitted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to place comment.'));
    }
}