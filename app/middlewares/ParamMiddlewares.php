<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

//Middlewares para verificar parametros
class ParamMiddlewares
{
    //Verificar los tipos de parametros

    public static function VerificarTalla(Request $request, RequestHandler $handler, $talla){
        if ($talla === "s" || $talla === "m" || $talla === "l")
        {
            $response = $handler->handle($request);
        }else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Talla INVALIDA")));
        }
        return $response;
    }
    public static function VerificarTipo(Request $request, RequestHandler $handler, $tipo,$talla){
        if ($tipo === "camiseta" || $tipo === "pantalon")
        {
            $response = self::VerificarTalla($request, $handler, $talla);
        }else{
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Tipo INVALIDO")));
        }
        return $response;
    }

    //Verifcar si existen los parametros
    public static function AltaTienda(Request $request, RequestHandler $handler){
        $parametros = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($parametros["nombre"], $parametros["precio"], $parametros["tipo"], $parametros["talla"], $parametros["color"], $parametros["stock"], $uploadedFiles["foto"])){
            $response = self::VerificarTipo($request, $handler, $parametros["tipo"],$parametros["talla"]);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function TiendaConsultar(Request $request, RequestHandler $handler){
        $parametros = $request->getQueryParams();
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($parametros["nombre"],  $parametros["tipo"],  $parametros["color"])){
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function AltaVentas(Request $request, RequestHandler $handler){
        $parametros = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($parametros["email"], $parametros["nombre"], $parametros["tipo"], $parametros["talla"], $parametros["stock"],$uploadedFiles["foto"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function ModificarVenta(Request $request, RequestHandler $handler){
        $parametros = $request->getParsedBody();

        if(isset($parametros["email"], $parametros["nombre"], $parametros["tipo"], $parametros["talla"], $parametros["stock"],$parametros["id"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function VentasPorUsuario(Request $request, RequestHandler $handler){
        $parametros = $request->getQueryParams();

        if(isset($parametros["email"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function VentasPorProducto(Request $request, RequestHandler $handler){
        $parametros = $request->getQueryParams();

        if(isset($parametros["nombre"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function ProductosEntreValores(Request $request, RequestHandler $handler){
        $parametros = $request->getQueryParams();

        if(isset($parametros["valor1"], $parametros["valor2"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function AltaUsuario(Request $request, RequestHandler $handler){
        $parametros = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        if(isset($parametros["mail"], $parametros["usuario"], $parametros["contraseña"], $parametros["perfil"],$uploadedFiles["foto"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

    public static function Login(Request $request, RequestHandler $handler){
        $parametros = $request->getParsedBody();

        if(isset($parametros["usuario"], $parametros["contraseña"])){
            $response = $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array("error" => "Parametros incorrectos")));
        }

        return $response;
    }

}