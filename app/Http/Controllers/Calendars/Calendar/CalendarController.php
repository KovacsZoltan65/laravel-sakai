<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCalendarRequest;
use App\Http\Requests\UpdateCalendarRequest;
use App\Http\Resources\CalendarResource;
use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $query = Calendar::query();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('field') && $request->has('order')) {
            $query->orderBy($request->get('field'), $request->get('order'));
        }

        $items = $query->paginate(10);

        return CalendarResource::collection($items);
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