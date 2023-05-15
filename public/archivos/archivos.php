<?php

// use Fpdf\Fpdf;
require_once('./fpdf185/fpdf.php');

require_once "./models/menu.php";

class Archivos{

    public static function GenerarPDF(){

        // $ventas = Venta::obtenerTodos();
        $pdf=new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',11);
        $pdf->SetTitle("Comanda");

        $pdf->Cell(150,10,'Juliana Chaves Osorio - La comanda', 0,1);
        $pdf->Image('./assets/logo.png', 10, 8, 33);


        $pdf->Cell(60,10,'COMANDA', 0,1);
        $pdf->SetFont('Arial','',11);

    //     // foreach($ventas as $venta){                          // HACERLO CON EL TIPO DE VARIABLE A MOSTRAR EN EL PDF
    //     //                 $pdf->Cell(20, 10, Venta::ToString($venta));
    //     //     $pdf->Ln(10);            

    //     // }
        
        $pdf->Output('F', './PDFs/' . 'logo' .'.pdf', 'I');

// $pdf->Cell(40,10,'¡Hola, Mundo!');
$pdf->Output();
    }

    public function GetCSV(){
        $arc = 'menu.csv';
        $array = array();
        $fila =1;
        try{
            if (($gestor = fopen($arc, "r")) !== FALSE) {
                while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                    $numero = count($datos);
                    $fila++;
                    for ($c=0; $c < $numero; $c++) {
                        //echo $datos[$c] . "<br />\n";
                        array_push($array, $datos[$c]);
                    }
                }
                fclose($gestor);
            }

        }catch(\Throwable $th){
         echo "Error al intentar leer el archivo"; 
        }finally{
            return $array;
        }
    }

    public static function GuardarFoto($foto, $mesa)
    {
        $path= 'fotosMesas/';
        $extension = explode(".", $foto["name"]);
        $destino = $path.$mesa->GetMesa()."_".$mesa->GetComanda().'_'.end($extension);
        echo 'destino = '.$destino;

        if (!file_exists($path))
            mkdir('fotosMesas/', 0777, true);    

        if(move_uploaded_file($foto["tmp_name"],$destino))
            echo "\nImagen guardada con exito!\n";
        else{
            echo "Error";
            var_dump($foto["error"]);
        }
    }

    public static function NombreFoto($foto)
    {
        $nom = explode(".", $foto["name"]);
        return $nom[0];
    }
}

?>