<?php

namespace App\Http\Controllers\Geo;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegionIndexRequest;
use App\Models\Geo\City;
use App\Models\Geo\Country;
use App\Models\Geo\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegionController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(RegionIndexRequest $request): InertiaResponse
    {
        $countries = Country::all(columns: ['id', 'name']);
        $cities = City::all(columns: ['id', 'name']);

        return Inertia::render(component: 'Geo/Region/Index', props: [
            'title' => 'Regions',
            'filters' => $request->all(keys: ['search', 'field', 'order']),
            'countries' => $countries,
            'cities' => $cities
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_regions = Region::query();

        if( $request->has(key: 'search') ) {
            $_regions->whereReaw(
                sql: "CONCAT(name, ' ', code) LIKE ?", 
                bindings: ["%{$request->search}%"]
            );
        }

        if ($request->has(key: 'field') && $request->has(key: 'order')) {
            $_regions->orderBy(column: $request->field, direction: $request->order);
        }

        $regions = $_regions->paginate(
            perPage: 10, 
            columns: ['*'], 
            pageName: 'page', 
            page: $request->page?? 1
        );

        return response()->json(data: $regions);
    }
}
