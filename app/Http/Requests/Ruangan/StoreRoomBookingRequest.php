<?php

namespace App\Http\Requests\Ruangan;

use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRoomBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', RoomBooking::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i', 'after_or_equal:07:00', 'before:21:00'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time', 'before_or_equal:21:00'],
            'purpose' => ['required', 'string', 'max:255'],
            'participant_count' => ['required', 'integer', 'min:1'],
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
            'room_id' => 'ruangan',
            'booking_date' => 'tanggal',
            'start_time' => 'jam mulai',
            'end_time' => 'jam selesai',
            'purpose' => 'keperluan',
            'participant_count' => 'jumlah peserta',
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
            'date_format' => ':attribute harus berupa jam yang valid (format JJ:MM).',
            'after_or_equal' => [
                'booking_date' => ':attribute tidak boleh sebelum hari ini.',
                'start_time' => ':attribute paling awal 07:00.',
            ],
            'before' => ':attribute paling akhir 21:00.',
            'before_or_equal' => ':attribute paling akhir 21:00.',
            'after' => ':attribute harus setelah jam mulai.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute maksimal :max karakter.',
            'min' => ':attribute minimal :min.',
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
                if ($validator->errors()->hasAny(['room_id', 'participant_count', 'booking_date', 'start_time', 'end_time'])) {
                    return;
                }

                $room = Room::find($this->integer('room_id'));

                if (! $room) {
                    return;
                }

                if (! $room->is_active) {
                    $validator->errors()->add('room_id', 'Ruangan ini sedang tidak aktif dan tidak dapat dipesan.');

                    return;
                }

                if ($this->integer('participant_count') > $room->capacity) {
                    $validator->errors()->add('participant_count', "Jumlah peserta melebihi kapasitas ruangan ({$room->capacity} orang).");
                }

                if (RoomBooking::hasConflict($room->id, $this->string('booking_date')->toString(), $this->string('start_time')->toString(), $this->string('end_time')->toString())) {
                    $validator->errors()->add('start_time', 'Jadwal ini bentrok dengan pemesanan lain pada ruangan yang sama.');
                }
            },
        ];
    }
}
