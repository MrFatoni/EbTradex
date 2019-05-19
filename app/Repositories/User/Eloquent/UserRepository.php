<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 7/7/18
 * Time: 1:25 PM
 */

namespace App\Repositories\User\Eloquent;

use App\Models\User\User;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\UserInterface;

class UserRepository extends BaseRepository implements UserInterface
{
    /**
     * @var User
     */
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getCountByConditions(array $conditions)
    {
        return $this->model->where($conditions)->count();
    }

    public function getByUserIds(array $ids, array $conditions = [])
    {
        $model = $this->model->whereIn('id', $ids);

        if (!empty($conditions)) {
            $model = $model->where($conditions);
        }

        return $model->get();
    }
}