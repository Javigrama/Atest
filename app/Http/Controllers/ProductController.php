<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller {

    protected $user;

    public function __construct(Request $request){

        $token = $request->header('Authorization');

        if($token != '') {
            //En caso de que requiera autentificación la ruta
            //obtenemos el usuario y lo almacenamos en una variable, nosotros
            //no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
        }
    }
/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/

    public function index() {
    //Listamos todos los productos
        $productos = Product::get();
        return response()->json($productos);
    }

    

// ATENCION: ESTE METODO CREA EL PRODUCTO REPETIDO, HAY QUE HACER LAS CLAVES
/**
* Store a newly created resource in storage.
*
* @param \Illuminate\Http\Request $request
* @return \Illuminate\Http\Response
*/
public function store(Request $request) {
    //Validamos los datos
            $data = $request->only('name', 'description', 'stock');
            $validator = Validator::make($data, [
                'name' => 'required|max:50|string',
                'description' => 'required|max:150|string',
                'stock' => 'required|numeric',
            ]);
    //Si falla la validación
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
    //Creamos el producto en la BD
            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
            ]);
    //Respuesta en caso de que todo vaya bien.
            return response()->json([
                'message' => 'Producto creado',
                'data' => $product
                ], Response::HTTP_OK);
        }


        
/**
* Display the specified resource.
*
* @param \App\Models\Product $product
* @return \Illuminate\Http\Response
*/
    public function show($id){
        //Buscamos el producto
                $product = Product::find($id);
        //Si el producto no existe devolvemos error no encontrado
                if (!$product) {
                    return response()->json([
                        'message' => 'Producto no encontrado.'
                        ], 404);
                }
        //Si hay producto lo devolvemos
        
                return response()->json([
                    'data' => $product
                    ], Response::HTTP_OK);
            }


            /**
* Update the specified resource in storage.
*
* @param \Illuminate\Http\Request $request
* @param \App\Models\Product $product
* @return \Illuminate\Http\Response
*/
    public function update(Request $request, $id) {
        //Validación de datos
            $data = $request->only('name', 'description', 'stock');
            $validator = Validator::make($data, [
                'name' => 'required|max:50|string',
                'description' => 'required|max:50|string',
                'stock' => 'required|numeric',
            ]);
        //Si falla la validación error.
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages()], 400);
            }
        //Buscamos el producto
            $product = Product::findOrfail($id);
        //Actualizamos el producto.
            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'stock' => $request->stock,
            ]);
        //Devolvemos los datos actualizados.
            return response()->json([
                'message' => 'Producto actualizado correctamente',
                'data' => $product
                ], Response::HTTP_OK);
        }


        /**
* Remove the specified resource from storage.
*
* @param \App\Models\Product $product
* @return \Illuminate\Http\Response
*/
    public function destroy($id) {
        //Buscamos el producto
        $product = Product::findOrfail($id);
        //Eliminamos el producto
        $product->delete();
        //Devolvemos la respuesta
        return response()->json([
            'message' => 'Producto borrado correctamente'
            ], Response::HTTP_OK);
    }
}