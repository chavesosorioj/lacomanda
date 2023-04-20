<?php
//use Slim\Psr7\Environment;

class AccesoDatos
{
    private static $objAccesoDatos;
    private $objetoPDO;



//     $dsn = 'mysql:host=localhost;dbname=nombre_de_la_base_de_datos;charset=utf8mb4';
// $usuario = 'nombre_de_usuario';
// $contrasena = 'contraseña';

// try {
//     $pdo = new PDO($dsn, $usuario, $contrasena);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     echo "Conexión exitosa a la base de datos.";
// } catch (PDOException $e) {
//     echo "Error de conexión a la base de datos: " . $e->getMessage();
// }



    private function __construct()
    {
        try {
            $this->objetoPDO = new PDO('mysql:host=127.0.0.1;dbname=la comanda;charset=utf8mb4', 
                                        'root', '',array(PDO::ATTR_EMULATE_PREPARES => false, 
                                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            $this->objetoPDO->exec("SET CHARACTER SET utf8");
        } catch (PDOException $e) {
            print "Error: " . $e->getMessage();
            die();
        }
    }

    public static function obtenerInstancia()
    {
        if (!isset(self::$objAccesoDatos)) {
            self::$objAccesoDatos = new AccesoDatos();
        }
        return self::$objAccesoDatos;
    }

    public function prepararConsulta($sql)
    {
        return $this->objetoPDO->prepare($sql);
    }

    public function obtenerUltimoId()
    {
        return $this->objetoPDO->lastInsertId();
    }

    public function __clone()
    {
        trigger_error('ERROR: La clonación de este objeto no está permitida', E_USER_ERROR);
    }


    // asi esatba antes el try catch
    // try {
    //     $this->objetoPDO = new PDO('mysql:host='.$_ENV['MYSQL_HOST'].';dbname='.$_ENV['MYSQL_DB'].';charset=utf8', 
    //                                 $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASS'], array(PDO::ATTR_EMULATE_PREPARES => false, 
    //                                 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    //     $this->objetoPDO->exec("SET CHARACTER SET utf8");
    // } catch (PDOException $e) {
    //     print "Error: " . $e->getMessage();
    //     die();
    // }
}