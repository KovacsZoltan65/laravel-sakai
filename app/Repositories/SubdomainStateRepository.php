<?php

namespace App\Repositories;

use App\Models\SubdomainState;
use App\Repositories\Interfaces\SubdomainStateRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\GetSubdomainStateRepository;
use App\Entities\GetSubdomainState;
use App\Validators\GetSubdomainStateValidator;

/**
 * Class GetSubdomainStateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SubdomainStateRepository extends BaseRepository implements SubdomainStateRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SubdomainState::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
