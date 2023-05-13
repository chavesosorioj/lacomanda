<?php

require_once './models/Comanda.php';
require_once './models/Orden.php';
require_once './models/menu.php';
require_once './interfaces/IApiUsable.php';

class ComandaController extends Comanda implements IApiUsable{

    public function CargarUno($request, $response, $args){
        
        $parametros = $request->getParsedBody();
        $codigo = Comanda::GenerarCodigo();
        echo "el codigo es ".$codigo;

        $auxCom = Comanda::obtenerComandaCodigo($codigo); 

        if(!isset($parametros['id_mesa'])
            && !isset($parametros['nombre_cliente']) && !isset($parametros['importe'])
            && !isset($parametros['estado']) && !isset($parametros['demora'])){
            
            $payload = json_encode(array("mensaje" => "Datos Invalidos"));
        }else if(!empty($auxCom)){ 
            // var_dump('Lo que hay en auxCom',$auxCom);
            $payload = json_encode(array("mensaje" => "La comanda ya existe"));
        }
        else{
            $com = new Comanda();
            $com->id_mesa = $parametros['id_mesa'];
            $com->nombre_cliente = $parametros['nombre_cliente'];
            // $com->codigo_comanda = $parametros['codigo_comanda'];
            $com->codigo_comanda = $codigo;
            $com->importe = $parametros['importe'];
            $com->estado = $parametros['estado'];
            $com->demora = $parametros['demora'];
            $com->baja = 1; // ver si se lo saco
            $com->crearComanda();

            //$datos = array("id" => $emp->id, "tipo_usuario" => $emp->sector); // esto no se para que lo quiero
            $payload = json_encode(array("mensaje" => "Comanda creada con exito", "Codigo para el cliente: " => $codigo));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args){
        $lista = Comanda::obtenerTodos();
        $payload = json_encode(array("lista comandas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    public function TraerPorId($request, $response, $args){

        $id = $args['id'];
        $com = Comanda::obtenerComandaId($id);
        $payload = json_encode($com);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){
        $cod = $args['codigo_comanda'];
        $com = Comanda::obtenerComandaCodigo($cod);
        $payload = json_encode($com);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $com = Comanda::obtenerComandaCodigo($parametros['codigo_comanda']);
       // var_dump($com);
        if(!isset($parametros['estado']) && !isset($parametros['codigo_comanda'])){

            $payload = json_encode(array("mensaje" => "Datos invalidos"));
        }else if(empty($com) ||(Comanda::comandaEstado($parametros['estado']) == 'Error')) { //

            $payload = json_encode(array("mensaje" => "Codigo de comanda o estado incorrecto. verificar"));

        }else{
            Comanda::modificarComanda($parametros['codigo_comanda'],Comanda::comandaEstado($parametros['estado']));
            $payload = json_encode(array("mensaje" => "Se modifico el estado de la comanda con exito"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args){
        $cod = $args['codigo_comanda'];
        $com = Comanda::obtenerComandaCodigo($cod);
        if(!empty($com)){
            Comanda::borrarComanda($cod);
            Orden::borrarOrdenComanda($cod);
            $payload = json_encode(array("mensaje" => "Se dio de baja con exito a la comanda y sus ordenes"));
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}


?>