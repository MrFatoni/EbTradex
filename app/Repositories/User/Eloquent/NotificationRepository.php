<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 7/7/18
 * Time: 1:25 PM
 */

namespace App\Repositories\User\Eloquent;


use App\Models\User\Notification;
use App\Repositories\BaseRepository;
use App\Repositories\User\Interfaces\NotificationInterface;
use Carbon\Carbon;

class NotificationRepository extends BaseRepository implements NotificationInterface
{
    /**
     * @var Notification
     */
    protected $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function getLastFive($userId)
    {
       return $this->model->where('user_id',$userId)->whereNull('read_at')->orderBy('id','desc')->take(5)->get();
    }

    public function countUnread($userId)
    {
        return $this->model->where('user_id',$userId)->whereNull('read_at')->count();
    }

    public function read($id)
    {
        $notice = $this->model->where('id', $id)->firstOrFail();
        if (empty($notice->read_at)) {
            $notice->read_at = Carbon::now();
            return $notice->update();
        }
        return false;
    }

    public function unread($id)
    {
        $notice = $this->model->where('id', $id)->firstOrFail();
        if (!empty($notice->read_at)) {
            $notice->read_at = null;
            return $notice->update();
        }
        return false;
    }
}