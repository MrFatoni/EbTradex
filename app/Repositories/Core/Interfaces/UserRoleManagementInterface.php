<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 12:34 PM
 */

namespace App\Repositories\Core\Interfaces;


interface UserRoleManagementInterface
{
    public function getUserRoles();

    public function getDefaultRole();

    public function create(array $parameters);

    public function update(array $parameters, int $id, string $attribute);

    public function deleteById(int $id);

    public function isNonDeletableRole(int $id);

    public function toggleStatusById(int $id);
}