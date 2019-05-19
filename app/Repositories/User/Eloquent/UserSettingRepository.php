<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 7/7/18
 * Time: 1:25 PM
 */

namespace App\Repositories\User\Eloquent;


use App\Models\User\UserSetting;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\UserSettingInterface;

class UserSettingRepository extends BaseRepository implements UserSettingInterface
{
    /**
     * @var UserSetting
     */
    protected $model;

    public function __construct(UserSetting $model)
    {
        $this->model = $model;
    }
}