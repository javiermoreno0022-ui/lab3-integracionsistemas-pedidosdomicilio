<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UsuarioResource;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    /**
     * Mostrar el usuario autenticado y sus pedidos.
     */
    public function index(Request $request)
    {
        $usuario = $request->user()->load('pedidos');

        return new UsuarioResource($usuario);
    }
}
