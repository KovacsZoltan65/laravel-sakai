<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntityIndexRequest;
use App\Http\Requests\GetEntityRequest;
use App\Http\Requests\StoreEntityRequest;
use App\Models\Company;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Exception;

class EntityController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(EntityIndexRequest $request): InertiaResponse
    {
        $companies = Company::active()->select('name', 'id')->get();

        return Inertia::render('Entity/Index', [
            'title' => 'Entities',
            'filters' => $request->all(['search', 'field', 'order']),
            'companies' => $companies,
        ]);
    }

    public function fetch(Request $request)
    {
        $_entities = Entity::query();

        if ($search = $request->search) {
            $_entities->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has(['field', 'order'])) {
            $_entities->orderBy($request->field, $request->order);
        }

        $entities = $_entities->with('company')->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($entities);
    }

    public function getEntity(GetEntityRequest $request)
    {
        try {
            $entity = Entity::findOrFail($request->id);

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getEntity ModelNotFoundException: ' . print_r(value: $ex, return: true));
        } catch( QueryException $ex ) {
            \Log::info(message: 'getEntity QueryException: ' . print_r(value: $ex, return: true));
        } catch( Exception $ex ) {
            \Log::info(message: 'getEntity Exception: ' . print_r(value: $ex, return: true));
        }
    }

    public function getEntityByName(string $name)
    {
        try {
            $entity = Entity::where('name', '=', $name)->firstOrFail();

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('getEntityByName ModelNotFoundException: ' . print_r($ex, true));
        } catch( QueryException $ex ) {
            \Log::info('getEntityByName QueryException: ' . print_r($ex, true));
        } catch( Exception $ex ) {
            \Log::info('getEntityByName Exception: ' . print_r($ex, true));
        }
    }

    public function createEntity(StoreEntityRequest $request): Entity
    {
        try {
            $entity = null;

            DB::transaction(function() use($request, &$entity) {
                // 1. Entitás létrehozása
                $entity = Entity::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($entity);

                // 3. Cache törlése, ha releváns

            });

            return response()->json($entity, Response::HTTP_CREATED);
        } catch( QueryException $ex ) {
            \Log::info('createEntity QueryException: ' . print_r($ex, true));
            throw $ex;
        } catch( Exception $ex ) {
            \Log::info('createEntity Exception: ' . print_r($ex, true));
            throw $ex;
        }
    }

    public function updateEntity(StoreEntityRequest $request, int $id): ?Entity
    {
        try {
            $entity = null;

            DB::transaction(function() use($request, $id, &$company) {
                $entity = Entity::lockForUpdate()->findOrFail($id);
                $entity->update($request->all());
                $entity->refresh();

                // 3. Cache törlése, ha releváns

            });

            return response()->json($entity, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('updateEntity ModelNotFoundException: ' . print_r($ex, true));
            throw $ex;
        } catch( Exception $ex ) {
            \Log::info('updateEntity Exception: ' . print_r($ex, true));
            throw $ex;
        }
    }

    public function deleteEntities(Request $request): int
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array|min:1', // Kötelező, legalább 1 id kell
                'ids.*' => 'integer|exists:companies,id', // Az id-k egész számok és létező cégek legyenek
            ]);

            $ids = $validated['ids'];
            $deletedCount = 0;

            DB::transaction(function () use ($ids, &$deletedCount) {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $deletedCount = Entity::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$entities = Entity::whereIn('id', $ids)->lockForUpdate()->get();
                //$entities->each(function (Entity $entity) use (&$deletedCount) {
                //    if ($entity->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges
            });

            return response()->json($deletedCount, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteEntities ModelNotFoundException: ' . print_r($ex, true));
            throw $ex;
        } catch( Exception $ex ) {
            \Log::info('deleteEntities Exception: ' . print_r($ex, true));
            throw $ex;
        }
    }

    public function deleteEntity(GetEntityRequest $request): JsonResponse
    {
        try {
            $entity = null;

            DB::transaction(function() use($request, &$entity) {
                $entity = Entity::lockForUpdate()->findOrFail($request->id);
                $entity->delete();

                // Cache törlése, ha szükséges
            });

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteEntity ModelNotFoundException: ' . print_r($ex, true));
            throw $ex;
        } catch( QueryException $ex ) {
            \Log::info('deleteEntity QueryException: ' . print_r($ex, true));
            throw $ex;
        } catch( Exception $ex ) {
            \Log::info('deleteEntity Exception: ' . print_r($ex, true));
            throw $ex;
        }
    }

    private function createDefaultSettings(Entity $entity): void
    {
        //
    }
}
