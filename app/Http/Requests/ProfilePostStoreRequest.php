<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfilePostStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'wallpaper_id' => 'required|exists:wallpapers,id',
            'caption' => 'nullable|string|max:255',
        ];
    }
}
