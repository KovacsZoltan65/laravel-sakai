<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Repositories\PersonRepository;
use App\Http\Resources\PersonResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Exception;

class PersonController extends Controller
{
    protected PersonRepository $personRepository;

    public function __construct(PersonRepository $personRepository)
    {
        /*
         | read permission, 
         | create permission, 
         | update permission
         | delete permission
         */
        $this->personRepository = $personRepository;
        
        //$this->middleware('permission:read person', ['only' => ['index', 'show', 'fetch', 'getPerson', 'getPersonByName']]);
        //$this->middleware('permission:create person', ['only' => ['storePerson']]);
        //$this->middleware('permission:update person', ['only' => ['updatePerson', 'restorePerson']]);
        //$this->middleware('permission:delete person', ['only' => ['deletePersons', 'deletePerson', 'realDeletePerson']]);
    }
    
    public function index(Request $request): InertiaResponse
    {
\Log::info('PersonController::index()');

\Log::info('request->all(): ' . print_r($request->all(), true));

        return Inertia::render(component: 'Persons/Index', props: [
            'filters' => $request->all(['search', 'field', 'order']),
            'title' => 'Persons',
        ]);
    }

    public function applySearch(Builder $query, string $search): Builder
    {
        return $query->when($search, function (Builder $query, string $search) {
            $query->where('name', 'like', "%{$search}%");
        });
    }

    public function getPersons(Request $request)
    {
        try {
            $_persons = $this->personRepository->getPersons($request);
            $personResource = PersonResource::collection($_persons);

            $retval = [
                'data' => $personResource->items(),
                'title' => 'Persons',
                'filters' => $request->all(['search', 'field', 'order']),
                'pagination' => [
                    'current_page' => $_persons->currentPage(),
                    'per_page' => $_persons->perPage(),
                    'total' => $_persons->total(),
                    'last_page' => $_persons->lastPage(),
                ],
            ];
        } catch( QueryException $ex ) {
            \Log::info('getPersons QueryException: ' . print_r($ex->getMessage(), true));
        } catch( Exception $ex ) {
            \Log::info('getPersons Exception: ' . print_r($ex->getMessage(), true));
        }

        return response()->json($retval, Response::HTTP_OK);
    }
}
