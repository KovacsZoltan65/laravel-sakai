<?php

namespace App\Repositories;

use App\Interfaces\ShiftRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\Shift;

/**
 * Class ShiftRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShiftRepository extends BaseRepository implements ShiftRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Shift::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
