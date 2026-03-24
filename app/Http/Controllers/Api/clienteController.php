<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class clienteController extends Controller
{
    //LISTAR
    public function index()
    {
        $clientes = Cliente::where('estado', true)->get();

        if ($clientes->isEmpty()) {
            return response()->json(['message' => 'No hay clientes registrados'], 200);
        }

        return response()->json($clientes, 200);
    }

    //CREAR CLIENTE
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre'       => 'required|string|max:255',
            'apellido'     => 'required|string|max:255',
            'telefono'     => 'required|string|max:20',
            'razon_social' => 'nullable|string|max:255',
            'nit'          => 'nullable|string|unique:cliente,nit',
            'ci'           => 'nullable|string|unique:cliente,ci',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors'  => $validator->errors()
            ], 400);
        }

        $cliente = Cliente::create($request->all());

         if (!$cliente) {
            return response()->json(['message' => 'Error al crear el cliente'], 500);
        }

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'cliente' => $cliente
        ], 201);
    }

    //OBTENER UN SOLO CLIENTE
    public function show($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return response()->json($cliente, 200);
    }
    //ELIMINAR UN CLIENTE 
    public function destroy($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $cliente->estado = false;
        $cliente->save();

        return response()->json(['message' => 'Cliente desactivado exitosamente'], 200);
    }

    //ACTUALIZAR UN CLIENTE
    public function update(Request $request, $id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre'       => 'sometimes|required|string|max:255',
            'apellido'     => 'sometimes|required|string|max:255',
            'telefono'     => 'sometimes|required|string|max:20',
            'razon_social' => 'sometimes|nullable|string|max:255',
            'nit'          => 'sometimes|nullable|string|unique:cliente,nit,' . $id . ',id_cliente',
            'ci'           => 'sometimes|nullable|string|unique:cliente,ci,' . $id . ',id_cliente',
            'estado'       => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors'  => $validator->errors()
            ], 400);
        }

        $cliente->fill($request->only([
            'nombre', 'apellido', 'telefono', 'razon_social', 'nit', 'ci', 'estado'
        ]));

        $cliente->save();

        return response()->json([
            'message' => 'Cliente actualizado exitosamente',
            'cliente' => $cliente
        ], 200);
    }
}
