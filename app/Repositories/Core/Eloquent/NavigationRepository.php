<?php
/**
 * Created by PhpStorm.
 * User: zahid
 * Date: 2018-09-18
 * Time: 2:55 PM
 */

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\Navigation;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\NavigationInterface;

class NavigationRepository extends BaseRepository implements NavigationInterface
{
    /**
     * @var Navigation
     */
    protected $model;

    public function __construct(Navigation $model)
    {
        $this->model = $model;
    }

    public function getBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }
}