<?php

namespace App\Http\Controllers\User\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\CommentRequest;
use App\Http\Requests\User\TradeAnalyst\QuestionRequest;
use App\Repositories\Backend\Interfaces\CommentInterface;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionsController extends Controller
{
    private $questionRepository;


    public function __construct(QuestionInterface $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function index()
    {
        $searchFields = [
            ['questions.title', __('Title')],
        ];

        $orderFields = [
            ['questions.id', __('ID')],
            ['questions.title', __('Title')],
            ['questions.created_at', __('Created Date')],
        ];

        $conditions = ['questions.user_id' => Auth::id()];
        $select = ['questions.*', DB::raw('CONCAT(user_infos.first_name, " " , user_infos.last_name) as full_name')];
        $join = ['user_infos', 'user_infos.user_id', '=', 'questions.user_id'];

        $query = $this->questionRepository->paginateWithFilters($searchFields, $orderFields, $conditions, $select, $join);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Questions');

        return view('backend.questions.index', $data);
    }

    public function create()
    {
        $data['title'] = __('Create Question');
        return view('backend.questions.create', $data);
    }


    public function store(QuestionRequest $request)
    {
        $attributes = $request->only(['title', 'content']);
        $attributes['user_id'] = Auth::id();


        if ($question = $this->questionRepository->create($attributes)) {
            return redirect()->route('faq.show', $question->id)->with(SERVICE_RESPONSE_SUCCESS, __('Question has been created successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create question.'));
    }

}
