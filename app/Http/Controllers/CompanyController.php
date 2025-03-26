<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyIndexRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;

class CompanyController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(CompanyIndexRequest $request): InertiaResponse
    {
        $_companies = Company::query();

        if( $request->has(key: 'search') ) {
            $_companies->whereRaw("CONCAT(name, ' ', email, ' ', address, ' ', phone) LIKE ?", ["%{$request->search}%"]);
            //$_companies->where(column: 'name', operator: 'LIKE', value: "%{$request->search}%");
            //$_companies->orWhere(column: 'email', operator: 'LIKE', value: "%{$request->search}%");
            //$_companies->orWhere(column: 'address', operator: 'LIKE', value: "%{$request->search}%");
            //$_companies->orWhere(column: 'phone', operator: 'LIKE', value: "%{$request->search}%");
        }

        if ($request->has('field') && $request->has('order')) {
            $_companies->orderBy($request->field, $request->order);
        }
        //if( $request->hasMany( ['field', 'order'] ) ) {
        //    $_companies->orderBy(column: $request->field, direction: $request->order);
        //}

        $page = $request->page ?? 1;

        $companies = $_companies->paginate(
            perPage: 10,
            columns: ['*'],
            pageName: 'page',
            page: $page
        );

        $params = [
            'title' => 'Companies',
            'filters' => $request->all(['search', 'field', 'order']),
            'companies' => $companies,
        ];

        return Inertia::render('Companies/Index', $params);
    }
}
