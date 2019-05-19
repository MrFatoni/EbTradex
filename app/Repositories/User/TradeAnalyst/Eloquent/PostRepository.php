<?php

namespace App\Repositories\User\TradeAnalyst\Eloquent;

use App\Models\Backend\Post;
use App\Repositories\BaseRepository;
use App\Repositories\User\TradeAnalyst\Interfaces\PostInterface;

class PostRepository extends BaseRepository implements PostInterface
{
    /**
     * @var Post
     */
    protected $model;

    public function __construct(Post $post)
    {
        $this->model = $post;
    }

    public function getLatestByCondition(array $conditions, $limit = null, $relations = [])
    {
        if (is_null($limit)) {
            return $this->model->where($conditions)->with($relations)->latest()->get();
        }

        return $this->model->where($conditions)->with($relations)->take($limit)->latest()->get();
    }

}