<?php

namespace App\Repositories\User\Trader\Eloquent;

use App\Models\User\Question;
use App\Repositories\User\Trader\Interfaces\QuestionInterface;
use App\Repositories\BaseRepository;

class QuestionRepository extends BaseRepository implements QuestionInterface
{
    /**
     * @var Question
     */
    protected $model;

    public function __construct(Question $question)
    {
        $this->model = $question;
    }
}