<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 7/7/18
 * Time: 1:25 PM
 */

namespace App\Repositories\User\Eloquent;

use App\Models\User\UserInfo;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\UserInfoInterface;

class UserInfoRepository extends BaseRepository implements UserInfoInterface
{
    /**
     * @var UserInfo
     */
    protected $model;

    public function __construct(UserInfo $model)
    {
        $this->model = $model;
    }
}