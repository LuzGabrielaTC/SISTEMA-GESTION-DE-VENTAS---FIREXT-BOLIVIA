<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class servicioController extends Controller
{
    //LISTAR SERVICIOS
    public function index()
    {
        //Traemos el servicio con su "padre" Item 
        $servicios = Servicio::with(['item'])->get();

        if ($servicios->isEmpty()) {
            return response()->json(['message' => 'No hay servicios registrados'], 200);
        }

        return response()->json($servicios, 200);
    }

    //CREAR SERVICIO 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Datos del Item (Padre)
            'id_recepcion' => 'nullable|exists:nota_recepcion,id_recepcion',
            'id_entrega'   => 'nullable|exists:nota_entrega,id_entrega',
            'marca'        => 'nullable|string',
            'articulo'     => 'nullable|string',
            'capacidad'    => 'nullable|numeric',
            'unidad'       => 'nullable|string',
            'precio'       => 'required|numeric',
            'descripcion'  => 'nullable|string',
            // Datos del Servicio (Hijo)
            'tipo_gas'     => 'required|string' 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Usamos una Transacción para asegurar que se creen ambos o ninguno
        try {
            return DB::transaction(function () use ($request) {
                // 1. Crear el Item
                $item = Item::create([
                    'id_recepcion' => $request->id_recepcion,
                    'marca'        => $request->marca,
                    'articulo'     => $request->articulo,
                    'capacidad'    => $request->capacidad,
                    'unidad'       => $request->unidad,
                    'serie'        => $request->serie,
                    'precio'       => $request->precio,
                    'descripcion'  => $request->descripcion,
                    'estado'       => true
                ]);

                // 2. Crear el Servicio usando el ID del item recién creado
                $servicio = Servicio::create([
                    'id_item_servicio' => $item->id_item,
                    'tipo_gas'         => $request->tipo_gas
                ]);

                return response()->json([
                    'message' => 'Servicio registrado correctamente',
                    'data'    => $servicio->load('item')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al registrar servicio', 'error' => $e->getMessage()], 500);
        }
    }

    //OBTENER UN SERVICIO ESPECÍFICO
    public function show($id)
    {
        $servicio = Servicio::with('item')->find($id);

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json($servicio, 200);
    }

    //ACTUALIZAR SERVICIO
    public function update(Request $request, $id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_gas' => 'sometimes|required|string',
            'id_recepcion' => 'sometimes|nullable|exists:nota_recepcion,id_recepcion',
            'id_entrega'   => 'sometimes|nullable|exists:nota_entrega,id_entrega',
            'marca'        => 'sometimes|nullable|string|max:255',
            'articulo'     => 'sometimes|nullable|string|max:255',
            'capacidad'    => 'sometimes|nullable|numeric',
            'unidad'       => 'sometimes|nullable|string|max:50',
            'serie'        => 'sometimes|nullable|string|max:100',
            'precio'       => 'sometimes|required|numeric',
            'descripcion'  => 'sometimes|nullable|string',
            'estado'       => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try{
            return DB::transaction(function () use ($request, $servicio) {
                // Actualizar el Servicio
                if ($request->has('tipo_gas')) {
                    $servicio->update(['tipo_gas' => $request->tipo_gas ?? $servicio->tipo_gas]);
                }

                // Actualizar el Item relacionado
                $item = Item::find($servicio->id_item_servicio);
                if ($item) {
                    $item -> update($request->all());
                }

                return response()->json([
                    'message' => 'Servicio actualizado correctamente',
                    'data'    => $servicio->load('item')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar servicio', 'error' => $e->getMessage()], 500);
        }
    }

    //ELIMINAR (Aquí eliminamos el Item padre y por cascada se va el servicio)
    public function destroy($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        // Al eliminar el item, la base de datos (por el onDelete cascade) borra el servicio
        $item = Item::find($servicio->id_item_servicio);
        if ($item) {
            $item->delete();
        }

        return response()->json(['message' => 'Servicio eliminado correctamente'], 200);
    }
}