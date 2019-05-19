<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 12:11 PM
 */

namespace App\Repositories\User\Interfaces;


interface UserInterface
{
    public function getCountByConditions(array $conditions);

    public function getByUserIds(array $ids, array $conditions = []);
}