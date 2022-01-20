<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

 CONTROLADORES:

// PARA CONTROLAR NECESITAN UNA PETICIÓN POR LO QUE 
// 1. HAY QUE USAR LA CLASE REQUEST CUYA RUTA ES use Illuminate\Http\Request;
// 2. HAY QUE CREAR UN OBJETO DE ESTA CLASE  Y  METERSELO A LOS MÉTODOS COMO PARÁMETRO TRAL QUE ASI (Request $request)

class AuthController extends Controller{
    

    public function register(Request $request){

        $data = $request->only('name', 'email', 'password');

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',

        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator -> messages()], 400);
        }
        //hasta aqui la validación. Ahora creamos el nuevo usuario

        $user = User::create([
            'name' => $request -> name,
            'email' => $request -> email,
            'password' => bcrypt($request -> password)
        ]);
        // Guardamos el usuario y el pass para realizar la peticion del token
        $credentials = $request->only('email', 'password');
        return response()->json([
            'message'=> 'Usuario creado',
            'token' => JWTAuth::attempt($credentials),
            'user'=> $user
        ], Response::HTTP_OK);
    }


    public function authenticate(Request $request){

        $credentials = $request -> only ('email', 'password');

        //con esto solo verificamos que el formato de mail y password es correcto
        $validator = Validator::make($credentials, [
            'email'=> 'required|email',
            'password'=> 'required|string|min:6|max:50'
        ]);
        if($validator->fails()){
            return response()->json(['error'=> $validator -> messages()], 400);
        }
        //si todo es correcto intentamos logar

        try {

            if(!$token = JWTAuth::attempt($credentials)){

                return response()->json([

                    'message' => 'login falló'
                ], 401);
            }
        }
        catch(JWTException $e){

            return response()->json([

                'message' => 'Error'
            ], 500);
        }

        return response()->json([

            'success' => true,
            'token' => $token,
            'use'=> Auth::user()
        ]);

    }


    //Función que utilizaremos para eliminar el token y desconectar al usuario
    public function logout(Request $request) {
        //Validamos que se nos envie el token
            $validator = Validator::make($request->only('token'), [
                'token' => 'required'
            ]);
        //Si falla la validación
            if ($validator->fails()) {
                    return response()->json(['error' => $validator->messages()], 400);
            }
            try {
                //Si el token es valido eliminamos el token desconectando al usuario.
                JWTAuth::invalidate($request->token);
                return response()->json([
                    'success' => true,
                    'message' => 'Usuario desconectado'
                    ]);
                } 
            catch (JWTException $exception) {
                //Error chungo
                return response()->json([
                    'success' => false,
                    'message' => 'Error'
                    ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }


//Función que utilizaremos para obtener los datos del usuario y validar si el token ha expirado.
public function getUser(Request $request) {
    //Validamos que la request tenga el token
        $this->validate($request, [
            'token' => 'required'
            ]);
    //Realizamos la autentificación
        $user = JWTAuth::authenticate($request->token);
    //Si no hay usuario es que el token no es valido o que ha expirado
        if(!$user) {
            return response()->json([
                'message' => 'Token invalido / token expirado',
                ], 401);
            }
    //Devolvemos los datos del usuario si todo va bien.
        return response()->json(['user' => $user]);
    }
}




    
