<?php

namespace App\Http\Controllers\Subdomains;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteSubdomainStateRequest;
use App\Http\Requests\StoreSubdomainStateRequest;
use App\Http\Requests\IndexSubdomainStateRequest;
use App\Http\Requests\UpdateSubdomainStateRequest;
use App\Models\Subdomain;
use App\Models\SubdomainState as State;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function response;

class SubdomainStateController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(IndexSubdomainStateRequest $request)
    {
        $subdomains = Subdomain::all();

        return Inertia::render('Subdomains/States/Index', [
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

        $states = $_states->with('subdomains')
            ->withCount('subdomains')
            ->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($states, Response::HTTP_OK);
    }

    public function getSubdomainState(DeleteSubdomainStateRequest $request): JsonResponse
    {
        try {
            $state = State::with(['subdomains'])->findOrFail($request->id);

            return response()->json($state, Response::HTTP_OK);

        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getSubdomainState ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainState Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getSubdomainState QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainState Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getSubdomainState Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainState Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSubdomainStateByName(string $name): JsonResponse
    {
        try {
            $state = State::where('name', '=', $name)->firstOrFail();

            return response()->json($state, Response::HTTP_OK);

        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getSubdomainStateByName ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainStateByName Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getSubdomainStateByName QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainStateByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getSubdomainStateByName Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getSubdomainStateByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeSubdomainState(StoreSubdomainStateRequest $request): JsonResponse
    {
        try {
            $state = DB::transaction(function() use($request): Subdomain {
                // 1. Entitás létrehozása
                $_state = State::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_state);

                // 3. Cache törlése, ha releváns

                return $_state;
            });

            return response()->json($state, Response::HTTP_OK);

        } catch( QueryException $ex ) {
            \Log::info('storeSubdomainState QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'storeSubdomainState Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('storeSubdomainState Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'storeSubdomainState Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateSubdomainState(UpdateSubdomainStateRequest $request, int $id): JsonResponse
    {
        try {

            $state = DB::transaction(function() use($request, $id): Subdomain {
                $_state = State::lockForUpdate()->findOrFail($id);
                $_state->update($request->all());
                $_state->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_state);

                // 4. Cache törlése, ha releváns

                return $_state;

            });

            return response()->json($state, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('updateSubdomain ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateSubdomain City not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateSubdomain QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateSubdomain Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('updateSubdomain Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateSubdomain Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteSubdomainStates(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:subdomain_states,id', // Az id-k egész számok és létező aldomain legyenek
            ]);

            $ids = $validated['ids'];

            $deletedCount = DB::transaction(function () use ($ids): int {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = State::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$_states = State::whereIn('id', $ids)->lockForUpdate()->get();
                //$_states->each(function (State $state) use (&$deletedCount) {
                //    if ($state->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteSubdomainStates ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomainStates Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( QueryException $ex ) {
            \Log::info('deleteSubdomainStates QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomainStates Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteSubdomainStates Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomainStates Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteSubdomainState(DeleteSubdomainStateRequest $request): JsonResponse
    {
        try {

            $state = DB::transaction(function() use($request): Satate {
                $_state = State::lockForUpdate()->findOrFail($request->id);
                $_state->delete();

                // Cache törlése, ha szükséges

                return $_state;
            });

            return response()->json($state, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteSubdomainState ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomainState City not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info('deleteSubdomainState QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomainState Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteSubdomainState Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteSubdomainState Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createDefaultSettings(Subdomain $subdomain): void
    {
        //
    }

    private function updateDefaultSettings(Subdomain $subdomain): void
    {
        //
    }

}
