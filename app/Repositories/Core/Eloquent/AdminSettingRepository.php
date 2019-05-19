<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 2018-07-30
 * Time: 2:47 PM
 */

namespace App\Repositories\Core\Eloquent;


use App\Models\Core\AdminSetting;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\AdminSettingInterface;


class AdminSettingRepository extends BaseRepository implements AdminSettingInterface
{
    /**
     * @var AdminSetting
     */
    protected $model;

    public function __construct(AdminSetting $model)
    {
        $this->model = $model;
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug')->firstOrFail();
    }

    public function getBySlugs($slugs)
    {
        return $this->model->whereIn('slug',$slugs)->pluck('value', 'slug')->toArray();
    }
}