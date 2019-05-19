<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 11:19 AM
 */

namespace App\Repositories\User\Interfaces;


interface NotificationInterface
{
    public function read($id);

    public function unread($id);

    public function countUnread($userId);

    public function getLastFive($userId);
}