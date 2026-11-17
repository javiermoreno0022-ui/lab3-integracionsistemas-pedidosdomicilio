<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePedidoRequest;
use App\Http\Requests\Api\UpdatePedidoRequest;
use App\Http\Resources\PedidoResource;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    protected PedidoService $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function index(Request $request)
    {
        $pedidos = $this->pedidoService->listar($request->user());

        return PedidoResource::collection($pedidos);
    }

    public function store(StorePedidoRequest $request)
    {
        $pedido = $this->pedidoService->crear(
            $request->user(),
            $request->validated()
        );

        return (new PedidoResource($pedido))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $id)
    {
        $pedido = $this->pedidoService->mostrar(
            $request->user(),
            $id
        );

        return new PedidoResource($pedido);
    }

    public function update(UpdatePedidoRequest $request, string $id)
    {
        $pedido = $this->pedidoService->actualizar(
            $request->user(),
            $id,
            $request->validated()
        );

        return new PedidoResource($pedido);
    }

    public function destroy(Request $request, string $id)
    {
        $this->pedidoService->eliminar(
            $request->user(),
            $id
        );

        return response()->noContent();
    }
}
