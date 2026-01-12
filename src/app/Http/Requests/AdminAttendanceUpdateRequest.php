<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules()
    {
        $rules = [
            'clock_in'  => [
                'required',
                'date_format:H:i',
            ],
            'clock_out' => [
                'required',
                'date_format:H:i',
            ],
            'reason'    => 'required|string|max:255',
        ];

        // 出勤・退勤の前後チェック
        $rules['clock_in'][] = function ($attr, $value, $fail) {
            $clockOut = $this->input('clock_out');

            if ($value && $clockOut) {
                $ci = Carbon::createFromFormat('H:i', $value);
                $co = Carbon::createFromFormat('H:i', $clockOut);

                if ($ci->gte($co)) {
                    $fail('出勤時間もしくは退勤時間が不適切な値です');
                }
            }
        };

        // 休憩時間
        foreach ($this->input('break_times', []) as $i => $break) {

            // 休憩開始
            $rules["break_times.$i.start_time"] = [
                'required',
                'date_format:H:i',
                function ($attr, $value, $fail) {
                    $ci = Carbon::createFromFormat('H:i', $this->input('clock_in'));
                    $co = Carbon::createFromFormat('H:i', $this->input('clock_out'));
                    $start = Carbon::createFromFormat('H:i', $value);

                    if ($start->lt($ci) || $start->gt($co)) {
                        $fail('休憩時間が不適切な値です');
                    }
                },
            ];

            // 休憩終了
            $rules["break_times.$i.end_time"] = [
                'nullable',
                'date_format:H:i',
                function ($attr, $value, $fail) use ($i) {
                    if (!$value) return;

                    $start = Carbon::createFromFormat(
                        'H:i',
                        $this->input("break_times.$i.start_time")
                    );
                    $end = Carbon::createFromFormat('H:i', $value);
                    $co = Carbon::createFromFormat('H:i', $this->input('clock_out'));

                    if ($end->lte($start) || $end->gt($co)) {
                        $fail('休憩時間もしくは退勤時間が不適切な値です');
                    }
                },
            ];
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'clock_in.required'  => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_in.date_format'  => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です',

            'reason.required' => '備考を記入してください',
            'reason.max'      => '備考を記入してください',
        ];
    }
}
