<?php

namespace App\Http\Requests;

use App\Enums\MetodoPagoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreVentaRequest extends FormRequest
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
            'cliente_id' => 'nullable|exists:clientes,id',
            'comprobante_id' => 'nullable|exists:comprobantes,id',
            'metodo_pago' => ['required', new Enum(MetodoPagoEnum::class)],
            'subtotal' => 'required|numeric|min:1',
            'total' => 'required|numeric|min:1',
            'monto_recibido' => 'required|numeric|min:1',
            'vuelto_entregado' => 'required|numeric|min:0'
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $total         = (float) $this->input('total', 0);
            $subtotal      = (float) $this->input('subtotal', 0);
            $montoRecibido = (float) $this->input('monto_recibido', 0);
            $vuelto        = (float) $this->input('vuelto_entregado', 0);

            if ($total < $subtotal) {
                $validator->errors()->add('total', 'El total no puede ser menor que el subtotal.');
            }
            if ($montoRecibido < $total) {
                $validator->errors()->add('monto_recibido', 'El monto recibido no puede ser menor que el total a pagar.');
            }
            // Tolerancia de 1 peso para diferencias de redondeo en el frontend
            if (abs($vuelto - ($montoRecibido - $total)) > 1) {
                $validator->errors()->add('vuelto_entregado', 'El vuelto no coincide con monto recibido menos total.');
            }
        });
    }
}
