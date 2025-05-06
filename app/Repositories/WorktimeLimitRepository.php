<?php

namespace App\Repositories;

use App\Interfaces\WorktimeLimitRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\WorktimeLimit;

/**
 * Class WorktimeLimitRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WorktimeLimitRepository extends BaseRepository implements WorktimeLimitRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WorktimeLimit::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
