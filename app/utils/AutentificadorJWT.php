<?php

use Firebase\JWT\JWT;

class AutentificadorJWT
{
    private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            'exp' => $ahora + (60000),
            'data' => $datos,
            'firma' => "Agustin Teira"
        );
        return JWT::encode($payload, self::$claveSecreta, self::$tipoEncriptacion[0]);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function VerificarTipoUsuario($token, $tipoUsuario)
    {
        $datos = JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );

        if($datos->data->tipo == $tipoUsuario){
            $retorno = true;
        }else{
            $retorno=false;
        }

        return $retorno;
    }

    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($request)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }
}