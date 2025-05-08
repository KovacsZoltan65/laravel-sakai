<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexWorktimeLimitRequest;
use App\Models\WorktimeLimit;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

class WorktimeLimitController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('WorktimeLimit/Index',[
            'title' => 'Worktime Limits',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(IndexWorktimeLimitRequest $request): JsonResponse
    {
        $_limits = WorktimeLimit::query();

        if( $search = $request->search ) {
            $_limits->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if( $request->has(['field', 'order']) ) {
            $_limits->orderBy($request->field, $request->order);
        }

        $limits = $_limits->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json(data: $limits);
    }
}
