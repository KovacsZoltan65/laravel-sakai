<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexShiftRequest;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShiftController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Shift/Index', [
            'title' => 'Shifts',
            'filter' => $request->all(['search', 'field', 'order']),
        ]);
    }

    public function fetch(IndexShiftRequest $request): JsonResponse
    {
        $_shifts = Shift::query();

        if ($search = $request->search) {
            $_shifts->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has(['field', 'order'])) {
            $_shifts->orderBy($request->field, $request->order);
        }

        $shifts = $_shifts->with('company')->paginate(10, ['*'], 'page', $request->page ?? 1);

        return response()->json($shifts);
    }
}
