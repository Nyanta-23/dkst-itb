<?php

namespace App\Http\Requests\Teknologi;

use App\Models\Deal;
use Illuminate\Foundation\Http\FormRequest;

class StoreDealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Deal::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'partner_id' => ['required', 'integer', 'exists:partners,id'],
            'type' => ['required', 'string', 'in:'.implode(',', Deal::TYPES)],
            'value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:'.implode(',', Deal::STATUSES)],
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
            'partner_id' => 'mitra',
            'type' => 'jenis transaksi',
            'value' => 'nilai transaksi',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'status' => 'status',
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
            'date' => ':attribute harus berupa tanggal yang valid.',
            'after_or_equal' => ':attribute tidak boleh sebelum tanggal mulai.',
            'in' => ':attribute yang dipilih tidak valid.',
            'min' => ':attribute tidak boleh negatif.',
        ];
    }
}
