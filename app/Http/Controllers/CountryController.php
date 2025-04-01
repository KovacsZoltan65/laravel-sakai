<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryIndexRequest;
use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class CountryController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(CountryIndexRequest $request): InertiaResponse
    {
        return Inertia::render('Country/Index', [
            'title' => 'Country',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_countries = Country::query();

        if( $request->has(key: 'search') ) {
            $_countries->whereRaw("CONCAT(name, ' ', email, ' ', address, ' ', phone) LIKE ?", ["%{$request->search}%"]);
        }

        if ($request->has('field') && $request->has('order')) {
            $_countries->orderBy($request->field, $request->order);
        }

        $companies = $_countries->with('entities')->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($companies);
    }
}
