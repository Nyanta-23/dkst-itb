<?php

namespace App\Http\Requests\Teknologi;

use App\Models\IpAsset;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIpAssetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('ipAsset')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:'.implode(',', IpAsset::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'application_number' => ['nullable', 'string', 'max:255'],
            'certificate_number' => ['nullable', 'string', 'max:255'],
            'filing_date' => ['nullable', 'date'],
            'issue_date' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:'.implode(',', IpAsset::STATUSES)],
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
            'type' => 'jenis KI',
            'title' => 'judul',
            'application_number' => 'nomor permohonan',
            'certificate_number' => 'nomor sertifikat',
            'filing_date' => 'tanggal daftar',
            'issue_date' => 'tanggal terbit',
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
            'date' => ':attribute harus berupa tanggal yang valid.',
            'in' => ':attribute yang dipilih tidak valid.',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }
}
