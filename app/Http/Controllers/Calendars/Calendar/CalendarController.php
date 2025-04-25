<?php

namespace App\Http\Controllers\Calendars\Calendar;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCalendarRequest;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        return Inertia::render('Calendars/Calendar/Index', [
            'title' => 'Calendar',
            'filters' => $request->all(['search', 'field', 'order']),
        ]);
        /*
        $query = Calendar::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('field') && $request->has('order')) {
            $query->orderBy($request->get('field'), $request->get('order'));
        }

        $items = $query->paginate(10);

        return CalendarResource::collection($items);
        */
    }

    public function fetch(IndexCalendarRequest $request)
    {
        $_calendar = Calendar::query();

        if($search = $request->search) {
            $_calendar->where('date', 'like', "%{$search}%");
        }

        if($request->has(['field', 'order'])) {
            $_calendar->orderBy($request->field, $request->order);
        }

        $calendar = $_calendar->paginate(10, ['*'], 'page', $request->page ?? 1);
        return response()->json($calendar);
    }

    public function store(StoreCalendarRequest $request)
    {
        $item = Calendar::create($request->validated());
        return new CalendarResource($item);
    }

    public function update(UpdateCalendarRequest $request, Calendar $calendar)
    {
        $calendar->update($request->validated());
        return new CalendarResource($calendar);
    }

    public function destroy(Calendar $calendar)
    {
        $calendar->delete();
        return response()->noContent();
    }
}