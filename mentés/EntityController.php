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
        $companies = Company::active()
            ->select('name', 'id')
            ->get()
            ->toArray();

        $params = [
            'title' => 'Entity',
            'filters' => $request->all(['search', 'field', 'order']),
            'companies' => $companies,
        ];

        return Inertia::render(component: 'Entity/Index', props: $params);
    }

    public function getEntities(Request $request)
    {
        try {
            $entities = $this->entityRepository->getEntities($request);

            $data = [
                'entities' => $entities,
                'pagination' => [
                    'current_page' => $entities->currentPage(),
                    'per_page' => $entities->perPage(),
                    'total' => $entities->total(),
                    'last_page' => $entities->lastPage(),
                ],
            ];

            return response()->json($data, Response::HTTP_OK);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function create(Request $request)
    {
        //
    }
}
