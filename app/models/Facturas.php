<?php

include_once "././db/ADO/AccesoDatos.php";
use thiagoalessio\TesseractOCR\TesseractOCR;


class Facturas{
    public $fecha;
    public $tipoConsumidor;
    public $numeroDeOperacion;
    public $comprador;
    public $CUIT;
    public $totalFacturado;
    public $banco;
    public $medioDePago;


    public function __construct ($fecha, $tipoConsumidor, $numeroDeOperacion, $comprador, $CUIT, $totalFacturado, $banco, $medioDePago){
        $this->fecha = $fecha;
        $this->tipoConsumidor = $tipoConsumidor;
        $this->numeroDeOperacion = $numeroDeOperacion;
        $this->comprador = $comprador;
        $this->CUIT = $CUIT;
        $this->totalFacturado = $totalFacturado;
        $this->banco = $banco;
        $this->medioDePago = $medioDePago;
    }
    public static function CrearFactura($fecha, $tipoConsumidor, $numeroDeOperacion, $comprador, $CUIT, $totalFacturado, $banco, $medioDePago){
        //$path = self::CargarFoto($foto,$nombre,$tipo);
        $Factura = new Facturas($fecha, $tipoConsumidor, $numeroDeOperacion, $comprador, $CUIT, $totalFacturado, $banco, $medioDePago);

        $datos = AccesoDatos::obtenerInstancia();
        $data = $datos->AltaFactura($Factura);
        
        return $data;
    }
    public static function SubirComprobanteMP($comprobante,$tipoConsumidor, $medioDePago){
        $textoComprobante = Facturas::LeerFoto($comprobante);
        //var_dump($textoComprobante);
        $fecha = Facturas::ObtenerFecha($textoComprobante);
        $numeroDeOperacion = Facturas::ObtenerNumeroDeOperacion($textoComprobante);
        $monto = Facturas::ObtenerMonto($textoComprobante);
        $nombre = Facturas::ObtenerNombre($textoComprobante);
        $cuit = Facturas::ObtenerCuit($textoComprobante);
        $data = true;
        $data = Facturas::CrearFactura($fecha, $tipoConsumidor, $numeroDeOperacion, $nombre, $cuit, $monto, "mp", $medioDePago);

        echo "Fecha: " . $fecha . "   ---   " . "NOMBRE:  " . $nombre . "   ---   " . "Numero de operacion:  " . $numeroDeOperacion . "   ---   " . "Monton:  $" . $monto  
            . "   ---   " . "CUIT:  " . $cuit  ;

        return $data;
    }

    public static function SubirComprobanteCDNI($comprobante,$tipoConsumidor, $medioDePago){
        $textoComprobante = Facturas::LeerFoto($comprobante);
        //var_dump($textoComprobante);
        $fecha = Facturas::ObtenerFechaCDNI($textoComprobante);
        $numeroDeOperacion = Facturas::ObtenerCodigoDeReferencia($textoComprobante);
        $monto = Facturas::ObtenerMonto($textoComprobante);
        
        $nombre = Facturas::ObtenerNombreCDNI($textoComprobante);
        
        $cuit = Facturas::ObtenerDNI($textoComprobante);
        var_dump($cuit);
        $data = Facturas::CrearFactura($fecha, $tipoConsumidor, $numeroDeOperacion, $nombre, $cuit, $monto, "cuenta DNI", $medioDePago);

        echo "Fecha: " . $fecha . "   ---   " . "NOMBRE:  " . $nombre . "   ---   " . "Numero de operacion:  " . $numeroDeOperacion . "   ---   " . "Monton:  $" . $monto  
            . "   ---   " . "CUIT:  " . $cuit  ;
        return $data;
    }

    public static function LeerFoto($foto){
        
        try{
            $fileRead = (new TesseractOCR($foto->getStream()->getMetadata('uri')))
                    ->setLanguage('esp')
                    ->run();
        }finally{
            return $fileRead;
        }/*
        $fileRead = (new TesseractOCR($foto))
                    ->setLanguage('esp')
                    ->run();
        */
        
    }

    // -----------------------------
    //      INICIO MERCADOPAGO
    // -----------------------------

