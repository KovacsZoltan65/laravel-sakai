<?php

namespace App\Repositories;

use App\Interfaces\CompanyRepositoryInterface;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class CompanyRepositoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    public function getCompanies(Request $request)
    {
        try {
            
            $_companies = Company::query();
            
            if ( $request->has('search') ) {
                $_companies->where('name', 'LIKE', "%{$request->search}%");
                $_companies->orWhere('email', 'LIKE', "%{$request->search}%");
                $_companies->orWhere('address', 'LIKE', "%{$request->search}%");
                $_companies->orWhere('phone', 'LIKE', "%{$request->search}%");
            }
            
            if( $request->has(['field', 'order']) ) {
                $_companies->orderBy($request->field, $request->order);
            }
            
            $page = $request->has('page') ? $request->page : 1;

            $retval = $_companies->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page);
            return $retval;
            
            /*
            // Keresési kulcsszó kinyerése a request-ből
            $search = $request->get('search');

            // Keresési lekérdezés végrehajtása a scopeSearch metódussal
            $companyQuery = Company::query()->search($search);

            // Oldaltörés
            $ret_val = $companyQuery->paginate(10);

            return $ret_val;
            */
        } catch( Exception $ex ) {
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
