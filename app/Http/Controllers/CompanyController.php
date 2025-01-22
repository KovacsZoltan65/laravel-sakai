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
            'title' => 'Company',
        ]);
    }

    public function applySearch(Builder $query, string $search)
    {
        return $query->when($search, function ($query, string $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }

    public function getCompanies(Request $request) {
        try {
            $_companies = $this->companyRepository->getCompanies($request);
            $companies = CompanyResource::collection($_companies)->pagination(10);
dd($companies);
            return response()->json($companies, Response::HTTP_OK);
        } catch( QueryException $ex ) {
            \Log::info('getCompanies QueryException: ' . print_r($ex->getMessage(), true));
        } catch( \Exception $ex ) {
            \Log::info('getCompanies Exception: ' . print_r($ex->getMessage(), true));
        }
    }
}
