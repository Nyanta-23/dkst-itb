<?php

namespace App\Http\Requests\Ruangan;

use App\Models\RoomBooking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ReviewRoomBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('review', $this->route('booking')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', 'string', 'in:approved,rejected'],
            'approval_notes' => ['required_if:action,rejected', 'nullable', 'string', 'max:1000'],
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
            'action' => 'keputusan',
            'approval_notes' => 'alasan',
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
            'required_if' => 'Alasan wajib diisi saat menolak pemesanan.',
            'in' => ':attribute tidak valid.',
            'string' => ':attribute harus berupa teks.',
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
                if ($validator->errors()->isNotEmpty() || $this->string('action')->toString() !== 'approved') {
                    return;
                }

                /** @var RoomBooking $booking */
                $booking = $this->route('booking');

                $conflicts = RoomBooking::query()
                    ->where('room_id', $booking->room_id)
                    ->where('booking_date', $booking->booking_date)
                    ->where('status', 'approved')
                    ->whereKeyNot($booking->id)
                    ->where('start_time', '<', $booking->end_time)
                    ->where('end_time', '>', $booking->start_time)
                    ->exists();

                if ($conflicts) {
                    $validator->errors()->add('action', 'Sudah ada pemesanan lain yang disetujui pada jadwal ini.');
                }
            },
        ];
    }
}
