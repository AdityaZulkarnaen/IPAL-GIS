<?php

namespace Modules\IPAL\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base Form Request untuk module IPAL.
 * Developer IPAL bisa extend class ini untuk validasi form.
 */
class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [];
    }
}
