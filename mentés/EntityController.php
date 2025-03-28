<?php

namespace App\Http\Controllers;

use App\Http\Requests\EntityIndexRequest;
use App\Http\Resources\EntityResource;
use App\Models\Company;
use App\Models\Entity;
use App\Repositories\EntityRepository;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use Inertia\Response AS InertiaResponse;

class EntityController extends Controller
{
    protected EntityRepository $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    public function index(EntityIndexRequest $request): InertiaResponse
    {
        // Lekéri az összes entitást az adatbázisból
        // az Eloquent lekérdezéskészítő használatával.
        //$_entities = Entity::query();

        // Ha a kérés keresési paramétert tartalmaz, 
        // szűrje le az entitásokat a keresési paraméter segítségével
        //if ($search = $request->search) {
        //    $_entities->whereRaw(sql: "(name LIKE ? OR email LIKE ?)", bindings: [
        //        "%{$search}%",
        //        "%{$search}%",
        //    ]);
        //}

        // Ellenőrizze, hogy a kérés tartalmaz-e „mező” és „rendelés” paramétereket
        // és ennek megfelelően rendezi az entitásokat
        //if ($request->has(key: ['field', 'order'])) {
        //    $_entities->orderBy(column: $request->field, direction: $request->order);
        //}

        //$page = $request->page ?? 1;

        $companies = Company::active()->get()->select('name', 'id')->toArray();

        //$entities = $_entities->with('company')->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page);

        $params = [
            'title' => 'Entity',
            'filters' => $request->all(['search', 'field', 'order']),
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

    public function getEntities(Request $request)
    {
        try {
            $entities = $this->entityRepository->getEntities($request);

// Visszaadja a jelenlegi oldal számát.
//\Log::info( 'currentPage: ' . print_r($entities->currentPage(), true) );
// Megadja az aktuális oldalon szereplő első elem indexét (vagy null, ha nincs elem).
//\Log::info( 'firstItem: ' . print_r($entities->firstItem(), true) );
// Az aktuális oldalon szereplő utolsó elem indexét adja vissza.
//\Log::info( 'lastItem: ' . print_r($entities->lastItem(), true) );
// Megadja, hogy hány elem található az aktuális oldalon.
//\Log::info( 'count: ' . print_r($entities->count(), true) );
// Lekérdezi, hogy hány elem kerül megjelenítésre oldalanként.
//\Log::info( 'perPage: ' . print_r($entities->perPage(), true) );
// Az összes elem számát adja vissza az adatbázisból.
//\Log::info( 'total: ' . print_r($entities->total(), true) );
// Ellenőrzi, hogy van-e további oldal (true, ha igen).
//\Log::info( 'hasMorePages: ' . print_r($entities->hasMorePages(), true) );
// A következő oldal URL-jét adja vissza, ha van; különben null-t.
//\Log::info( 'nextPageUrl: ' . print_r($entities->nextPageUrl(), true) );
// Az előző oldal URL-jét adja vissza, ha elérhető; különben null-t.
//\Log::info( 'previousPageUrl: ' . print_r($entities->previousPageUrl(), true) );
//\Log::info( 'entities: ' . print_r($entities, true) );
            //return response()->json($entities, Response::HTTP_OK);
            $data = [
                'entities' => $entities,
                'pagination' => [
                    'current_page' => $entities->currentPage(),
                    'per_page' => $entities->perPage(),
                    'total' => $entities->total(),
                    'last_page' => $entities->lastPage(),
                ],
            ];

//\Log::info( 'data: ' . print_r($data, true) );

            //return $entities;
            return response()->json($data, Response::HTTP_OK);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
