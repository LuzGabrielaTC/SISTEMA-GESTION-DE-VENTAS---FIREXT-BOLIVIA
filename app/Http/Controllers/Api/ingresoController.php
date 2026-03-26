<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use App\Models\NotaRecepcion;
use App\Models\NotaEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ingresoController extends Controller
{
    
    //LISTAR INGRESOS ACTIVOS
    public function index()
    {
        $ingresos = Ingreso::with(['notaRecepcion.cliente', 'notaEntrega'])
            ->where('estado', true)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($ingresos->isEmpty()) {
            return response()->json(['message' => 'No hay ingresos registrados'], 200);
        }

        return response()->json($ingresos, 200);
    }

    //CREAR INGRESO 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //Debe venir al menos una de las dos notas
            'id_recepcion' => 'required_without:id_entrega|nullable|exists:nota_recepcion,id_recepcion',
            'id_entrega'   => 'required_without:id_recepcion|nullable|exists:nota_entrega,id_entrega',
            'tipo_pago'    => 'required|string|max:100', //Efectivo, QR, Transferencia
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            return DB::transaction(function () use ($request) {
                $montoCalculado = 0;

                // CASO 1: Es un anticipo (Solo viene id_recepcion)
                if ($request->filled('id_recepcion') && !$request->filled('id_entrega')) {
                    $nota = NotaRecepcion::findOrFail($request->id_recepcion);
                    $montoCalculado = $nota->a_cuenta;
                } 
                // CASO 2: Es el pago final (Viene id_entrega)
                else if ($request->filled('id_entrega')) {
                    // Buscamos la entrega y su recepción relacionada para saber el saldo
                    $entrega = NotaEntrega::with('recepcion')->findOrFail($request->id_entrega);
                    if ($entrega->recepcion) {
                        $montoCalculado = $entrega->recepcion->saldo;
                    }
                }

                $ingreso = Ingreso::create([
                    'id_recepcion' => $request->id_recepcion,
                    'id_entrega'   => $request->id_entrega,
                    'tipo_pago'    => $request->tipo_pago,
                    'monto'        => $montoCalculado,
                    'estado'       => true
                ]);

                return response()->json([
                    'message' => 'Ingreso registrado exitosamente',
                    'monto_procesado' => $montoCalculado,
                    'data' => $ingreso
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el ingreso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //MOSTRAR DETALLE DE UN INGRESO
    public function show($id)
    {
        $ingreso = Ingreso::with(['recepcion', 'entrega'])->find($id);

        if (!$ingreso) {
            return response()->json(['message' => 'Ingreso no encontrado'], 404);
        }

        return response()->json($ingreso, 200);
    }

    
    //ACTUALIZAR INGRESO 
    public function update(Request $request, $id)
    {
        $ingreso = Ingreso::find($id);

        if (!$ingreso) {
            return response()->json(['message' => 'Ingreso no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tipo_pago' => 'sometimes|required|string',
            'estado'    => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        //No permitimos actualizar el monto directamente para evitar fraudes,
        //el monto debe ser consistente con las notas.
        $ingreso->update($request->only(['tipo_pago', 'estado']));

        return response()->json([
            'message' => 'Ingreso actualizado correctamente',
            'data' => $ingreso
        ], 200);
    }

    //ELIMINAR INGRESO
    public function destroy($id)
    {
        $ingreso = Ingreso::find($id);

        if (!$ingreso) {
            return response()->json(['message' => 'Ingreso no encontrado'], 404);
        }

        $ingreso->update(['estado' => false]);

        return response()->json(['message' => 'Ingreso anulado correctamente'], 200);
    }
}