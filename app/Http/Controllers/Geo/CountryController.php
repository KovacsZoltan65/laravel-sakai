<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountryIndexRequest;
use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\Region;
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
        $cities = City::all(columns: ['id', 'name']);
        $regions = Region::all(columns: ['id', 'name']);

        return Inertia::render(component: 'Geo/Country/Index', props: [
            'title' => 'Country',
            'filters' => $request->all(['search', 'field', 'order']),
            'regions' => $regions,
            'cities' => $cities
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_countries = Country::query();

        if( $request->has(key: 'search') ) {
            $_countries->whereRaw(
                sql: "CONCAT(name, ' ', email, ' ', address, ' ', phone) LIKE ?",
                bindings: ["%{$request->search}%"]
            );
        }

        if ($request->has(key: 'field') && $request->has(key: 'order')) {
            $_countries->orderBy(column: $request->field, direction: $request->order);
        }

        $countries = $_countries->with(relations: ['regions', 'cities'])
            ->withCount(relations: ['regions', 'cities'])
            ->paginate(
                perPage: 10,
                columns: ['*'],
                pageName:'page',
                page: $request->page ?? 1
            );

        return response()->json($countries);
    }
}
