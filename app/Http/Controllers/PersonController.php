<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PersonController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request): InertiaResponse
    {
\Log::info('PersonController::index()');
        return Inertia::render(component: 'Persons/Index', props: [
            //'filters' => $request->all(['search', 'field', 'order']),
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
        $_persons = Person::query();

        if( $request->has(key: 'search') ) {
            $_persons->where(column: 'name', operator: 'LIKE', value: "%{$request->search}%");
            $_persons->orWhere(column: 'email', operator: 'LIKE', value: "%{$request->search}%");
            $_persons->orWhere(column: 'address', operator: 'LIKE', value: "%{$request->search}%");
            $_persons->orWhere(column: 'phone', operator: 'LIKE', value: "%{$request->search}%");
        }

        if( $request->has(['field', 'order']) ) {
            $_persons->orderBy(column: $request->field, direction: $request->order);
        }

        $persons = $_persons->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: null);

\Log::info('$_persons: ' . print_r($_persons, true));
\Log::info('$persons: ' . print_r($persons, true));

        $retval = [
            'data' => $persons,
            'title' => 'Persons',
            'filters' => $request->all(['search', 'field', 'order']),
            'pagination' => [
                'current_page' => '',
                'per_page' => '',
                'total' => '',
                'last_page' => '',
            ],
        ];

        return response()->json($retval, Response::HTTP_OK);
    }
}
