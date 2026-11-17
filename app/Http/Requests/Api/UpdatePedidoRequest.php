<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $regla = $this->isMethod('PUT') ? 'required' : 'sometimes';

        return [
            'cliente' => [$regla, 'string', 'max:100'],
            'telefono' => [$regla, 'string', 'max:20'],
            'direccion' => [$regla, 'string'],
            'producto' => [$regla, 'string', 'max:150'],
            'cantidad' => [$regla, 'integer', 'min:1'],
            'total' => [$regla, 'numeric', 'min:0', 'max:99999999.99'],
            'estado' => [
                $regla,
                'string',
                Rule::in([
                    'Pendiente',
                    'Preparando',
                    'En camino',
                    'Entregado',
                    'Cancelado',
                ]),
            ],
            'costo_express' => [$regla, 'numeric', 'min:0', 'max:99999999.99'],
            'fecha_pedido' => [$regla, 'date'],
        ];
    }
}

