<?php

namespace App\Http\Requests\Program;

use App\Models\IndicatorRealization;
use App\Models\ProgramIndicator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIndicatorRealizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', [IndicatorRealization::class, $this->route('indicator')]) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var ProgramIndicator $indicator */
        $indicator = $this->route('indicator');
        $year = $indicator->program->year;

        return [
            'period' => ['required', 'string', 'in:'.$year.'-Q1,'.$year.'-Q2,'.$year.'-Q3,'.$year.'-Q4'],
            'actual_value' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'evidence' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'period' => 'periode',
            'actual_value' => 'nilai realisasi',
            'notes' => 'catatan',
            'evidence' => 'bukti',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => [
                'actual_value' => ':attribute tidak boleh negatif.',
                'string' => ':attribute minimal :min karakter.',
            ],
            'max' => [
                'notes' => ':attribute maksimal :max karakter.',
                'file' => 'Ukuran :attribute maksimal 5 MB.',
            ],
            'in' => 'Periode harus salah satu dari Q1–Q4 tahun program ini.',
            'file' => ':attribute harus berupa berkas.',
            'mimes' => ':attribute harus berformat PDF, JPG, JPEG, atau PNG.',
        ];
    }

    /**
     * Get the "after" validation callables for the request.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['period'])) {
                    return;
                }

                /** @var ProgramIndicator $indicator */
                $indicator = $this->route('indicator');

                $existing = IndicatorRealization::query()
                    ->where('indicator_id', $indicator->id)
                    ->where('period', $this->string('period')->toString())
                    ->first();

                if ($existing && $existing->status !== 'rejected') {
                    $validator->errors()->add('period', 'Realisasi untuk periode ini sudah pernah diajukan.');
                }
            },
        ];
    }
}
