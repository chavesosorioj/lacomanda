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
    public $demora; 
    public $baja; 
    
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

    public static function obtenerComandaImporte()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id_mesa, MIN(importe) as importe_minimo
                                                        FROM comanda
                                                        GROUP BY id_mesa
                                                        ORDER BY importe_minimo ASC");
        $consulta->execute();
        return $consulta->fetchAll();
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

    public static function obtenerComandaEntrega()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT c.codigo_comanda, SUM(o.demora) as tiempo_ordenes, c.demora 
                                                        as tiempo_comanda FROM comanda c 
                                                        INNER JOIN ( SELECT codigo_comanda, SUM(demora) as demora 
                                                                    FROM ordenes GROUP BY codigo_comanda ) o 
                                                        WHERE c.codigo_comanda = o.codigo_comanda 
                                                        GROUP BY c.codigo_comanda");
        $consulta->execute();
        return $consulta->fetchAll();
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

    public static function ComandaBarataCara(){
        $lista = Comanda::obtenerComandaImporte();
        echo "------- Mesas segun el importe de la mas barata a la mas cara"."\n";
        foreach($lista as $com){
            echo "mesa ".$com[0]." - importe ".$com[1]."\n";
        }
    }

    public static function EntregadaTiempo(){
        $lista = self::obtenerComandaEntrega();
        $cant=0;

        echo "---Comandas entregadas a tiempo ---"."\n";
        foreach($lista as $com){
            if(intval($com[1]) == $com[2]){
                echo "Comanda - ".$com[0]."\n";
                $cant = $cant+1;
            }
        }
        if($cant ===0)
            echo "Ninguna comanda fue entregada a tiempo "."\n";
    }

    public static function EntregadaDemora(){
        $lista = self::obtenerComandaEntrega();
        $cant=0;

        echo "---Comandas entregadas con demora ---"."\n";
        foreach($lista as $com){
            if(intval($com[1]) != $com[2]){
                echo "Comanda - ".$com[0]."\n";
                $cant = $cant+1;
            }
        }
        if($cant ===0)
            echo "Todas las comandas fueron entregadas a tiempo "."\n";
    }
}


?>