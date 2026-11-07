<?php

namespace App\Modules\Harvest\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHarvestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'commodity' => 'required|string|max:100',
            'variety' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'price_per_kg' => 'required|integer|min:500',
            'grade' => 'required|in:A,B,C',
            'harvest_date' => 'required|date',
            'ready_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:tersedia,sebagian_terjual,habis',
        ];
    }

    public function messages(): array
    {
        return [
            'commodity.required' => 'Nama komoditas wajib diisi.',
            'quantity.required' => 'Jumlah hasil panen wajib diisi.',
            'quantity.min' => 'Jumlah hasil panen minimal 1 kg.',
            'price_per_kg.required' => 'Harga per kg wajib diisi.',
            'grade.required' => 'Tingkatan grade (A/B/C) wajib dipilih.',
            'photo.image' => 'Berkas harus berupa gambar.',
            'photo.mimes' => 'Format gambar yang diperbolehkan adalah jpeg, png, jpg, atau webp.',
            'photo.max' => 'Ukuran gambar maksimal 2048 KB (2 MB).',
        ];
    }
}

