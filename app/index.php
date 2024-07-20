<?php


require_once '../vendor/autoload.php';

require_once "controllers/FacturasController.php";
require_once 'middlewares/ParamMiddlewares.php';
require_once "utils/ConfirmarPerfil.php";

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;


$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->post('/cargar', \FacturasController::class . ':Alta'); 
$app->post('/comprobanteMP', \FacturasController::class . ':MercadoPago'); 
$app->post('/comprobanteCDNI', \FacturasController::class . ':CuentaDNI'); 
$app->post('/cargarCarpetaMP', \FacturasController::class . ':CargarCarpetaMP'); 

$app->run();
?>
