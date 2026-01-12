<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAttendanceCorrectionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'clock_in'  => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'reason'    => ['required'],

            'break_start'   => ['nullable', 'array'],
            'break_start.*' => ['required', 'date_format:H:i', 'before:clock_out'],

            'break_end'   => ['nullable', 'array'],
            'break_end.*' => ['required', 'date_format:H:i', 'after:break_start.*', 'before:clock_out'],
        ];
    }

    public function messages()
    {
        return [
            // 出勤・退勤
            'clock_in.required'     => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_in.date_format'  => '出勤時間もしくは退勤時間が不適切な値です',

            'clock_out.required'    => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.date_format' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.after'       => '出勤時間もしくは退勤時間が不適切な値です',

            // 休憩開始
            'break_start.*.required'    => '休憩時間が不適切な値です',
            'break_start.*.date_format' => '休憩時間が不適切な値です',
            'break_start.*.before'      => '休憩時間が不適切な値です',

            // 休憩終了
            'break_end.*.required'    => '休憩時間もしくは退勤時間が不適切な値です',
            'break_end.*.date_format' => '休憩時間もしくは退勤時間が不適切な値です',
            'break_end.*.after'       => '休憩時間もしくは退勤時間が不適切な値です',
            'break_end.*.before'      => '休憩時間もしくは退勤時間が不適切な値です',

            // 備考
            'reason.required' => '備考を記入してください',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