    public static function ObtenerFecha($texto){
        $patron1 = '/(\d{1,2}) de (\w+) de (\d{4})/';
        $patron2 = '/(\d{1,2}) de (\w+) (\d{4})/';

        if (preg_match($patron1, $texto, $matches)) {
            $encontro = true;
        }elseif(preg_match($patron2, $texto, $matches)){
            $encontro = true;
        }else{
            $encontro = false;
        }

        if($encontro){
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];

            // Convertir el nombre del mes en español al número de mes
            $months = [
                'enero' => '01',
                'febrero' => '02',
                'marzo' => '03',
                'abril' => '04',
                'mayo' => '05',
                'junio' => '06',
                'julio' => '07',
                'agosto' => '08',
                'septiembre' => '09',
                'octubre' => '10',
                'noviembre' => '11',
                'diciembre' => '12'
            ];

            $monthNumber = $months[strtolower($month)];

            // Formatear la fecha al formato Y-m-d
            $formattedDate = sprintf('%04d-%02d-%02d', $year, $monthNumber, $day);

            return $formattedDate;
        }else{
            throw new Exception("No se encontró una fecha en el texto.");
        }

    }
    public static function ObtenerNumeroDeOperacion($texto){
        $patron_numero = '/\b\d{11}\b/';

        // Buscar coincidencias de número en el texto
        if (preg_match($patron_numero, $texto, $matches)) {
            // $matches[0] contiene el número de 11 dígitos encontrado
            $numero = $matches[0];

            return $numero;
        } else {
            throw new Exception("No se encontró un número de 11 dígitos en el texto.");
        }
    }
    public static function ObtenerMonto($texto){
        $patron_plata = '/\$\s?([\d.,]+)/';

        // Buscar coincidencias de cantidad de dinero en el texto
        if (preg_match($patron_plata, $texto, $matches)) {
            // $matches[1] contiene la cantidad de dinero encontrada (con formato)
            //var_dump($matches);
            $plata_formateada = $matches[1];

            // Eliminar caracteres no numéricos y convertir a entero
            $plata =  str_replace(['.'], '', $plata_formateada);
            
            return (int)$plata;
        } else {
            throw new Exception("No se encontró una cantidad de dinero en el texto.");
        }
    }
    public static function ObtenerNombre($texto){
        $patron_nombre1 = '/e\s+([A-Za-z\s]+)\s+CUIT/i';
        $patron_nombre2 = '/\d+\s+([A-Za-z\s]+)\s+\d/i';

        // Buscar coincidencias de nombre en el texto
        if (preg_match($patron_nombre1, $texto, $matches)) {
            $nombre = trim($matches[1]);
            return $nombre;
        }elseif (preg_match_all($patron_nombre2, $texto, $matches)) {
            if($matches[1][1] == "a las"){
                $nombre = trim($matches[1][2]);
            }
            else{
                $nombre = trim($matches[1][1]);
            }
            var_dump($matches[1]);
            return $nombre;
        } 
        else {
            throw new Exception("No se encontró un nombre en el texto.");
        }
    }
    public static function ObtenerCuit($texto){
        $patron_numero = '/\b\d{2}-\d{8}-\d\b/';

        // Buscar coincidencias de número en el texto
        if (preg_match($patron_numero, $texto, $matches)) {
            $numero_con_guiones = $matches[0];
            $numero_sin_guiones = str_replace('-', '', $numero_con_guiones);
            return $numero_sin_guiones;
        } else {
            throw new Exception("No se encontró un número en el formato especificado en el texto.");
        }

        
    }
    // -----------------------------
    //      FIN MERCADO PAGO
    // -----------------------------

    // -----------------------------
    //      INICIO CUENTADNI
    // -----------------------------

    public static function ObtenerFechaCDNI($texto){
        $regex = '/(\d{2})\/(\d{2})\/(\d{4})/';

        // Buscar la fecha en el texto
        if (preg_match($regex, $texto, $matches)) {
            // Extraer los componentes de la fecha
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];

            // Formatear la fecha al formato deseado
            $formatted_date = "$year-$month-$day";

            return $formatted_date;
        } else {
            throw new Exception("No se encontro la fecha especificada");
        }
    }

    public static function ObtenerCodigoDeReferencia($texto){
        // Definir la expresión regular para el código de referencia
        $regex = '/Cédigo de referencia\s*([A-Z0-9]+(?:[^\s]*))/i';

        // Buscar el código de referencia en el texto
        if (preg_match($regex, $texto, $matches)) {
            // Extraer el código de referencia
            $reference_code = $matches[1];
            return $reference_code;
        } else {
            throw new Exception("No se encontro el codigo de referencia");
        }
    }

    public static function ObtenerNombreCDNI($texto){
        $regex = '/Origen\s*([\p{L} ]+)/u';

        // Buscar el nombre en el texto
        if (preg_match($regex, $texto, $matches)) {
            // Extraer el nombre
            $name = $matches[1];
            return $name;
        } else {
            throw new Exception("No se encontro el nombre de origen");
        }
    }

    public static function ObtenerDNI($texto){
        $regex = '/(\d{2}\.\d{3}\.\d{3})/';

        // Buscar el dni en el texto
        if (preg_match($regex, $texto, $matches)) {
            // Extraer el nombre
            $dni_with_dots = $matches[1];
            $dni = str_replace('.', '', $dni_with_dots);
            return $dni;
        } else {
            throw new Exception("No se encontro el dni");
        }
    }

    // -----------------------------
    //      FIN CUENTADNI
    // -----------------------------



    /*
    public static function ComprobarRegistro($nombre, $tipo,$color){
        $datos = TiendasADO::obtenerInstancia();
        $data = $datos->comprobarRegistro($nombre, $tipo,$color);
        return $data;
    }

    public static function ObtenerProductosEntreValores($valor1, $valor2){
        $datos = TiendasADO::obtenerInstancia();
        $data = $datos->ListarProductosEntreValores($valor1, $valor2);
        return $data;
    }

    public static function CargarFoto($foto, $nombre, $tipo){
        $rutaTemporal =  $foto->getStream()->getMetadata('uri');
        $nombreImagen = $nombre . "_" . $tipo . ".jpg";
        $carpetaDestino = 'db/ImagenesDeRopa/2024/';
        $rutaDestino = $carpetaDestino . $nombreImagen;
        
        if (move_uploaded_file($rutaTemporal, $carpetaDestino . $nombreImagen)) {
            $retorno = $rutaDestino;
        } else {
            $retorno = null;
        }
        return $retorno;
    }*/
}