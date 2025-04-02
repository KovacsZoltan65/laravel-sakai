<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Http\Requests\CityIndexRequest;
use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class CityController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(CityIndexRequest $request): InertiaResponse
    {
        $regions = Region::all(columns: ['id', 'name']);
        $countries = Country::all(columns: ['id', 'name']);

        return Inertia::render(component: 'Geo/City/Index', props: [
            'title' => 'Cities',
            'filters' => $request->all(['search', 'field', 'order']),
            'regions' => $regions,
            'countries' => $countries,
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_cities = City::query();

        if( $request->has(key: 'search') ) {
            $_cities->whereRaw(
                sql: "CONCAT(name) LIKE ?", 
                bindings: ["%{$request->search}%"]
            );
        }

        if ($request->has(key: 'field') && $request->has(key: 'order')) {
            $_cities->orderBy(column: $request->field, direction: $request->order);
        }

        $cities = $_cities->with(relations: ['region', 'country'])
            ->paginate(
                perPage: 10, 
                columns: ['*'], 
                pageName: 'page', 
                page: $request->page?? 1
            );

        return response()->json($cities);
    }
}
