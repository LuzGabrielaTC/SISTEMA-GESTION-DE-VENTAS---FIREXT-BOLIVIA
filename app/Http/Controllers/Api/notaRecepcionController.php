<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NotaRecepcion;
use App\Models\Ingreso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class notaRecepcionController extends Controller
{
    //LISTAR
    public function index()
    {
        $notas = NotaRecepcion::with(['cliente', 'usuario'])->where('estado', true)->get();

        if ($notas->isEmpty()) {
            return response()->json(['message' => 'No hay notas de recepción registradas'], 200);
        }

        return response()->json($notas, 200);
    }
    //OBTENER NOTA POR ID
    public function show($id){
        $nota = NotaRecepcion::with(['cliente', 'usuario'])->find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota de recepcion no encontrada'], 404);
        }

        return response()->json($nota, 200);
    }

    //CREAR NOTA
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_cliente'   => 'required|exists:cliente,id_cliente',
            'id_usuario'   => 'required|exists:usuario,id_usuario',
            'fecha'        => 'required|date',
            'cantidad'     => 'required|integer|min:1',
            'precio_total' => 'required|numeric|min:0',
            'a_cuenta'     => 'required|numeric|min:0',
            'observacion'  => 'nullable|string',
            'tipoReserva'  => 'nullable|string|in:En tienda,Mobil',

            'tipo_pago' => 'required|string|in:Efectivo,QR,Transferencia' // Efectivo, QR, Transferencia
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        try {
            // Usamos DB::transaction para que si el ingreso falla, no se cree la nota (y viceversa)
            return DB::transaction(function () use ($request) {
                
                // 1. Cálculo del saldo
                $saldo = $request->precio_total - $request->a_cuenta;

                // 2. Crear la Nota de Recepción
                $nota = NotaRecepcion::create([
                    'id_cliente'   => $request->id_cliente,
                    'id_usuario'   => $request->id_usuario,
                    'fecha'        => $request->fecha,
                    'cantidad'     => $request->cantidad,
                    'precio_total' => $request->precio_total,
                    'a_cuenta'     => $request->a_cuenta,
                    'saldo'        => $saldo,
                    'observacion'  => $request->observacion,
                    'tipoReserva'  => $request->tipoReserva ?? 'En tienda',
                    'tipo_pago'    => $request->tipo_pago ?? 'Efectivo',
                    'estado'       => true
                ]);

                // 3. TRIGGER AUTOMÁTICO: Crear el Ingreso si dejó algo a cuenta
                if ($request->a_cuenta > 0) {
                    Ingreso::create([
                        'id_recepcion' => $nota->id_recepcion,
                        'tipo_pago'    => $request->tipo_pago,
                        'monto'        => $request->a_cuenta,
                        'estado'       => true
                    ]);
                }

                return response()->json([
                    'message' => 'Nota e Ingreso registrados exitosamente',
                    'nota'    => $nota->load('cliente')
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el registro',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    //ACTUALIZAR NOTA
    public function update(Request $request, $id)
    {
        $nota = NotaRecepcion::find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id_cliente'   => 'sometimes|required|exists:cliente,id_cliente',
            'id_usuario'   => 'sometimes|required|exists:usuario,id_usuario',
            'fecha'        => 'sometimes|required|date',
            'cantidad'     => 'sometimes|required|integer|min:1',
            'precio_total' => 'sometimes|required|numeric|min:0',
            'a_cuenta'     => 'sometimes|required|numeric|min:0',
            'estado'       => 'sometimes|boolean',
            'observacion'  => 'nullable|string',
            'tipoReserva'  => 'nullable|string|in:En tienda,Mobil',
            'tipo_pago'    => 'sometimes|required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación',
                'errors'  => $validator->errors()
            ], 400);
        }

        //Actualizamos los campos presentes
        $nota->fill($request->only([
            'id_cliente', 'id_usuario', 'fecha', 'cantidad', 
            'precio_total', 'a_cuenta', 'estado', 'observacion', 'tipoReserva'
        ]));

        //Si cambió el precio o lo que dejó a cuenta, recalculamos el saldo
        if ($request->has('precio_total') || $request->has('a_cuenta')) {
            $nota->saldo = $nota->precio_total - $nota->a_cuenta;
        }

        $nota->save();

        return response()->json([
            'message' => 'Nota actualizada exitosamente',
            'nota'    => $nota
        ], 200);
    }

    //DESACTIVAR NOTA (Borrado lógico)
    public function destroy($id)
    {
        $nota = NotaRecepcion::find($id);

        if (!$nota) {
            return response()->json(['message' => 'Nota no encontrada'], 404);
        }

        $nota->estado = false;
        $nota->save();

        return response()->json(['message' => 'Nota desactivada correctamente'], 200);
    }
}