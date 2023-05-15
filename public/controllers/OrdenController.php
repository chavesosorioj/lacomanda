<?php

require_once './models/Orden.php';
require_once './models/menu.php';
require_once './models/Comanda.php';
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/UsuariosMiddleware.php';

class OrdenController extends Orden implements IApiUsable{
   
    public function CargarUno($request, $response, $args){
        
        $parametros = $request->getParsedBody();

        if(isset($parametros['codigo_comanda']) && isset($parametros['pedido'])){
            
            $comanda = Comanda::obtenerComandaCodigo($parametros['codigo_comanda']);

            $auxPedido = Menu::verificarMenu($parametros['pedido']);
            //echo $auxPedido;
            if($auxPedido != 'Error' && !empty($comanda)){       
                $ord = new Orden();
                $ord->codigo_comanda = $parametros['codigo_comanda'];
                $ord->pedido = $parametros['pedido'];
                $ord->area = Menu::puestoMenu($parametros['pedido']); 
                $ord->idUsuario = Usuario::ObtenerUnUsuarioPorPuesto($ord->area);
                $ord->demora = Menu::demoraMenu($parametros['pedido']);
                $ord->estado = "ingresado";
                // var_dump($ord);
                $ord->crearOrden();
                
                $payload = json_encode(array("mensaje" => "Orden creada con exito"));
                
            }
            else{
                $payload = json_encode(array("mensaje" => "El pedido no esta en el menu o la comanda no existe. Verifique."));
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

    public function TraerIngresadasPrep($request, $response, $args){
        $lista = Orden::obtenerTodos();
        Orden::ListaPorUsuario($lista);
        $payload = json_encode(array("mensaje" => "Orden ingresadas y en preparacion"));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function TraerListo($request, $response, $args){
        $lista = Orden::obtenerTodos();
        Orden::ListaServir($lista);
        $payload = json_encode(array("mensaje" => "Orden listas para servir"));

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
        // var_dump($ord);
        if(!isset($parametros['id']) && !isset($parametros['estado'])){
            
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

    public function TraerOrdenPorUsuario($request, $response, $args){
        $idUsuario = $args['idUsuario'];
         $aux = Usuario::obtenerUsuarioId($idUsuario);
        $lista = Orden::obtenerOrdenIdUsuario($idUsuario);

        if(empty($aux)){
            $payload = json_encode(array("mensaje" => "No hay ningun usuario con ese id."));
        }else if(empty($lista))
            $payload = json_encode(array("mensaje" => "El usuario no tiene ninguna orden asignada."));
        else{
            $payload = json_encode(array("mensaje" => "Lista de orden por usuario"));
            Orden::ListaPorUsuario($lista);
            
            
        }
                $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerTotalOperaciones($request, $response, $args){

        $lista = Orden::obtenerOrdenSector();
        $payload = json_encode(array("Operaciones totales ordenadas por sector" => $lista));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function TraerMasMenosVendido($request, $response, $args){

        Orden::MasMenosVendido();
        $payload = json_encode("Mas menos pedido");
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}

?>