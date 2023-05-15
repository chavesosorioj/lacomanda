<?php

require_once './db/AccesoDatos.php';

class Usuario{

    public $idUsuario;
    public $nombre;
    public $mail;
    public $clave;
    public $puesto;
    public $estado;
    public $idPuesto;
    public $idEstado; 
    public $fecha_ingreso;
    public $fecha_salida;

    // no estoy segura de idPuesto o idEstado
    public function GetIdPuesto(){
        return $this->idPuesto;
    }
    public function GetPuesto(){
        return $this->puesto;
    }

    public function GetIdUsuario(){
        return $this->idUsuario;
    }

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (idUsuario, nombre, mail,
                                                        clave, puesto, estado, idEstado, idPuesto,
                                                        fecha_ingreso, fecha_salida) 
                                                        VALUES (:idUsuario, :nombre, :mail,
                                                        :clave, :puesto, :estado, :idEstado, :idPuesto,
                                                        :fecha_ingreso, :fecha_salida)");
 //       $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_INT);
        $consulta->bindValue(':puesto', $this->puesto, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':idPuesto', $this->idPuesto, PDO::PARAM_INT);
        $consulta->bindValue(':idEstado', $this->idEstado, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_ingreso', $this->fecha_ingreso, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_salida', $this->fecha_salida, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idUsuario, nombre, mail,
                                                    clave, puesto, estado, idEstado, idPuesto,
                                                    fecha_ingreso, fecha_salida FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function obtenerUsuarioMail($mail){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idUsuario, nombre, mail,
                                                        clave, puesto, estado, idEstado, idPuesto,
                                                        fecha_ingreso, fecha_salida
                                                        FROM usuarios WHERE 
                                                        mail = :mail");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }


    public static function obtenerUsuarioId($idUsuario){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idUsuario, nombre, mail,
                                                    clave, puesto, estado, idPuesto, idEstado,
                                                    fecha_ingreso, fecha_salida
                                                    FROM usuarios WHERE 
                                                    idUsuario = :idUsuario");
        $consulta->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioIngreso($id){

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idUsuario, nombre, fecha_ingreso
                                                        FROM usuarios WHERE 
                                                        id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    // que necesito modificar aca?
    public static function modificarUsuario($mail, $idUsuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET mail = :mail WHERE idUsuario = :idUsuario");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consulta->execute();
    }

    // borra usuario - cambia estado, idEstado y fecha_baja

    public static function borrarUsuario($idUsuario)
    {
        $fechaSalida = new DateTime();
        $estado = 'Baja';
        $idEstado = 3;
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_salida = :fechaSalida, 
                                                        estado = :estado, idEstado = :idEstado
                                                        WHERE idUsuario = :idUsuario");
        $consulta->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaSalida', $fechaSalida->format('d-m-Y'), PDO::PARAM_STR); 
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idEstado', $idEstado, PDO::PARAM_INT);
        $consulta->execute();
    }

    // public function echoUsuario(){
    //     echo 'Nombre: '.$this->nombre.'\n';
    //     echo 'Mail: '.$this->mail.'\n';
    //     echo 'clave: '.$this->clave.'\n';
    //     echo 'Puesto: '.$this->Puesto.'\n';
    // }

    // public function listarUsuarios($lista){
    //     foreach($lista as $aux){
    //         $aux->echoUsuario();
    //     }
    // }

    public function Puesto($aux){
        switch($aux)
        {
            case 'Socio':
                return 1;
            case 'Bartender':
                return 2;
            case 'Cervecero':
                return 3;
            case 'Cocinero':
                return 4;
            case 'Mozo':
                return 5;
            default:
                return 'Error';

        }
    }

    public function Estado($aux){
        switch($aux)
        {
            case "Disponible":
                return 1;
            case "Suspendido":
                return 2;
            case "Baja":
                return 3;
            default:
                return 'Error';

        }
    }

    public static function ObtenerUsuarioLogIn($idUsuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idUsuario, nombre, puesto, estado,
                                                        fecha_ingreso, fecha_salida
                                                        FROM usuarios WHERE idUsuario = :idUsuario");
        $consulta->bindValue(':idUsuario', $idUsuario, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function ObtenerUnUsuarioPorPuesto($puesto){
        $lista = self::obtenerTodos();
        $array = array();
        foreach($lista as $aux){
            if($aux->GetIdPuesto()==$puesto){
                array_push($array, $aux);
            }
        }
        //   var_dump($array);
        $random = random_int(1, count($array));
        if($random == count($array))
            return $array[$random-1]->GetIdUsuario();
        else
            return $array[$random]->GetIdUsuario();
    }

}


?>
