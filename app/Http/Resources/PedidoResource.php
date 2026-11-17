<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PedidoResource extends JsonResource
{
    /**
     * Transformar el pedido en una respuesta JSON.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente' => $this->cliente,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'producto' => $this->producto,
            'cantidad' => $this->cantidad,
            'total' => $this->total,
            'estado' => $this->estado,
            'costo_express' => $this->costo_express,
            'fecha_pedido' => $this->fecha_pedido?->format('Y-m-d'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'usuario' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
        ];
    }
}
