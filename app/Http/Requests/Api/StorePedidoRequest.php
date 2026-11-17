<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePedidoRequest extends FormRequest
{
    /**
     * Determinar si el usuario puede realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validaciÃƒÂ³n para registrar un pedido.
     */
    public function rules(): array
    {
        return [
            'cliente' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:20'],
            'direccion' => ['required', 'string'],
            'producto' => ['required', 'string', 'max:150'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'total' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'estado' => [
                'required',
                'string',
                Rule::in([
                    'Pendiente',
                    'Preparando',
                    'En camino',
                    'Entregado',
                    'Cancelado',
                ]),
            ],
            'costo_express' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'fecha_pedido' => ['required', 'date'],
        ];
    }

    /**
     * Mensajes personalizados de validaciÃƒÂ³n.
     */
    public function messages(): array
    {
        return [
            'cliente.required' => 'El nombre del cliente es obligatorio.',
            'telefono.required' => 'El telÃƒÂ©fono es obligatorio.',
            'direccion.required' => 'La direcciÃƒÂ³n es obligatoria.',
            'producto.required' => 'El producto es obligatorio.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'total.required' => 'El total es obligatorio.',
            'estado.required' => 'El estado del pedido es obligatorio.',
            'estado.in' => 'El estado seleccionado no es vÃƒÂ¡lido.',
            'costo_express.required' => 'El costo express es obligatorio.',
            'fecha_pedido.required' => 'La fecha del pedido es obligatoria.',
        ];
    }
}
