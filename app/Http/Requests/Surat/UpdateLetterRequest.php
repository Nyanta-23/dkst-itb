<?php

namespace App\Http\Requests\Surat;

use App\Models\Letter;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLetterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('letter')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:'.implode(',', Letter::TYPES)],
            'letter_number' => ['required', 'string', 'max:255'],
            'agenda_number' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'sender' => ['required', 'string', 'max:255'],
            'recipient' => ['required', 'string', 'max:255'],
            'letter_date' => ['required', 'date'],
            'received_date' => ['nullable', 'date'],
            'classification' => ['required', 'string', 'in:'.implode(',', Letter::CLASSIFICATIONS)],
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
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
            'type' => 'jenis surat',
            'letter_number' => 'nomor surat',
            'agenda_number' => 'nomor agenda',
            'subject' => 'perihal',
            'sender' => 'pengirim',
            'recipient' => 'penerima',
            'letter_date' => 'tanggal surat',
            'received_date' => 'tanggal diterima',
            'classification' => 'sifat surat',
            'file' => 'berkas surat',
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
            'file' => ':attribute harus berupa berkas.',
            'mimes' => ':attribute harus berformat PDF.',
            'max' => [
                'file' => 'Ukuran :attribute maksimal 5 MB.',
                'string' => ':attribute maksimal :max karakter.',
            ],
        ];
    }
}
