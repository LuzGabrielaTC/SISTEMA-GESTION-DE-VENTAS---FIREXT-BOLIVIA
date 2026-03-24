<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class itemController extends Controller
{
    //LISTAR TODOS LOS ITEMS ACTIVOS
    public function index()
    {
        $items = Item::with(['recepcion.cliente', 'entrega'])->where('estado', true)->get();

        if ($items->isEmpty()) {
            return response()->json(['message' => 'No hay items registrados'], 200);
        }

        return response()->json($items, 200);
    }

    //CREAR UN ITEM
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_recepcion' => 'nullable|exists:nota_recepcion,id_recepcion',
            'id_entrega'   => 'nullable|exists:nota_entrega,id_entrega',
            'marca'        => 'nullable|string|max:255',
            'articulo'     => 'nullable|string|max:255',
            'capacidad'    => 'nullable|numeric|min:0',
            'unidad'       => 'nullable|string|max:20', // kg, lb, etc.
            'serie'        => 'nullable|string|max:100',
            'precio'       => 'required|numeric|min:0',
            'descripcion'  => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        $item = Item::create([
            'id_recepcion' => $request->id_recepcion,
            'id_entrega'   => $request->id_entrega,
            'marca'        => $request->marca,
            'articulo'     => $request->articulo,
            'capacidad'    => $request->capacidad,
            'unidad'       => $request->unidad,
            'serie'        => $request->serie,
            'precio'       => $request->precio,
            'descripcion'  => $request->descripcion,
            'estado'       => true
        ]);

        return response()->json([
            'message' => 'Item registrado exitosamente',
            'item'    => $item
        ], 201);
    }

    //MOSTRAR UN ITEM ESPECÍFICO
    public function show($id)
    {
        $item = Item::with(['recepcion', 'entrega', 'servicio', 'producto'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        return response()->json($item, 200);
    }

    //ACTUALIZAR ITEM
    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_recepcion' => 'nullable|exists:nota_recepcion,id_recepcion',
            'id_entrega'   => 'nullable|exists:nota_entrega,id_entrega',
            'marca'        => 'sometimes|nullable|string',
            'articulo'     => 'sometimes|nullable|string',
            'capacidad'    => 'sometimes|nullable|numeric',
            'unidad'       => 'sometimes|nullable|string',
            'precio'       => 'sometimes|nullable|numeric',
            'estado'       => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        $item->update($request->all());

        return response()->json([
            'message' => 'Item actualizado exitosamente',
            'item'    => $item
        ], 200);
    }

    //BORRADO LÓGICO
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item no encontrado'], 404);
        }

        $item->estado = false;
        $item->save();

        return response()->json(['message' => 'Item desactivado correctamente'], 200);
    }
}