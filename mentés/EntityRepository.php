<?php

namespace App\Repositories;

use App\Interfaces\EntityRepositoryInterface;
use App\Models\Entity;
use Exception;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class EntityRepository extends BaseRepository implements EntityRepositoryInterface
{
    public function getEntities(Request $request)
    {
        try {
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

            $entities = $_entities->with('company')->paginate(perPage: 10, columns: ['*'], pageName: 'page', page: $page);

            return $entities;

        } catch ( Exception $ex ) {
            throw $ex;
        }
    }
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Entity::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}