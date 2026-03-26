<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(),
        [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message'=>'Error al loguearse',
                'errors'=>$validator->errors()
            ],400);
        }

        // return response()->json([
        //     "ALGO"=>$request->username
        // ]);

        $user = Usuario::where("username",$request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login'=>['Las credenciales son incorrectas']
            ]);
        }

        $tokenName = $request->userAgent()?? "kae";
        $accessToken = $user->createToken($tokenName,["*"]);
        $tokenString = $accessToken->plainTextToken;

        return response()->json([
            'message'=>"Login exitoso",
            "usuario"=>$user,
            "token"=>$tokenString
        ]);

    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken();
        $token->delete();

        return response()->json([
            'message'=>"logout exitoso",

        ]);
    }

    public function me(Request $request){
        $user = $request->user();
        return response()->json([
            'message'=>"usuario actual exitoso",
            'usuario'=>$user
        ]);
    }
}
