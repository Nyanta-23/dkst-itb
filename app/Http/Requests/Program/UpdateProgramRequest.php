<?php

namespace App\Http\Requests\Program;

use App\Models\Program;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProgramRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('program')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'pic_id' => ['nullable', 'integer', 'exists:users,id'],
            'code' => ['required', 'string', 'max:255', Rule::unique('programs', 'code')->ignore($this->route('program'))],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'budget' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:'.implode(',', Program::STATUSES)],

            'indicators' => ['required', 'array', 'min:1'],
            'indicators.*.id' => ['nullable', 'integer', 'exists:program_indicators,id'],
            'indicators.*.name' => ['required', 'string', 'max:255'],
            'indicators.*.is_iku' => ['boolean'],
            'indicators.*.iku_code' => ['required_if:indicators.*.is_iku,true', 'nullable', 'string', 'max:255'],
            'indicators.*.target' => ['required', 'numeric', 'min:0'],
            'indicators.*.measurement_unit' => ['required', 'string', 'max:30'],
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
            'unit_id' => 'unit',
            'pic_id' => 'PIC',
            'code' => 'kode program',
            'name' => 'nama program',
            'description' => 'deskripsi',
            'year' => 'tahun',
            'budget' => 'anggaran',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'status' => 'status',
            'indicators' => 'indikator',
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
            'integer' => ':attribute tidak valid.',
            'numeric' => ':attribute harus berupa angka.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'unique' => ':attribute sudah digunakan.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'after_or_equal' => ':attribute tidak boleh sebelum tanggal mulai.',
            'in' => ':attribute yang dipilih tidak valid.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
            'indicators.required' => 'Minimal harus ada satu indikator.',
            'indicators.min' => 'Minimal harus ada satu indikator.',
            'indicators.*.name.required' => 'Nama indikator wajib diisi.',
            'indicators.*.target.required' => 'Target indikator wajib diisi.',
            'indicators.*.measurement_unit.required' => 'Satuan indikator wajib diisi.',
            'indicators.*.iku_code.required_if' => 'Kode IKU wajib diisi untuk indikator IKU.',
        ];
    }
}
