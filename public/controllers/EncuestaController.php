<?php

require_once './models/Encuesta.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/UsuariosMiddleware.php';

class EncuestaController extends Encuesta implements IApiUsable{
   
    public function CargarUno($request, $response, $args){
        
        $parametros = $request->getParsedBody();
        $id = $parametros ['id'];
        $aux = Encuesta::ObtenerEncuestaPorId($id);
       // var_dump($aux);
       $p_cocinero= $parametros ['p_cocinero'];
        $p_mozo= $parametros ['p_mozo'];
        $p_restaurant= $parametros ['p_restaurant'];
        $p_mesa= $parametros ['p_mesa'];
        if(isset($p_cocinero) && isset($p_restaurant)
        && isset($p_mesa) && isset($p_mesa)){

            if(count($aux)>0){
                if($aux[0]->GetPMozo()==0 && $aux[0]->GetPMesa()==0 
                && $aux[0]->GetPCocinero()==0 && $aux[0]->GetPRestaurant()==0){
                  
                    Encuesta::modificarEncuesta($p_cocinero, $p_mesa,$p_mozo, $p_restaurant, $id);
                    $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
                }
                else{
                    $payload = json_encode(array("mensaje" => "La encuesta ya fue cargada"));
                }
            }
            else {
                $payload = json_encode(array("mensaje" => "No hay una encuentra con ese id"));
            }
        }else{
            $payload = json_encode(array("mensaje" => "Los datos cargados son invalidos"));
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

        $id = $args['id'];
        $aux =Encuesta::ObtenerEncuestaPorId($id);
        $payload = json_encode($aux);

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
    //VER QUE DATO SE LE MODIFICA
    public function ModificarUno($request, $response, $args){
        /*
        $parametros = $request->getParsedBody();
        $id = $parametros['id'];

        $aux = Empleado::ObtenerEmpleadoPorId($id);
        if(count($aux)>0){
            $est= $parametros['estado'];
            Empleado::ModificarEmpleado($est, $id);
            $payload = json_encode(array("mensaje" => "Estado del empleado actualizado con exito"));
        }
        else {
            $payload = json_encode(array("mensaje" => "No hay ningun empleado con ese nombre"));
        }
        

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
        */
    }

    public function TraerTodos($request, $response, $args){
        $lista = Encuesta::ObtenerTodos();
        $payload = json_encode(array("lista encuestas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    
}



?>
