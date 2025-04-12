<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteEntityRequest;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\Company;
use App\Models\Entity;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use function response;

class EntityController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(EntityRequest $request): InertiaResponse
    {
        $companies = Company::active()->select('name', 'id')->get();

        return Inertia::render('Entity/Index', [
            'title' => 'Entities',
            'filters' => $request->all(['search', 'field', 'order']),
            'companies' => $companies,
        ]);
    }

    public function fetch(Request $request): JsonResponse
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

    public function getEntity(Request $request): JsonResponse
    {
        try {
            $entity = Entity::with(['users', 'companies'])
                ->withCount(['users', 'companies'])
                ->findOrFail($request->id);

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info(message: 'getEntity ModelNotFoundException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEntity Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info(message: 'getEntity QueryException: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEntity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info(message: 'getEntity Exception: ' . print_r(value: $ex, return: true));
            return response()->json(['error' => 'getEntity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getEntityByName(string $name): JsonResponse
    {
        try {
            $entity = Entity::where('name', '=', $name)->firstOrFail();

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('getEntityByName ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'getEntityByName Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info('getEntityByName QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'getEntityByName Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('getEntityByName Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'getEntityByName Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function storeEntity(StoreEntityRequest $request): Entity
    {
        try {

            $entity = DB::transaction(function() use($request): Entity {
                // 1. Entitás létrehozása
                $_entity = Entity::create($request->all());

                // 2. Kapcsolódó rekordok létrehozása (pl. alapértelmezett beállítások)
                $this->createDefaultSettings($_entity);

                // 3. Cache törlése, ha releváns

                return $_entity;
            });

            return response()->json($entity, Response::HTTP_CREATED);
        } catch( QueryException $ex ) {
            \Log::info('storeEntity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'storeEntity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('storeEntity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'storeEntity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateEntity(UpdateEntityRequest $request, int $id): ?Entity
    {
        try {

            $entity = DB::transaction(function() use($request, $id): Entity {
                $_entity = Entity::lockForUpdate()->findOrFail($id);
                $_entity->update($request->all());
                $_entity->refresh();

                // 3. Kapcsolódó rekordok frissítése (pl. alapértelmezett beállítások)
                $this->updateDefaultSettings($_entity);

                // 4. Cache törlése, ha releváns

                return $_entity;

            });

            return response()->json($entity, Response::HTTP_CREATED);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('updateEntity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateEntity Country not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('updateEntity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'updateEntity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('updateEntity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'updateEntity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $deletedCount = DB::transaction(function () use ($ids) {
                // 1. Törlés - válaszd az egyik verziót:
                // a) Observer nélküli, gyors SQL törlés:
                $count = Entity::whereIn('id', $ids)->delete();

                // b) Observer-kompatibilis, egyenkénti törlés:
                //$entities = Entity::whereIn('id', $ids)->lockForUpdate()->get();
                //$entities->each(function (Entity $entity) use (&$deletedCount) {
                //    if ($entity->delete()) {
                //        $deletedCount++;
                //    }
                //});

                // Cache törlése, ha szükséges

                return $count;
            });

            return response()->json($deletedCount, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteEntities ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteEntities Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteEntities Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteEntities Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteEntity(DeleteEntityRequest $request): JsonResponse
    {
        try {

            $entity = DB::transaction(function() use($request): Entity {
                $_entity = Entity::lockForUpdate()->findOrFail($request->id);
                $_entity->delete();

                // Cache törlése, ha szükséges

                return $_entity;
            });

            return response()->json($entity, Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::info('deleteEntity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteEntity Entity not found'], Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::info('deleteEntity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteEntity Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::info('deleteEntity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'deleteEntity Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restoreEntity(GetEntityRequest $request): JsonResponse
    {
        try {

            $entity = DB::transaction(function () use ($request): Entity {
                // Soft-deleted ország lekérése
                $_entity = Entity::withTrashed()->findOrFail($request->id);

                // Visszaállítás
                $_entity->restore();

                // Friss adat betöltése
                $_entity->refresh();

                return $_entity;
            });

            return response()->json($entity, Response::HTTP_OK);

        } catch (ModelNotFoundException $ex) {
            \Log::error('restoreEntity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreEntity Entity not found'], Response::HTTP_NOT_FOUND);
        } catch (QueryException $ex) {
            \Log::error('restoreEntity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreEntity Database error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (Exception $ex) {
            \Log::error('restoreEntity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'restoreEntity Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function realDeleteEntity(GetEntityRequest $request): JsonResponse
    {
        try {

            $country = DB::transaction(function()use($request): Entity {
                // 1. Ország keresése
                $_country = Entity::withTrashed()->lockForUpdate()->findOrFail($request->id);
                // 2. Ország véglegesen törlése
                $_country->forceDelete();

                return $_country;
            });

            return response()->json($country,  Response::HTTP_OK);
        } catch( ModelNotFoundException $ex ) {
            \Log::error('realDeleteEntity ModelNotFoundException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteEntity Entity not found'],  Response::HTTP_NOT_FOUND);
        } catch( QueryException $ex ) {
            \Log::error('realDeleteEntity QueryException: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteEntity Database error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch( Exception $ex ) {
            \Log::error('realDeleteEntity Exception: ' . print_r($ex, true));
            return response()->json(['error' => 'realDeleteEntity Internal server error'],  Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createDefaultSettings(Entity $entity): void
    {
        //
    }

    private function updateDefaultSettings(Entity $country): void
    {
        //
    }
}
