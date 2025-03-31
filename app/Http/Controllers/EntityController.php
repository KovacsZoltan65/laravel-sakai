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
}
