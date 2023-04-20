
<?php

require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './middlewares/AutenticacionJWT.php';

class UsuarioController extends Usuario implements IApiUsable{


    public function CargarUno($request, $response, $args){
        $parametros = $request->getParsedBody();

        if(isset($parametros['idUsuario']) && isset($parametros['nombre']) && 
        isset($parametros['mail']) && isset($parametros['clave']) && 
        isset($parametros['puesto']) && isset($parametros['estado'])){

            $mail = $parametros['mail'];
            $auxFecha = new DateTime();
            $aux = Usuario::obtenerUsuarioMail($mail);
            if(!$aux){
                $Us = new Usuario();
                $Us->idUsuario = $parametros['idUsuario'];
                $Us->nombre = $parametros['nombre'];
                $Us->mail = $mail;
                $Us->clave = $parametros['clave'];
                $Us->puesto = $parametros['puesto'];
                $Us->estado = $parametros['estado'];
                $Us->idEstado = Usuario::Estado($Us->estado);
                $Us->idPuesto = Usuario::Puesto($Us->puesto);
                $Us->fecha_ingreso = $auxFecha->format('d-m-Y'); 
                $Us->fecha_salida = '---';
                $Us->crearUsuario();
        
                $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
            }
            else {
                $payload = json_encode(array("mensaje" => "El usuario ya existe"));
            }
        }
        else{
            $payload = json_encode(array("mensaje" => "Los datos son invalidos"));
        }

        // deberia sumarle que no este dado de baja 
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

	public function TraerTodos($request, $response, $args){
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("lista usuarios" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
        
    }

	public function TraerUno($request, $response, $args){
        $mail = $args['mail'];
        $aux = Usuario::obtenerUsuarioMail($mail);
        $payload = json_encode($aux);

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

	public function ModificarUno($request, $response, $args){
        $parametros = $request->getParsedBody();
        $idUsuario = $parametros['idUsuario'];

        $aux = Usuario::obtenerUsuarioId($idUsuario);
        if(!empty($aux)){
            $mail = $parametros['mail'];
            Usuario::modificarUsuario($mail, $idUsuario);
            $payload = json_encode(array("mensaje" => "Usuario actualizado con exito"));
        }
        else {
            $payload = json_encode(array("mensaje" => "No hay ningun usuario con ese id"));
        }
       
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

	public function BorrarUno($request, $response, $args){
        $idUsuario = $args['id'];
        $aux = Usuario::obtenerUsuarioId($idUsuario);
        if(!empty($aux)){
            Usuario::borrarUsuario($idUsuario);
            $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        }
        else {
            $payload = json_encode(array("mensaje" => "No hay ningun usuario con ese id"));
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function LogIn($request, $response, $args){
        $parametros = $request->getParsedBody();
  
        if(isset($parametros['idUsuario']) && isset($parametros['nombre']) && isset($parametros['puesto'])){
  
            $Us = Usuario::ObtenerUsuarioLogIn($parametros['idUsuario']);
            //var_dump($Us);
            if($Us->idUsuario == $parametros['idUsuario'] && $Us->puesto == $parametros['puesto']){
                
                $datos = array("idUsuario" => $Us->idUsuario, "tipo_usuario" => $Us->puesto);
                $token = AutenticacionJWT::CrearToken($datos);
                $response->getBody()->write(json_encode(array("token: " => $token)));
            }
            else {
                $response->getBody()->write(json_encode(array("error" => "El idUsuario y el puesto son incorrectos")));
                $response = $response->withStatus(400);
          }
  
        }
        else {
          $response->getBody()->write(json_encode(array("error" => "Los datos ingresados son invalidos")));
          $response = $response->withStatus(400);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
      }
}




?>
