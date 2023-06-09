<?php

class Encuesta{

    public $id;
    public $codigo_mesa;
    public $codigo_comanda;
    public $p_mesa;
    public $p_restaurant;
    public $p_mozo;
    public $p_cocinero;
    public $comentario;

    public function GetPMozo(){
        return $this->p_mozo;
    }
    public function GetPRestaurant(){
        return $this->p_restaurant;
    }
    public function GetPMesa(){
        return $this->p_mesa;
    }
    public function GetPCocinero(){
        return $this->p_cocinero;
    }
    public function GetComentario(){
        return $this->comentario;
    }

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuestas (codigo_mesa, codigo_comanda, 
                                                                    p_mesa, p_restaurant, p_mozo, p_cocinero, comentario)
                                                                  VALUES (:codigo_mesa, :codigo_comanda, 
                                                                    :p_mesa, :p_restaurant, :p_mozo, :p_cocinero
                                                                    , :comentario)");
        $consulta->bindValue(':codigo_mesa', $this->codigo_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_comanda', $this->codigo_comanda, PDO::PARAM_STR);
        $consulta->bindValue(':p_mesa', $this->p_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':p_restaurant', $this->p_restaurant, PDO::PARAM_INT);
        $consulta->bindValue(':p_mozo', $this->p_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':p_cocinero', $this->p_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $this->comentario, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function ObtenerEncuestaPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,codigo_mesa, codigo_comanda, 
                                                        p_mesa, p_restaurant, p_mozo, p_cocinero
                                                        FROM encuestas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function ObtenerEncuestaPorComanda($codigo_comanda)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT codigo_mesa, codigo_comanda, 
                                                        p_mesa, p_restaurant, p_mozo, p_cocinero
                                                        FROM encuestas WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }

    public static function modificarEncuesta($p_cocinero, $p_mesa, $p_mozo,
                                            $p_restaurant, $comentario, $codigo_comanda)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE encuestas SET p_mozo = :p_mozo, p_mesa= :p_mesa,
                                                        p_restaurant = :p_restaurant, p_cocinero = :p_cocinero,
                                                        comentario = :comentario
                                                        WHERE codigo_comanda = :codigo_comanda");
        $consulta->bindValue(':p_mozo', $p_mozo, PDO::PARAM_INT);
        $consulta->bindValue(':p_cocinero', $p_cocinero, PDO::PARAM_INT);
        $consulta->bindValue(':p_restaurant', $p_restaurant, PDO::PARAM_INT);
        $consulta->bindValue(':p_mesa', $p_mesa, PDO::PARAM_INT);
        $consulta->bindValue(':comentario', $comentario, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_comanda', $codigo_comanda, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function BorrarEncuesta($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM encuestas  
                                                        WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function mejoresComentarios(){

        $lista = Encuesta::ObtenerTodos();
        $auxMejores =[];
        foreach($lista as $enc){
            if($enc->GetPCocinero()>= 7 && $enc->GetPMesa()>=7 &&
                $enc->GetPMozo()>= 7 && $enc->GetPRestaurant()>= 7){
                    array_push($auxMejores, $enc);
                    echo "------"."\n";
                    echo "- ".$enc->GetComentario()."\n";

            }
        }
        return $auxMejores;
    }

    public static function peoresComentarios(){

        $lista = Encuesta::ObtenerTodos();
        $aux =[];
        echo "------Peores comentarios ----"."\n";
        foreach($lista as $enc){
            if($enc->GetPCocinero()<= 5 && $enc->GetPCocinero()!= -1 
                && $enc->GetPMesa()<=5 && $enc->GetPMesa()!= -1 &&
                $enc->GetPMozo()<= 5 && $enc->GetPMozo()!= -1 
                && $enc->GetPRestaurant()<= 5 && $enc->GetPRestaurant() != -1){
                    array_push($aux, $enc);
                    echo "- ".$enc->GetComentario()."\n";

            }
        }
        return $aux;
    }
}
?>