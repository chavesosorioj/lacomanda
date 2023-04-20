<?php

require_once './models/Orden.php';
require_once './models/menu.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/UsuariosMiddleware.php';

class OrdenController extends Orden implements IApiUsable{
   
    public function CargarUno($request, $response, $args){
        
        $parametros = $request->getParsedBody();

        if(isset($parametros['codigo_comanda']) && isset($parametros['pedido'])){
            
            // CAMBIAR ESTO POR FUNCION QUE LLAME A COMANDA Y VEA SI EXISTE
            $aux = Orden::obtenerOrdenComanda($parametros['codigo_comanda']); 
            $auxId = Orden::obtenerOrden(20); 

            //////////////////////////
            $auxPedido = Menu::verificarMenu($parametros['pedido']);
            echo $auxPedido;
            if(empty($auxId)){       
                $ord = new Orden();
                $ord->codigo_comanda = $parametros['codigo_comanda'];
                $ord->pedido = $parametros['pedido'];
                $ord->area = Menu::puestoMenu($parametros['pedido']); 
                $ord->demora = Menu::demoraMenu($parametros['pedido']);
                $ord->estado = "ingresado";
                $ord->crearOrden();
                

                $payload = json_encode(array("mensaje" => "Orden creada con exito"));
                
            }
            else if($auxPedido == 'Error'){
                $payload = json_encode(array("mensaje" => "El pedido no esta en el menu"));
            }
            else{
                $payload = json_encode(array("mensaje" => "La orden ya existe"));
            }

        }else {
            $payload = json_encode(array("mensaje" => "Datos invalidos"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Orden::obtenerTodos();
        $payload = json_encode(array("lista ordenes" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorComanda($request, $response, $args){

        $cod = $args['codigo_comanda'];
        $aux = Orden::obtenerOrdenComanda($cod);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){
        $id = $args['id'];
        $aux = Orden::obtenerOrden($id);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        if(isset($parametros['id']) && isset($parametros['pedido'])){
            $ord = Orden::obtenerOrden($parametros['id']);
            $auxPedido = Menu::verificarMenu($parametros['pedido']);
        
            //$ord[0]->GetPedido()!=$parametros['pedido']

            if(!empty($ord) && $auxPedido != 'Error' ){
                $puesto = Menu::puestoMenu($parametros['pedido']); 
                $demora = Menu::demoraMenu($parametros['pedido']);
                Orden::ModificarOrdenPedido($parametros['id'], $parametros['pedido'], $demora, $puesto);
               
                $payload = json_encode(array("mensaje" => "Orden actualizada con exito"));
            }else{
                $payload = json_encode(array("mensaje" => "No hay una orden con ese pedido o el pedido no esta en el menu. verifique"));
            }
        }else{
            $payload = json_encode(array("mensaje" => "Datos invalidos"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstado($request, $response, $args){
        $parametros = $request->getParsedBody();
        $ord = Orden::obtenerOrden($parametros['id']);
        if(!isset($aux) && !isset($parametros['estado'])){
            
            $payload = json_encode(array("mensaje" => "Datos invalidos"));

        }else if(empty($ord) || $ord->GetEstado()==$parametros['estado']){
            $payload = json_encode(array("mensaje" => "No se tiene orden con ese id o el estado es el mismo. verifique."));
        }
        else{
            Orden::ModificarOrdenEstado($parametros['estado'], $parametros['id']);
            $payload = json_encode(array("mensaje" => "Estado de orden actualizado con exito"));
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
       $ord = Orden::obtenerOrden($parametros['id']);
        if(!isset($parametros['id']) && !isset($parametros['pedido'])){

            $payload = json_encode(array("mensaje" => "Datos invalidos"));

        }else if(empty($ord) || ($ord->GetPedido() != $parametros['pedido'])){

            $payload = json_encode(array("mensaje" => "No hay orden con ese id o el pedido es incorrecto. verifique"));    
        }else{
            Orden::borrarOrden($parametros['id'], $parametros['pedido']);
            $payload = json_encode(array("mensaje" => "Orden borrada con exito"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');

        
    }

    public static function TotalDemora($request, $response, $args){
        $cod = $args['codigo_comanda'];
        $demora = Orden::DemoraOrdenesComanda($cod);

        $payload = json_encode('La demora es de '.$demora.' minutos');
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>