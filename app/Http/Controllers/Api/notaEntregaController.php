<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use App\Models\NotaEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class notaEntregaController extends Controller
{
    //LISTAR 
    public function index()
    {
        //Cargamos la recepción relacionada y el usuario que entrega
        $entregas = NotaEntrega::with(['recepcion', 'usuario'])->where('estado', true)->get();

        if ($entregas->isEmpty()) {
            return response()->json(['message' => 'No hay notas de entrega registradas'], 200);
        }

        return response()->json($entregas, 200);
    }

    //CREAR NOTA DE ENTREGA
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_recepcion' => 'required|exists:nota_recepcion,id_recepcion',
            'id_usuario'   => 'required|exists:usuario,id_usuario',
            'fecha'        => 'required|date',
            'cantidad'     => 'required|integer|min:1',
            'precio_total' => 'required|numeric|min:0',
            'a_cuenta'     => 'required|numeric|min:0',
            'observacion'  => 'nullable|string',
            'tipoEntrega'  => 'nullable|string|in:En tienda,Mobil',

            'tipo_pago' => 'required|string|in:Efectivo,QR,Transferencia' // Efectivo, QR, Transferencia
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

       try {
            return DB::transaction(function () use ($request) {
                // 1. Cálculo del saldo de la entrega
                $saldo = $request->precio_total - $request->a_cuenta;

                // 2. Crear la Nota de Entrega
                $entrega = NotaEntrega::create([
                    'id_recepcion' => $request->id_recepcion,
                    'id_usuario'   => $request->id_usuario,
                    'fecha'        => $request->fecha,
                    'cantidad'     => $request->cantidad,
                    'precio_total' => $request->precio_total,
                    'a_cuenta'     => $request->a_cuenta,
                    'saldo'        => $saldo,
                    'observacion'  => $request->observacion,
                    'tipoEntrega'  => $request->tipoEntrega ?? 'En tienda',
                    'tipo_pago'    => $request->tipo_pago ?? 'Efectivo',
                    'estado'       => true
                ]);

                // 3. TRIGGER AUTOMÁTICO: Registrar el ingreso del dinero
                // En una entrega, el ingreso es lo que el cliente paga en ese momento (a_cuenta)
                if ($request->a_cuenta > 0) {
                    Ingreso::create([
                        'id_recepcion' => $request->id_recepcion,
                        'id_entrega'   => $entrega->id_entrega,
                        'tipo_pago'    => $request->tipo_pago,
                        'monto'        => $request->a_cuenta,
                        'estado'       => true
                    ]);
                }

                return response()->json([
                    'message' => 'Nota de entrega e ingreso registrados correctamente',
                    'entrega' => $entrega
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar la entrega',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //OBTENER UNA ENTREGA POR ID
    public function show($id)
    {
        $entrega = NotaEntrega::with(['recepcion', 'usuario'])->find($id);

        if (!$entrega) {
            return response()->json(['message' => 'Nota de entrega no encontrada'], 404);
        }

        return response()->json($entrega, 200);
    }

    //ACTUALIZAR NOTA DE ENTREGA
    public function update(Request $request, $id)
    {
        $entrega = NotaEntrega::find($id);

        if (!$entrega) {
            return response()->json(['message' => 'Nota de entrega no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_recepcion' => 'sometimes|required|exists:nota_recepcion,id_recepcion',
            'id_usuario'   => 'sometimes|required|exists:usuario,id_usuario',
            'fecha'        => 'sometimes|required|date',
            'cantidad'     => 'sometimes|required|integer|min:1',
            'precio_total' => 'sometimes|required|numeric|min:0',
            'a_cuenta'     => 'sometimes|required|numeric|min:0',
            'estado'       => 'sometimes|boolean',
            'observacion'  => 'nullable|string',
            'tipoEntrega'  => 'nullable|string|in:En tienda,Mobil',
            'tipo_pago'    => 'sometimes|required|string|in:Efectivo,QR,Transferencia' // Efectivo, QR, Transferencia
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        $entrega->fill($request->only([
            'id_recepcion', 'id_usuario', 'fecha', 'cantidad', 
            'precio_total', 'a_cuenta', 'estado', 'observacion', 'tipoEntrega'
        ]));

        // Recalcular saldo si se modifican montos
        if ($request->has('precio_total') || $request->has('a_cuenta')) {
            $entrega->saldo = $entrega->precio_total - $entrega->a_cuenta;
        }

        $entrega->save();

        return response()->json([
            'message' => 'Nota de entrega actualizada exitosamente',
            'entrega' => $entrega
        ], 200);
    }

    //ELIMINAR
    public function destroy($id)
    {
        $entrega = NotaEntrega::find($id);

        if (!$entrega) {
            return response()->json(['message' => 'Nota de entrega no encontrada'], 404);
        }

        $entrega->estado = false;
        $entrega->save();

        return response()->json(['message' => 'Nota de entrega desactivada correctamente'], 200);
    }
}