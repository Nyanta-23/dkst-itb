<?php

namespace App\Http\Requests\Teknologi;

use App\Models\Technology;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTechnologyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('technology')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('technologies', 'code')->ignore($this->route('technology'))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sector' => ['nullable', 'string', 'max:255'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'inventors' => ['nullable', 'string'],
            'trl' => ['required', 'integer', 'min:1', 'max:9'],
            'commercialization_status' => ['required', 'string', 'in:'.implode(',', Technology::COMMERCIALIZATION_STATUSES)],
            'pic_id' => ['nullable', 'integer', 'exists:users,id'],
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
            'code' => 'kode teknologi',
            'title' => 'judul',
            'description' => 'deskripsi',
            'sector' => 'bidang',
            'faculty' => 'fakultas',
            'inventors' => 'inventor',
            'trl' => 'TRL',
            'commercialization_status' => 'status komersialisasi',
            'pic_id' => 'PIC',
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
            'integer' => ':attribute harus berupa angka.',
            'unique' => ':attribute sudah digunakan.',
            'in' => ':attribute yang dipilih tidak valid.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute maksimal :max.',
            'exists' => ':attribute yang dipilih tidak ditemukan.',
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
                if ($validator->errors()->has('pic_id') || ! $this->filled('pic_id')) {
                    return;
                }

                $dttUnitId = Unit::where('code', 'DTT')->value('id');
                $pic = User::find($this->integer('pic_id'));

                if ($pic && $pic->unit_id !== $dttUnitId) {
                    $validator->errors()->add('pic_id', 'PIC harus merupakan user dari unit Subdit Data dan Teknologi Informasi (DTT).');
                }
            },
        ];
    }
}
