<?php

namespace App\Http\Controllers\TradingView;

use App\Models\User\Question;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Services\Core\DataListService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    public function index(){
        $searchFields = [
            ['questions.title', __('Title')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];

        $orderFields = null;
        $groupBy = ['questions.id','questions.title','questions.content', 'questions.created_at','users.avatar', 'first_name', 'last_name'];

        $select = ['questions.id','questions.title','questions.content', 'questions.created_at', 'users.avatar', 'first_name', 'last_name',DB::raw('count(comments.id) as comments')];
        $joinArray = [
            ['users', 'users.id', '=', 'questions.user_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
            ['comments', 'comments.commentable_id', '=', 'questions.id',['commentable_type'=>get_class(new Question())]
            ],
        ];

        $query = app(QuestionInterface::class)->paginateWithFilters($searchFields, $orderFields, null,$select, $joinArray,$groupBy,20);
        $data['questions'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields, true);
        $data['title'] = __('Frequently Asked Questions');
        return view('frontend.faq.index',$data);
    }
    public function show($id){
        $data['question'] = app(QuestionInterface::class)->findOrFailByConditions(['id'=>$id]);
        $data['title'] = __('Question Details');
        return view('frontend.faq.show',$data);
    }
}
