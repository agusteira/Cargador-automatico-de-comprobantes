<?php

include_once "models/Facturas.php";

class FacturasController{

    public static function Alta($request, $response, $args){
        $parametros = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();

        $dia = $parametros['dia'];
        $mes = $parametros['mes'];
        $anio = $parametros['anio'];

        $fecha = $anio . "/" . $mes . "/" . $dia;
        $tipoConsumidor = $parametros['tipoConsumidor'];
        $numeroDeOperacion = $parametros['numeroDeOperacion'];
        $comprador = $parametros['comprador'];
        $CUIT = $parametros['CUIT'];
        $totalFacturado = $parametros['totalFacturado'];
        $banco = $parametros['banco'];
        $medioDePago = $parametros['medioDePago'];
        
        if(Facturas::CrearFactura($fecha, $tipoConsumidor, $numeroDeOperacion, $comprador, $CUIT, $totalFacturado, $banco, $medioDePago)){
            $payload = json_encode(array("mensaje" => "Factura creada con exito"));
        }
        else{
            $payload = json_encode(array("mensaje" => "La Factura NO se pudo crear"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function MercadoPago($request, $response, $args){
        $uploadedFiles = $request->getUploadedFiles();
        $parametros = $request->getParsedBody();

        $comprobante = $uploadedFiles["comprobante"];
        $tipoConsumidor = $parametros['tipoConsumidor'];
        $medioDePago = $parametros['medioDePago'];

        if(Facturas::SubirComprobanteMP($comprobante, $tipoConsumidor, $medioDePago)){
            $payload = json_encode(array("mensaje" => "Factura creada con exito"));
        }
        else{
            $payload = json_encode(array("mensaje" => "La Factura NO se pudo crear"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function cargarCarpetaMP($request, $response, $args){
        $parametros = $request->getParsedBody();

        $tipoConsumidor = $parametros['tipoConsumidor'];
        $medioDePago = $parametros['medioDePago'];

        $carpeta = "../otros/comprobantes/mp/";
        $archivos = scandir($carpeta);
        //var_dump($archivos);
        foreach ($archivos as $archivo) {
            if(is_file($carpeta . $archivo)  && Facturas::SubirComprobanteMP($carpeta . $archivo, $tipoConsumidor, $medioDePago)){
                $payload = json_encode(array("mensaje" => "Factura creada con exito"));
            }
            else{
                $payload = json_encode(array("mensaje" => "La Factura NO se pudo crear"));
            }
        }

        

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function CuentaDNI($request, $response, $args){
        $uploadedFiles = $request->getUploadedFiles();
        $parametros = $request->getParsedBody();

        $comprobante = $uploadedFiles["comprobante"];
        $tipoConsumidor = $parametros['tipoConsumidor'];
        $medioDePago = $parametros['medioDePago'];

        if(Facturas::SubirComprobanteCDNI($comprobante, $tipoConsumidor, $medioDePago)){
            $payload = json_encode(array("mensaje" => "Factura creada con exito"));
        }
        else{
            $payload = json_encode(array("mensaje" => "La Factura NO se pudo crear"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}