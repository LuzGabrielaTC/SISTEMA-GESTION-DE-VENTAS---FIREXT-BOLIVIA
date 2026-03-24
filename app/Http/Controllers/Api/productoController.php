<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class productoController extends Controller
{
    //LISTAR PRODUCTOS
    public function index()
    {
        //Traemos el producto con su "padre" Item
        $productos = Producto::with(['item'])->get();

        if ($productos->isEmpty()) {
            return response()->json(['message' => 'No hay productos registrados'], 200);
        }

        return response()->json($productos, 200);
    }

    //CREAR PRODUCTO 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Validación para la tabla Item (Padre)
            'id_recepcion' => 'nullable|exists:nota_recepcion,id_recepcion',
            'id_entrega'   => 'nullable|exists:nota_entrega,id_entrega',
            'marca'        => 'nullable|string|max:255',
            'articulo'     => 'nullable|string|max:255',
            'capacidad'    => 'nullable|numeric',
            'unidad'       => 'nullable|string',
            'precio'       => 'required|numeric',
            // Validación para la tabla Producto (Hijo)
            'nombre'       => 'required|string|max:255' // Nombre comercial del producto
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 1. Crear el registro en la tabla padre 'item'
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

                // 2. Crear el registro en la tabla hija 'producto' vinculando el ID
                $producto = Producto::create([
                    'id_item_producto' => $item->id_item,
                    'nombre'           => $request->nombre
                ]);

                return response()->json([
                    'message' => 'Producto registrado exitosamente',
                    'data'    => $producto->load('item')
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar el producto',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //MOSTRAR UN PRODUCTO ESPECÍFICO
    public function show($id)
    {
        $producto = Producto::with('item')->find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json($producto, 200);
    }

    //ACTUALIZAR PRODUCTO
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

       $validator = Validator::make($request->all(), [
            'nombre'       => 'sometimes|required|string|max:255',
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

        try {
            return DB::transaction(function () use ($request, $producto) {
                // 2. Actualizamos los datos propios de la tabla 'producto'
                if ($request->has('nombre')) {
                    $producto->update(['nombre' => $request->nombre]);
                }

                // 3. Actualizamos los datos de la tabla padre 'item'
                $item = Item::find($producto->id_item_producto);
                if ($item) {
                    // update() con $request->all() funcionará bien porque Item 
                    // ignorará los campos que no estén en su $fillable (como 'nombre')
                    $item->update($request->all());
                }

                return response()->json([
                    'message' => 'Producto actualizados correctamente',
                    'data'    => $producto->load('item')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el producto',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //ELIMINAR 
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Buscamos el item padre y lo eliminamos
        // El onDelete('cascade') en la migración borrará automáticamente el registro en 'producto'
        $item = Item::find($producto->id_item_producto);
        if ($item) {
            $item->delete();
        }

        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    }
}