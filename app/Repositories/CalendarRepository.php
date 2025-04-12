<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Interfaces\CalendarRepositoryInterface;
use App\Models\Calendar;

/**
 * Class CalendarRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CalendarRepository extends BaseRepository implements CalendarRepositoryInterface
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Calendar::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
