<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Egreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class egresoController extends Controller
{
    //LISTAR
    public function index()
    {
        $egresos = Egreso::with('usuario')->where('estado', true)->get();
        if($egresos->isEmpty()){
            return response()->json(['message' => 'No hay egresos registrados'], 200);
        }
        return response()->json($egresos, 200);
    }


    //CREAR EGRESOS
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_usuario' => 'required|exists:usuario,id_usuario',
            'tipo'       => 'required|string|max:255',
            'monto'      => 'required|numeric|min:0',
            'descripcion' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        $egreso = Egreso::create([
            'id_usuario'  => $request->id_usuario,
            'tipo'        => $request->tipo,
            'monto'       => $request->monto,
            'descripcion' => $request->descripcion,
            'estado'      => true
        ]);

        return response()->json([
            'message' => 'Egreso creado exitosamente',
            'egreso'  => $egreso
        ], 201);
    }

    //OBTENER UN EGRESO POR ID
    public function show($id)
    {
        $egreso = Egreso::with('usuario')->where('id_egreso', $id)->where('estado', true)->first();

        if (!$egreso) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }

        return response()->json($egreso, 200);
    }

    //ACTUALIZAR EGRESO
    public function update(Request $request, $id)
    {
        $egreso = Egreso::find($id);

        if (!$egreso || !$egreso->estado) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_usuario'   => 'sometimes|required|exists:usuario,id_usuario',
            'tipo'         => 'sometimes|required|string|max:255',
            'monto'        => 'sometimes|required|numeric|min:0',
            'descripcion'  => 'sometimes|required|string',
            'estado'       => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

         $egreso->fill($request->only([
             'id_usuario', 'tipo', 'monto', 'descripcion', 'estado'
         ]));

         $egreso->save();

         return response()->json([
             'message' => 'Egreso actualizado exitosamente',
             'egreso'  => $egreso
         ], 200);
    }

    //ELIMINAR EGRESO
    public function destroy($id)
    {
        $egreso = Egreso::find($id);

        if (!$egreso || !$egreso->estado) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }
        $egreso->estado = false;
        $egreso->save();

        return response()->json(['message' => 'Egreso eliminado exitosamente'], 200);
    }
}
