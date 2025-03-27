<?php

namespace App\Repositories;

use App\Interfaces\PersonRepositoryInterface;
use App\Models\Person;
use Illuminate\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Exception;

class PersonRepository extends BaseRepository implements PersonRepositoryInterface
{
    public function getPersons(Request $request)
    {
        try
        {
            $_persons = Person::query();

            if( $request->has(key: 'search') ) {
                $_persons->where(column: 'name', operator: 'LIKE', value: "%{$request->search}%");
                $_persons->orWhere(column: 'email', operator: 'LIKE', value: "%{$request->search}%");
                $_persons->orWhere(column: 'address', operator: 'LIKE', value: "%{$request->search}%");
                $_persons->orWhere(column: 'phone', operator: 'LIKE', value: "%{$request->search}%");
            }

            if( $request->hasAny(keys: ['field', 'order']) ) {
                $_persons->orderBy(column: $request->field, direction: $request->order);
            }

            $page = $request->page ?? null;

            return $_persons->paginate(
                perPage: 10, 
                columns: ['*'], 
                pageName: 'page', 
                page: $page
            );

        } catch(Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Person::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}