<?php

namespace App\Http\Requests;

use App\Enums\MetodoPagoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCompraRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'proveedore_id' => 'nullable|exists:proveedores,id',
            'comprobante_id' => 'nullable|exists:comprobantes,id',
            'numero_comprobante' => 'max:255|nullable',
            'file_comprobante' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'metodo_pago' => ['nullable', new Enum(MetodoPagoEnum::class)],
            'fecha_hora' => 'nullable|date|date_format:Y-m-d\TH:i',
            'subtotal' => 'required|min:1',

            'total' => 'required|min:1'
        ];
    }
}
