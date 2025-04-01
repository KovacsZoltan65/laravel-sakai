<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegionIndexRequest;
use App\Models\Region;
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
        return Inertia::render(component: 'Regions/Index', props: [
            'title' => 'Regions',
            'filters' => $request->all(keys: ['search', 'field', 'order']),
        ]);
    }

    public function fetch(Request $request): JsonResponse
    {
        $_regions = Region::query();

        if( $request->has(key: 'search') ) {
            $_regions->whereReaw("CONCAT(name, ' ', code) LIKE ?", ["%{$request->search}%"]);
        }

        if ($request->has(key: 'field') && $request->has(key: 'order')) {
            $_regions->orderBy(column: $request->field, direction: $request->order);
        }

        $regions = $_regions->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $request->page?? 1);

        return response()->json(data: $regions);
    }
}
