<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Repositories\CompanyRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    protected $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function index(Request $request)
    {
        return inertia('Companies/Index', [
            'filters' => $request->all(['search', 'field', 'order']),
            'title' => 'Companies',
        ]);
    }

    public function applySearch(Builder $query, string $search)
    {
        return $query->when($search, function ($query, string $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }

    public function getCompanies(Request $request)
    {
\Log::info('CompanyController@getCompanies $request: ' . print_r($request->all(), true));
        try {
            $_companies = $this->companyRepository->getCompanies($request);

            // Resource kollekcióval formázás
            $companiesResource = CompanyResource::collection($_companies);

            $retval = [
                'data' => $companiesResource->items(),
                'title' => 'Companies',
                'filters' => $request->all(['search', 'field', 'order']),
                'pagination' => [
                    'current_page' => $_companies->currentPage(),
                    'per_page' => $_companies->perPage(),
                    'total' => $_companies->total(),
                    'last_page' => $_companies->lastPage(),
                ]
            ];
\Log::info('$retval: ' . print_r($retval, true));
            return response()->json($retval, Response::HTTP_OK);

        } catch( QueryException $ex ) {
            \Log::info('getCompanies QueryException: ' . print_r($ex->getMessage(), true));
        } catch( \Exception $ex ) {
            \Log::info('getCompanies Exception: ' . print_r($ex->getMessage(), true));
        }
    }
}
