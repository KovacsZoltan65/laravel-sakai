<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubdomainIndexRequest;
use App\Models\SubdomainState;
use Illuminate\Http\Request;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;

class SubdomainController extends Controller
{
    public function __construct()
    {
        //
    }
    
    public function index(SubdomainIndexRequest $request)
    {
        $states = SubdomainState::all();
        
        return \Inertia\Inertia::render('Subdomain/Index', [
            'title' => 'Subdomains',
            'filters' => $request->all(['search', 'field', 'order']),
            'states' => $states,
        ]);
    }
    
    public function fetch(Request $request): JsonResponse
    {
        $_subdomains = State::query();
        
        if( $search = $request->search ) {
            $_subdomains->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        }
        
        if( $request->has(['field', 'order']) ) {
            $_subdomains->orderBy($request->field, $request->order);
        }
        
        $subdomains = $_subdomains->with('subdomainState')->paginate(10, ['*'], 'page', $request->page ?? 1);
        
        return response()->json($subdomains);
    }
    
    public function getSubdomain(GetSubdomainRequest $request)
    {
        try {
            //
        } catch( ModelNotFoundException $ex ) {
            //
        } catch( QueryException $ex ) {
            //
        } catch( Exception $ex ) {
            //
        }
    }
}
