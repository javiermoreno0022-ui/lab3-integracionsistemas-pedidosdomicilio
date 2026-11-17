<?php

namespace App\Services;

use App\Models\User;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Collection;

class PedidoService
{
    /**
     * Listar los pedidos del usuario autenticado.
     */
    public function listar(User $usuario): Collection
    {
        return $usuario->pedidos()
            ->with('user')
            ->latest()
            ->get();
    }

    /**
     * Registrar un nuevo pedido.
     */
    public function crear(User $usuario, array $datos): Pedido
    {
        return $usuario->pedidos()->create($datos);
    }

    /**
     * Mostrar un pedido específico del usuario.
     */
    public function mostrar(User $usuario, string $id): Pedido
    {
        return $usuario->pedidos()
            ->with('user')
            ->findOrFail($id);
    }

    /**
     * Actualizar un pedido del usuario.
     */
    public function actualizar(
        User $usuario,
        string $id,
        array $datos
    ): Pedido {
        $pedido = $usuario->pedidos()->findOrFail($id);

        $pedido->update($datos);

        return $pedido->refresh()->load('user');
    }

    /**
     * Eliminar un pedido del usuario.
     */
    public function eliminar(User $usuario, string $id): void
    {
        $pedido = $usuario->pedidos()->findOrFail($id);

        $pedido->delete();
    }
}