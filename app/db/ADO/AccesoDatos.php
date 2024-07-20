<?php
class AccesoDatos
{
    protected $objetoPDO;
    protected static $objAccesoDatos;

    protected function __construct()
    {
        try {
            $contStr = "mysql:host=localhost; dbname=zoe";
            $user = "root";
            $pass = "";
            $this->objetoPDO = new PDO($contStr, $user, $pass);
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    public static function obtenerInstancia()
    {
        if (!isset(self::$objAccesoDatos)) {
            self::$objAccesoDatos = new AccesoDatos();
        }
        return self::$objAccesoDatos;
    }

    public function AltaFactura($factura){
        $sql = "INSERT INTO `facturas` (`fecha`, `tipoC`, `numeroDeOperacion`, `comprador`, `CUIT`, `totalFacturado`, `banco`, `medioDePago`) 
        VALUES (:fecha, :tipoC, :numeroDeOperacion, :comprador, :CUIT, :totalFacturado, :banco, :medioDePago)";
    
        $stmt = $this->prepararConsulta($sql);
        // Vincular los valores a los parámetros
        $stmt->bindParam(':fecha', $factura->fecha);
        $stmt->bindParam(':tipoC', $factura->tipoConsumidor);
        $stmt->bindParam(':numeroDeOperacion',  $factura->numeroDeOperacion);
        $stmt->bindParam(':comprador', $factura->comprador);
        $stmt->bindParam(':CUIT', $factura->CUIT);
        $stmt->bindParam(':totalFacturado',  $factura->totalFacturado);
        $stmt->bindParam(':banco',  $factura->banco);
        $stmt->bindParam(':medioDePago',  $factura->medioDePago);
        // Ejecutar la consulta
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    
    public function prepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    public function obtenerUltimoId()
    {
        return $this->objetoPDO->lastInsertId();
    }
    
}