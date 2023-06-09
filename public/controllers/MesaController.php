<?php

require_once './models/Mesa.php';
require_once './archivos/archivos.php';
require_once './models/Comanda.php';
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/UsuariosMiddleware.php';

class MesaController extends Mesa implements IApiUsable{

    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        $aux = Mesa::obtenerMesaCodigoComanda($parametros['codigo_comanda']);
        //var_dump($aux);
        $auxFecha = new DateTime();
        if(!isset($parametros['codigo_mesa']) && !isset($parametros['codigo_comanda']) 
            && !isset($parametros['estado']) && !isset($parametros['mozo']) 
            && !isset($parametros['foto']) && !isset($parametros['foto'])){

                $payload = json_encode(array("mensaje" => "Datos invalidos"));
            
        }else if(!empty($aux)){
            
            $payload = json_encode(array("mensaje" => "La mesa para ese numero de comanda ya existe"));
        }else{

            $mesa = new Mesa();
            $mesa->codigo_comanda = $parametros['codigo_comanda'];
            $mesa->codigo_mesa = $parametros['codigo_mesa'];
            $mesa->estado =  Mesa::mesaEstado($parametros['estado']);  
            $mesa->mozo = Usuario::ObtenerUnUsuarioPorPuesto( $parametros['mozo']);
            $mesa->foto = Archivos::NombreFoto($_FILES['foto']);
            $mesa->id_puntuacion = -1; 
            $mesa->fecha = $auxFecha->format('Y-m-d'); 
            
             Archivos::GuardarFoto($_FILES['foto'], $mesa);
            var_dump($mesa);
             $mesa->crearMesa();
    
            $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args){
        $id = $args['id'];
        $aux = Mesa::obtenerMesa($id);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorCodComanda($request, $response, $args){
        $com = $args['codigo_comanda'];
        $aux = Mesa::obtenerMesaCodigoComanda($com);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

	public function TraerTodos($request, $response, $args){
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("lista mesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasUsada($request, $response, $args){
        $aux = Mesa::obtenerMesaMasUsada();
        $payload = json_encode("La mesa mas usada fue ".$aux[0][0]);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMenosUsada($request, $response, $args){
        $aux = Mesa::obtenerMesaMenosUsada();
        $payload = json_encode("La mesa menos usada fue ".$aux[0][0]);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMasImporte($request, $response, $args){
        $aux = Mesa::obtenerMesaMasImporte();
        $payload = json_encode("La mesa con mayor importe fue ".$aux[0][0]);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerMenosImporte($request, $response, $args){
        $aux = Mesa::obtenerMesaMenosImporte();
        $payload = json_encode("La mesa con menos importe fue ".$aux[0][0]);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

	public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $codcom = Mesa::obtenerMesaCodigoComanda($parametros['codigo_comanda']);

        if(!isset($parametros['codigo_mesa']) && !isset($parametros['estado'])){
           
            $payload = json_encode(array("mensaje" => "Datos invalidos"));
        }else if(empty($codcom) || (Mesa::mesaEstado($parametros['estado']) == 'Error')){

            $payload = json_encode(array("mensaje" => "No hay ninguna mesa con ese codigo de comanda o estado incorrecto. verificar"));
        }
        else{

            Mesa::modificarMesa($parametros['codigo_comanda'], Mesa::mesaEstado($parametros['estado']) );
            $payload = json_encode(array("mensaje" => "Estado de mesa modificado exitosamente"));
        }
       
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


	public function BorrarUno($request, $response, $args){
        $codigo_mesa = $args['codigo_mesa'];
        $mesa = Mesa::obtenerMesaCodigo($codigo_mesa);

        if(!isset($codigo_mesa)){
           
            $payload = json_encode(array("mensaje" => "Datos invalidos"));
        }else if(empty($mesa)){

            $payload = json_encode(array("mensaje" => "No hay ninguna mesa con ese codigo. verificar"));
        }
        else{
            Mesa::borrarMesa($codigo_mesa);
            $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerImportePorFecha($request, $response, $args){
        $parametros = $request->getParsedBody();
        $importe = Mesa::obtenerMesaPorFecha($parametros['codigo_mesa'],$parametros['fecha1'],$parametros['fecha2']);

        $payload = json_encode("El importe total de lo facturado por la mesa ".$parametros['codigo_mesa']." entre las fechas seleccionadas es ".$importe[0][0]." pesos.");

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}


?>