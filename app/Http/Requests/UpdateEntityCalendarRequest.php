<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntityCalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'entity_id'      => 'sometimes|exists:entities,id',
            'year'           => 'sometimes|integer|min:2000|max:2100',
            'month'          => 'sometimes|integer|min:1|max:12',
            'calendar_data'  => 'sometimes|array',
        ];

        foreach (range(1, 31) as $day) {
            $key = str_pad($day, 2, '0', STR_PAD_LEFT);

            $rules["calendar_data.$key"] = 'nullable|array';

            $rules["calendar_data.$key.shift_id"] = 'nullable|integer|exists:shifts,id';

            $rules["calendar_data.$key.planned_shift.start"] = 'nullable|date_format:H:i';
            $rules["calendar_data.$key.planned_shift.end"]   = 'nullable|date_format:H:i';

            $rules["calendar_data.$key.actual_shift.start"]  = 'nullable|date_format:H:i';
            $rules["calendar_data.$key.actual_shift.end"]    = 'nullable|date_format:H:i';

            $rules["calendar_data.$key.breaks"]              = 'nullable|array';
            $rules["calendar_data.$key.breaks.*.start"]      = 'required_with:calendar_data.' . $key . '.breaks.*.end|date_format:H:i';
            $rules["calendar_data.$key.breaks.*.end"]        = 'required_with:calendar_data.' . $key . '.breaks.*.start|date_format:H:i';

            $rules["calendar_data.$key.worked_hours"]        = 'nullable|numeric|min:0|max:24';

            $rules["calendar_data.$key.leave.type"]          = 'nullable|string|in:vacation,sick_leave,holiday,training,other';
            $rules["calendar_data.$key.leave.note"]          = 'nullable|string|max:255';

            $rules["calendar_data.$key.access_log.entry"]    = 'nullable|date_format:H:i';
            $rules["calendar_data.$key.access_log.exit"]     = 'nullable|date_format:H:i';

            $rules["calendar_data.$key.overtime.start"]      = 'nullable|date_format:H:i';
            $rules["calendar_data.$key.overtime.end"]        = 'nullable|date_format:H:i';

            $rules["calendar_data.$key.note"]                = 'nullable|string|max:500';
        }

        return $rules;
    }
}
