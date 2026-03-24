<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class usuarioController extends Controller
{
    //LISTAR
    public function index(){
        $usuarios = Usuario::where('estado', true)->get();
        
        if($usuarios -> isEmpty()){
            return response()->json(['message' => 'No hay usuarios registrados'], 200);
        }

        return response() -> json($usuarios, 200);
    }

    //CREAR USUARIO
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'ci' => 'required|string|unique:usuario,ci',
            'telefono' => 'required|string|max:20',
            'rol' => 'required|string|max:50',
            'username' => 'required|string|max:255|unique:usuario,username',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors'  => $validator->errors()
            ], 400);  
        }

        $usuario = Usuario::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'ci'       => $request->ci,
            'telefono' => $request->telefono,
            'rol'      => $request->rol,
            'username' => $request->username,
            'password' => Hash::make($request->password) //Encriptado
        ]);
        if(!$usuario){
            return response()->json(['message' => 'Error al crear el usuario'], 500);
        }
        
        return response()->json([
            'message' => 'Usuario creado exitosamente',
            'usuario' => $usuario
        ], 201);
    }

    //OBTENER USUARIO POR ID
    public function show($id)
    {
        $usuario = Usuario::find($id);

        if(!$usuario){
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario, 200);
    }

    //ELIMINAR USUARIO
    public function destroy($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->estado = false;
        $usuario->save();

        return response()->json([
            'message' => 'Usuario desactivado exitosamente',
            'usuario' => $usuario
        ], 200);
    }

    //ACTUALIZAR USUARIO
    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        
        if(!$usuario){
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'apellido' => 'sometimes|required|string|max:255',
            'ci' => 'sometimes|required|string|unique:usuario,ci,' . $id . ',id_usuario',
            'telefono' => 'sometimes|required|string|max:20',
            'rol' => 'sometimes|required|string|max:50',
            'username' => 'sometimes|required|string|max:255|unique:usuario,username,' . $id . ',id_usuario',
            'password' => 'sometimes|required|string|min:6',
            'estado'   => 'sometimes|boolean',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors'  => $validator->errors()
            ], 400);  
        }

        $usuario->fill($request->only([
            'nombre', 'apellido', 'ci', 'telefono', 'rol', 'username', 'estado'
        ]));
        if($request->has('password')){
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        return response()->json(
        [
            'message' => 'Usuario actualizado exitosamente',
            'usuario' => $usuario
        ], 200);
    }  
}