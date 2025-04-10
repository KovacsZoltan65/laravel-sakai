<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubdomainStateIndexRequest;
use App\Models\Subdomain;
use App\Models\SubdomainState As State;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;

class SubdomainStateController extends Controller
{
    public function __construct()
    {
        //
    }
    
    public function index(SubdomainStateIndexRequest $request)
    {
        $subdomains = Subdomain::all();
        
        return \Inertia\Inertia::render('SubdomainState/Index', [
            'title' => 'Subdomain States',
            'filters' => $request->all(['search', 'field', 'order']),
            'subdomains' => $subdomains,
        ]);
    }
    
    public function fetch(Request $request): JsonResponse
    {
        $_states = State::query();
        
        if( $search = $request->search ) {
            $_states->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
        
        if( $request->has(['field', 'order']) ) {
            $_states->orderBy($request->field, $request->order);
        }
        
        $states = $_states->with('entites')->paginate(10, ['*'], 'page', $request->page ?? 1);
        
        return response()->json($states);
    }
}
