<?php

class Menu{
    public $id;
    public $producto;

    public function __construct($id, $producto){
        $this->id = $id;
        $this->producto = $producto;
    }

    public static function NewMenu($id, $producto){
        $aux = new Menu($id, $producto);
        return $aux;
    }

    public function GetId(){
        return $this->id;
    }

    public function GetProducto(){
        return $this->producto;
    }

    public static function verificarMenu($aux){
        switch($aux)
        {
            case 'Milanesa a caballo':
                return 1;
            case 'Hamburguesa':
                return 2;
            case 'Cerveza':
                return 3;
            case 'Daikiri':
                return 4;
            default:
                return 'Error';

        }
    }

    public static function demoraMenu($aux){
        switch($aux)
        {
            case 'Milanesa a caballo':
                return 30;
            case 'Hamburguesa':
                return 20;
            case 'Cerveza':
                return 5;
            case 'Daikiri':
                return 10;
            default:
                return 'Error';

        }
    }

    public static function puestoMenu($aux){
        switch($aux)
        {
            case 'Milanesa a caballo':
                return 'Cocinero';
            case 'Hamburguesa':
                return 'Cocinero';
            case 'Cerveza':
                return 'Cervecero';
            case 'Daikiri':
                return 'Bartender';
            default:
                return 'Error';

        }
    }

    // public static function precioMenu($aux){
    //     switch($aux)
    //     {
    //         case 'Milanesa a caballo':
    //             return 200;
    //         case 'Hamburguesa':
    //             return 100;
    //         case 'Cerveza':
    //             return 50;
    //         case 'Daikiri':
    //             return 60;
    //         default:
    //             return 'Error';

    //     }
    // }
}

?>