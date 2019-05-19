<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/2/18
 * Time: 4:57 PM
 */

namespace App\Repositories\Core\Eloquent;


use App\Repositories\BaseRepository;
use App\Repositories\Core\Interfaces\AuditInterface;
use OwenIt\Auditing\Models\Audit;

class AuditRepository extends BaseRepository implements AuditInterface
{
    /**
     * @var Audit
     */
    protected $model;

    public function __construct(Audit $audit)
    {
        $this->model = $audit;
    }
}