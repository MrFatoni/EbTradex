<?php

namespace App\Http\Controllers\User\TradeAnalyst;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Admin\CommentRequest;
use App\Repositories\User\Interfaces\CommentInterface;
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

        $select = ['questions.*', DB::raw('CONCAT(user_infos.first_name, " " , user_infos.last_name) as full_name')];
        $join = ['user_infos', 'user_infos.user_id', '=', 'questions.user_id'];

        $query = $this->questionRepository->paginateWithFilters($searchFields, $orderFields, null, $select, $join);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Questions');

        return view('backend.questions.index', $data);
    }

    public function answerForm($id)
    {
        $data['question'] = $this->questionRepository->findOrFailById($id, ['user.userInfo', 'comments.user.userInfo']);
        $data['title'] = __('Edit Question');
        return view('backend.questions.answer', $data);
    }

    public function answer(CommentRequest $request, $id, CommentInterface $comment)
    {
        $question = $this->questionRepository->getFirstById($id);

        if (empty($question)) {
            return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Question could not found.'));
        }

        $attributes = $request->only('content');
        $attributes['user_id'] = Auth::id();


        if ($comment->save($attributes, $question)) {
            return redirect()->route('trade-analyst.questions.answer', $question->id)->with(SERVICE_RESPONSE_SUCCESS, __('Answer has been submitted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to answer.'));
    }

    public function destroy($id)
    {
        if ($this->questionRepository->deleteById($id) ) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The question has been deleted successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to delete the question.'));
    }
}
