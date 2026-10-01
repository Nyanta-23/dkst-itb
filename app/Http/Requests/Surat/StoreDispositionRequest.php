<?php

namespace App\Http\Requests\Surat;

use App\Models\Disposition;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreDispositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', [Disposition::class, $this->route('letter')]) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'to_unit_id' => ['required', 'integer', 'exists:units,id'],
            'to_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'instruction' => ['required', 'string', 'max:2000'],
            'due_date' => ['nullable', 'date'],
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
            'to_unit_id' => 'unit tujuan',
            'to_user_id' => 'user tujuan',
            'instruction' => 'instruksi',
            'due_date' => 'batas waktu',
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
            'integer' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'max' => ':attribute maksimal :max karakter.',
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
                if ($validator->errors()->hasAny(['to_unit_id', 'to_user_id']) || ! $this->filled('to_user_id')) {
                    return;
                }

                $toUser = User::find($this->integer('to_user_id'));

                if ($toUser && $toUser->unit_id !== $this->integer('to_unit_id')) {
                    $validator->errors()->add('to_user_id', 'User tujuan harus merupakan anggota dari unit tujuan.');
                }
            },
        ];
    }
}
