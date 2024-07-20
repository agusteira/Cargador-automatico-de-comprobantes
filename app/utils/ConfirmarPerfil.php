<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once "utils/AutentificadorJWT.php";

class ConfirmarPerfil
{
    /**
     * Example middleware invokable class
     *
     * //@param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    private $perfilesPermitidos;

    public function __construct(array $perfiles)
    {
        $this->perfilesPermitidos = $perfiles;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::VerificarToken($token);

            $data = AutentificadorJWT::ObtenerData($request);

            if (!in_array($data->perfil, $this->perfilesPermitidos)) {
                $response = new Response();
                $response->getBody()->write(json_encode(array("error" => "Perfil NO AUTORIZADO")));
            }else{
                $response = $handler->handle($request);
            }

        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }


        return $response->withHeader('Content-Type', 'application/json');
    }

}