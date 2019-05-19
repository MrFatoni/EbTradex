<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 11:31 AM
 */

namespace App\Repositories\Core\Interfaces;

interface NavigationInterface
{
    public function getBySlug($slug);
}