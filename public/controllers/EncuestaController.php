<?php

require_once './models/Encuesta.php';
require_once './models/Comanda.php';
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/UsuariosMiddleware.php';

class EncuestaController extends Encuesta implements IApiUsable{
   
    public function CargarUno($request, $response, $args){
        
        $parametros = $request->getParsedBody();
        $aux = Encuesta::ObtenerEncuestaPorComanda($parametros['codigo_comanda']);
        $comanda = Comanda::obtenerComandaCodigo($parametros['codigo_comanda']);

        if(!isset($parametros['codigo_mesa']) && !isset($parametros['codigo_comanda'])){

                $payload = json_encode(array("mensaje" => "Datos invalidos"));
            
        }else if(!empty($aux) || empty($comanda)){
            
            $payload = json_encode(array("mensaje" => "La encuesta para ese codigo de comanda ya existe o la comanda no existe. Verifique"));
        }else{

            $enc = new Encuesta();
            $enc->codigo_comanda = $parametros['codigo_comanda'];
            $enc->codigo_mesa = $parametros['codigo_mesa'];
            $enc->p_mesa =  -1;
            $enc->p_cocinero = -1;
            $enc->p_mozo = -1;
            $enc->p_restaurant = -1; 
            $enc->comentario = " ";
            //var_dump($enc);
            
            $enc->crearEncuesta();
    
            $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));
        }



        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function TraerPorId($request, $response, $args){

        $id = $args['id'];
        $aux =Encuesta::ObtenerEncuestaPorId($id);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){

        $cod_com = $args['codigo_comanda'];
        $aux =Encuesta::ObtenerEncuestaPorComanda($cod_com);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Encuesta::ObtenerTodos();
        $payload = json_encode(array("lista encuestas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMejores($request, $response, $args){
        Encuesta::mejoresComentarios();
        $payload = json_encode(array("Mejores comentarios"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPeores($request, $response, $args){
        Encuesta::peoresComentarios();
        $payload = json_encode(array("Peores comentarios"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    

    //VER QUE DATO SE LE MODIFICA
    public function ModificarUno($request, $response, $args){
        
        $parametros = $request->getParsedBody();
        $aux = Encuesta::ObtenerEncuestaPorComanda($parametros['codigo_comanda']);
        $mesa = Mesa::obtenerMesaCodigoComanda($parametros['codigo_comanda']);

        if(!isset($parametros['codigo_mesa']) && !isset($parametros['codigo_comanda']) &&
        !isset($parametros['p_mozo']) && !isset($parametros['p_cocina']) &&
        !isset($parametros['p_restaurant']) && !isset($parametros['p_mesa']) &&
        !isset($parametros['comentario'])){

                $payload = json_encode(array("mensaje" => "Datos invalidos"));
            
        }else if(empty($aux) || ($mesa[0]->GetEstado() != 'cerrada')){
            
            $payload = json_encode(array("mensaje" => "La encuesta para ese codigo de comanda no existe o la mesa no esata cerrada, verifique."));
        }else{

            Encuesta::modificarEncuesta($parametros['p_cocinero'], $parametros['p_mesa'],
                                        $parametros['p_restaurant'], $parametros['p_cocinero'], 
                                        $parametros['comentario'], $parametros['codigo_comanda']);
            $payload = json_encode(array("mensaje" => "Los datos de la encuesta fueron cargados con exito"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
        
    }

    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];
        Encuesta::BorrarEncuesta($id);
        $payload = json_encode(array("mensaje" => "Encuesta borrada con exito"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}



?>
