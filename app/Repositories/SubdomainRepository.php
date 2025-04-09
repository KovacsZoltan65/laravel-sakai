<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SubdomainRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Entities\Subdomain;
use App\Validators\SubdomainValidator;

/**
 * Class SubdomainRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SubdomainRepository extends BaseRepository implements SubdomainRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Subdomain::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
