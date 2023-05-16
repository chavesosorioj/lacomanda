<?php
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

// required controllers
 require_once './controllers/ComandaController.php';
 require_once './controllers/OrdenController.php';
 require_once './controllers/MesaController.php';
 require_once './controllers/EncuestaController.php';
 require_once './controllers/UsuarioController.php';

 // autenticacion
 require_once './middlewares/UsuariosMiddleware.php';

// Instantiate App
$app = AppFactory::create();
// Set base path
$app->setBasePath('/app/laComanda/public');

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response) {    
    $response->getBody()->write("GET => La Comanda - JC");
    return $response;

});

// peticiones Usuarios

 $app->group('/usuario', function (RouteCollectorProxy $group) {
     $group->post('/login', \UsuarioController::class . ':LogIn'); // obtengo el token

     $group->get('/traeruno/{mail}', \UsuarioController::class . ':TraerUno');
     $group->get('/traertodos', \UsuarioController::class . ':TraerTodos');
    

     //SOCIOS PETICIONES
     $group->post('/alta', \UsuarioController::class . ':CargarUno')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->put('/modificar', \UsuarioController::class . ':ModificarUno')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->delete('/borrar/{id}', \UsuarioController::class . ':BorrarUno')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

      $group->get('/ingresousuario/{id}', \UsuarioController::class . ':TraerIngreso')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/GenerarPDF', \UsuarioController::class . ':DescargarPDF')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
 });


//   // peticiones Mesa
 $app->group('/mesa', function (RouteCollectorProxy $group) {

   
    $group->post('/alta', \MesaController::class . ':CargarUno');
    //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

   $group->get('/traertodas', \MesaController::class . ':TraerTodos')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
  
   $group->get('/traeruna/{id}', \MesaController::class . ':TraerUno');
   //   ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

   $group->get('/traerporcomanda/{codigo_comanda}', \MesaController::class . ':TraerPorCodComanda');
//      ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

    $group->put('/modificar', \MesaController::class . ':ModificarUno')
      ->add(\UsuariosMiddleware::class . ':VerificaAccesoMozo');
     //  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

   $group->delete('/borrar/{codigo_mesa}', \MesaController::class . ':BorrarUno');
//      ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

//SOCIOS PETICIONES
     $group->get('/masusada', \MesaController::class . ':TraerMasUsada')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/menosusada', \MesaController::class . ':TraerMenosUsada')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
    
     $group->post('/importeporfecha', \MesaController::class . ':TraerImportePorFecha')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/importealto', \MesaController::class . ':TraerMasImporte')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/importebajo', \MesaController::class . ':TraerMenosImporte')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

});

//   // peticiones Comandas
   $app->group('/comanda', function (RouteCollectorProxy $group) {

    $group->post('/alta', \ComandaController::class . ':CargarUno');
  //  ->add(\UsuariosMiddleware::class . ':VerificaAccesoMozo');

     $group->get('/traertodas', \ComandaController::class . ':TraerTodos');

     $group->get('/traeruna/{codigo_comanda}', \ComandaController::class . ':TraerUno');
//   //  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

    $group->get('/traerporid/{id}', \ComandaController::class . ':TraerPorId');
// //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->put('/modificar', \ComandaController::class . ':ModificarUno');
// //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->delete('/borrar/{codigo_comanda}', \ComandaController::class . ':BorrarUno');
// //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

//SOCIOS SOLICITUDES
     $group->get('/baratacara', \ComandaController::class . ':BarataCara')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/entregadoatiempo', \ComandaController::class . ':TraerEntregadaTiempo')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/noentregadoatiempo', \ComandaController::class . ':TraerEntregadaDemora')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
  
    
 });

//   // peticiones Ordenes
   $app->group('/orden', function (RouteCollectorProxy $group) {

        $group->post('/alta', \OrdenController::class . ':CargarUno');
//    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traertodas', \OrdenController::class . ':TraerTodos');
//    // ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traerporcomanda/{codigo_comanda}', \OrdenController::class . ':TraerPorComanda'); 
//            ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traeruno/{id}', \OrdenController::class . ':TraerUno');
 //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traerlistasservir', \OrdenController::class . ':TraerListo')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoMozo');

     $group->put('/modificarpedido', \OrdenController::class . ':ModificarUno');
 //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

    $group->put('/modificarestado', \OrdenController::class . ':ModificarEstado');
 //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->delete('/borraruno', \OrdenController::class. ':BorrarUno');
 //    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

    $group->get('/totaldemora/{codigo_comanda}', \OrdenController::class . ':TotalDemora');

    $group->get('/traerporusuario/{idUsuario}', \OrdenController::class . ':TraerOrdenPorUsuario'); 
    // ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio'); 

    //SOCIOS PETICIONES
    $group->get('/totaloperaciones', \OrdenController::class . ':TraerTotalOperaciones')
    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio'); 

    $group->get('/masmenosvendido', \OrdenController::class . ':TraerListaMasMenosVendido')
    ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio'); 

    $group->get('/traeringresadasprep', \OrdenController::class . ':TraerIngresadasPrep')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traerordencantsector', \OrdenController::class . ':TraerOrdenCantSector')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traermasmenosvendido/{opcion}', \OrdenController::class . ':TraerMasMenosVendido')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

     $group->get('/traercanceladas', \OrdenController::class . ':TraerCanceladas')
     ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');
    
 });

// Encuestas
$app->group('/encuesta', function (RouteCollectorProxy $group) {

  $group->post('/alta', \EncuestaController::class . ':CargarUno');
  $group->get('/traertodas', \EncuestaController::class . ':TraerTodos');

  $group->get('/traeruna/{codigo_comanda}', \EncuestaController::class . ':TraerUno');
  $group->put('/modificar', \EncuestaController::class . ':ModificarUno');

  //SOCIOS PETICIONES
  $group->get('/traermejorescomentarios', \EncuestaController::class . ':TraerMejores')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

  $group->get('/traerpeorescomentarios', \EncuestaController::class . ':TraerPeores')
  ->add(\UsuariosMiddleware::class . ':VerificaAccesoSocio');

});

 $app->run();

