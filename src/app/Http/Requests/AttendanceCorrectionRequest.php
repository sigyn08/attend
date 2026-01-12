<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\Attendance;


class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],
            'break_start.*' => ['nullable', 'date_format:H:i'],
            'break_end.*' => ['nullable', 'date_format:H:i'],
            'reason' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => '備考を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $attendance = Attendance::find($this->route('id'));

            if (!$attendance) {
                return;
            }

            // 退勤後に休憩がある場合はNG
            if ($attendance->clock_out && !empty($this->break_times)) {
                foreach ($this->break_times as $break) {
                    if (!empty($break['start_time']) || !empty($break['end_time'])) {
                        $validator->errors()->add(
                            'break_times',
                            '退勤後に休憩時間は入力できません'
                        );
                        break;
                    }
                }
            }
        });
    }
}
