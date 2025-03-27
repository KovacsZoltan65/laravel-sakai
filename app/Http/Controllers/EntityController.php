<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntityIndexRequest;
use App\Http\Resources\EntityResource;
use App\Models\Company;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;

class EntityController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(EntityIndexRequest $request): InertiaResponse
    {
        // Lekéri az összes entitást az adatbázisból
        // az Eloquent lekérdezéskészítő használatával.
        $_entities = Entity::query();

        // Ha a kérés keresési paramétert tartalmaz, 
        // szűrje le az entitásokat a keresési paraméter segítségével
        if ($search = $request->search) {
            $_entities->whereRaw(sql: "(name LIKE ? OR email LIKE ?)", bindings: [
                "%{$search}%",
                "%{$search}%",
            ]);
        }

        // Ellenőrizze, hogy a kérés tartalmaz-e „mező” és „rendelés” paramétereket
        // és ennek megfelelően rendezi az entitásokat
        if ($request->has(key: ['field', 'order'])) {
            $_entities->orderBy(column: $request->field, direction: $request->order);
        }

        $page = $request->page ?? 1;

        //$entities = $_entities->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page);

        //$entityResource = EntityResource::collection(resource: $entities);

        $companies = Company::active()->get()->select('name', 'id')->toArray();

        $entities = $_entities->with('company')->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page);

        $params = [
            'title' => 'Entity',
            'filters' => $request->all(['search', 'field', 'order']),
            'entities' => $entities,
            'companies' => $companies,
            /*
            'pagination' => [
                'current_page' => $entities->currentPage(),
                'per_page' => $entities->perPage(),
                'total' => $entities->total(),
                'last_page' => $entities->lastPage(),
            ],
            */
        ];

        return Inertia::render(component: 'Entity/Index', props: $params);
    }
}
