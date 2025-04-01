<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Interfaces\RegionRepositoryInterface;
use App\Entities\Region;

/**
 * Class RegionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Region::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
