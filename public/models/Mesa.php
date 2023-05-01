<?php

//////////////////////////
// las mesas tienen los siguientes estados (las mandaria por postman)
// cliente esperando pedido, cliente comiendo, cliente pagando, cerrada
////////////////////////
class Mesa{

    public $id;
    public $codigo_comanda;
    public $codigo_mesa;
    public $estado;
    public $mozo;
    public $foto;
    public $fecha;
    public $id_puntuacion; //creo que lo voy a sacar, se buscan las encuentas por encuestra y ahi aparece la mesa

    // public function GetPedido(){
    //     return $this $codigo_comanda;
    // }

    public function GetEstado(){
        return $this->estado;
    }

    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo_comanda, codigo_mesa,
                                                                    estado, mozo, foto,id_puntuacion, fecha) 
                                                        VALUES (:codigo_comanda, :codigo_mesa,
                                                                :estado, :mozo, :foto, :id_puntuacion, :fecha)");
        $consulta->bindValue(':codigo_comanda', $this->codigo_comanda, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $this->codigo_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':mozo', $this->mozo, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':id_puntuacion', $this->id_puntuacion, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    public static function obtenerMesa($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_comanda, codigo_mesa,
                                                        estado, mozo, foto,id_puntuacion, fecha
                                                        FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function obtenerMesaCodigo($codigo_mesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_comanda, codigo_mesa,
                                                        estado, mozo, foto,id_puntuacion, fecha
                                                        FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }
    public static function obtenerMesaCodigoComanda($codigo_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_comanda, codigo_mesa,
                                                        estado, mozo, foto,id_puntuacion
                                                        FROM mesas WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue( ':codigo_comanda', $codigo_comanda, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function obtenerMesaCodigoPedido($codigo_mesa, $pedido_id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_cliente $codigo_comanda, codigo_mesa,
                                                                tiempo, estado, mozo, fecha,
                                                             foto, comentario 
                                                        FROM mesas WHERE codigo_mesa = :codigo_mesa
                                                        an $codigo_comanda = $codigo_comanda");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->bindValue( ':codigo_comanda', $pedido_id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }

    public static function borrarMesa($codigo_mesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM mesas WHERE codigo_mesa = :codigo_mesa");
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function modificarMesa($codigo_comanda, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado 
                                                    WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();
    }
    public static function mesaEstado($aux){
        switch($aux)
        {
            case 1:
                return 'cliente esperando pedido';
            case 2:
                return "cliente comiendo";
            case 3:
                return "cliente pagando";
            case 4:
                return "cerrada";
            default:
                return 'Error';
    
        }
    }

}
?>