<?php

//////////////////////////
// las ordenes tienen los siguientes estados que se envian por postman
// ingresada - en preparacion - lista para servir 
////////////////////////

class Orden{

    public $id;
    public $codigo_comanda;
    public $pedido;
    public $area;
    public $demora;
    public $estado;

    public function GetCodigo(){
        return $this->codigo_comanda;
    }
    public function GetPedido(){
        return $this->pedido;
    }
    public function GetEstado(){
        return $this->estado;
    }
    public function GetDemora(){
        return $this->demora;
    }

    public function crearOrden()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ordenes (codigo_comanda, pedido, 
                                                                    area, demora, estado)
                                                    VALUES (:codigo_comanda, :pedido, :area, :demora, :estado)");
        $consulta->bindValue(':codigo_comanda', $this->codigo_comanda, PDO::PARAM_STR);
        $consulta->bindValue(':pedido', $this->pedido, PDO::PARAM_STR);
        $consulta->bindValue(':area', $this->area, PDO::PARAM_STR);
        $consulta->bindValue(':demora', $this->demora, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ordenes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Orden');
    }
    public static function obtenerOrden($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_comanda, pedido, area, demora, estado
                                                        FROM ordenes WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Orden');
    }

    public static function obtenerOrdenComanda($codigo_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, codigo_comanda, pedido, 
                                                            area, demora, estado
                                                        FROM ordenes WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Orden');
    }

    public static function ModificarOrdenEstado($estado, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ordenes SET estado = :estado 
                                                        WHERE id = :id");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ModificarOrdenPedido($id, $pedido, $demora, $area)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ordenes SET pedido = :pedido,
                                                        demora = :demora,
                                                        area = :area 
                                                        WHERE id = :id");
        $consulta->bindValue(':pedido', $pedido, PDO::PARAM_STR);
        $consulta->bindValue(':demora', $demora, PDO::PARAM_INT);
        $consulta->bindValue(':area', $area, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function borrarOrden($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM ordenes WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function borrarOrdenComanda($codigo_comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM ordenes WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function DemoraOrdenesComanda($codigo_comanda){
        $ordenes = self::obtenerOrdenComanda($codigo_comanda);
        $total = 0;
        foreach($ordenes as $aux){
            $total = $total + $aux->GetDemora();
        }

        return $total;
    }
}

?>