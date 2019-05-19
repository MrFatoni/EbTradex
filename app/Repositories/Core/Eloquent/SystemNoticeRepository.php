<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 7/7/18
 * Time: 1:25 PM
 */

namespace App\Repositories\Core\Eloquent;

use App\Models\Core\SystemNotice;
use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use Carbon\Carbon;

class SystemNoticeRepository extends BaseRepository implements SystemNoticeInterface
{
    /**
     * @var SystemNotice
     */
    protected $model;

    public function __construct(SystemNotice $model)
    {
        $this->model = $model;
    }

    public function todaysNotifications()
    {
        $startDate = Carbon::now();
        return $this->model->where('status', 1)->where(function ($q) use ($startDate) {
            $q->where('start_at', '<=', $startDate)
                ->where('end_at', '>=', $startDate);
        })->orWhere(function ($q) {
            $q->whereNull('start_at')
                ->whereNull('end_at');
        })->get();

    }
}