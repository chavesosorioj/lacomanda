<?php
//////////////////////////
// las comandas tienen los siguientes estados (las mandaria por postman)
// ingresada - en preparacion - lista para servir 
////////////////////////


class Comanda{

    public $id;
    public $id_mesa;
    public $nombre_cliente;
    public $codigo_comanda; 
    public $importe;
    public $estado;
    public $demora; // necesito tener la demora? si ya las tienen las ordenes cada una por el codigo de la comanda
    public $baja; // ver si se lo saco

    public function crearComanda()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO comanda (id_mesa, nombre_cliente, codigo_comanda,
                                                                    importe, estado, demora, baja)
                                                        VALUES (:id_mesa, :nombre_cliente, :codigo_comanda, 
                                                                 :importe, :estado, :demora, :baja)");
        $consulta->bindValue(':id_mesa', $this->id_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':nombre_cliente', $this->nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_comanda', $this->codigo_comanda, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $this->importe, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':demora', $this->demora, PDO::PARAM_INT);
        $consulta->bindValue(':baja', $this->baja, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM comanda");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }
    public static function obtenerComandaId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_mesa,nombre_cliente, codigo_comanda,
                                                        importe, estado, demora, baja
                                                        FROM comanda WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Comanda');
    }
    public static function obtenerComandaCodigo($codigo_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, id_mesa, nombre_cliente, codigo_comanda,
                                                            importe, estado, demora, baja
                                                        FROM comanda WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();

        //return $consulta->fetchObject('Comanda');
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Comanda');
    }

    public static function borrarComanda($codigo_comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM comanda WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function GenerarCodigo(){
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($caracteres), 0, 6);
    }

    public static function modificarComanda($codigo, $estado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE comanda SET estado = :estado
                                                     WHERE codigo_comanda = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function comandaEstado($aux){
        switch($aux)
        {
            case 1:
                return 'ingresada';
            case 2:
                return "en preparacion";
            case 3:
                return "lista para servir";
            default:
                return 'Error';
    
        }
    }
}


?>