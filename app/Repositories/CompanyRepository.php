<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Interfaces\CompanyRepositoryInterface;
use App\Validators\CompanyRepositoryValidator;
use App\Models\Company;

/**
 * Class CompanyRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Company::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
