<?php

require_once('fpdf.php');



// ENCABEZADO Y PIE DE PÁGINA
// ==============================================================
$pie1 = "Textile Testing Services of America, S.A. de C.V.";
$pie2 = "Nueva Santo Domingo 205-A, Industrial San Antonio, C.P. 02760, Azcapotzalco, CDMX";
$pie3 = "http://www.ttsamexico.com";

 class PDF extends FPDF
 {
    //Cabecera de página
    function Header()
    {
      global $po;
      global $contenedor;
      global $proveedor;
      global $fecha_insp;
      $this->Image('../../img/logo_empresa.jpg',10,8,20,20,"JPG");
      $this->Image('../../img/logoWM.jpg',160,12,'45','',"jpg");
      $this->SetXY(35,8);
      $this->setFont('Helvetica', 'B', 12);
      $this->setTextColor(72, 129, 153);
      $this->Cell(120,5, utf8_decode('FINAL INSPECTION REPORT'), 0, 1, 'C');
      $this->Ln(4);
      $this->SetX(35);
      $this->setFont('Helvetica', 'B', 10);
      $this->MultiCell(120,4,$proveedor, 0, 'C');
      $this->SetX(35);
      $this->setFont('Helvetica', '', 8);
      $this->setTextColor(50,50,50);
      $this->Cell(120,4,utf8_decode('Inspection carried out on ').strftime('%d / %b / %Y', strtotime($fecha_insp)), 0, 1, 'C');
      $this->SetX(35);
      $this->setFont('Helvetica', 'B', 8);
      $this->Cell(120,4,'PO ' . $po . ' / Container ' . $contenedor , 0, 1, 'C');
      $this->Ln(4);

    }
    function Footer()
    {
      // Posición: a 1,5 cm del final
      global $pie1;
      global $pie2;
      global $pie3;
      $this->SetY(-20);
      $this->SetFont('Helvetica', '', 6);
      $this->setTextColor(128,128,128);
      $this->Cell(0,3, utf8_decode($pie1),0,1,'C');
      $this->Cell(0,3, utf8_decode($pie2),0,1,'C');
      $this->SetFont('Helvetica', 'BU', 6);
      $this->setTextColor(23,55,93);
      $this->Cell(0,3, utf8_decode($pie3), 0, 0, 'C', false, 'http://www.ttsamexico.com');
      $this->SetFont('Helvetica', '', 6);
      $this->setTextColor(128,128,128);

      $this->Cell(0,3, utf8_decode('Pág.').$this->PageNo().'/{nb}',0,0,'R');
    }

    // FUNCION PARA GENERAR ARRAYS Y MULTICELL
    function myCell($w, $h, $x, $t, $f){    
      $height = $h/3;
      $first = $height+2;
      $second = $height+$height+$height+3;
      $len = strlen($t);
      if ($len>15) {
        $txt = str_split($t,15);
        $this->SetX($x);
        $this->Cell($w,$first,$txt[0], '', '', 'C', $f);
        $this->SetX($x);
        $this->Cell($w,$second,$txt[1], '', '', 'C', $f);
        $this->SetX($x);
        $this->Cell($w,$h, '', 1, 0, 'C', $f);
      } else {
        $this->SetX($x);
        $this->Cell($w,$h, $t, 1, 0, 'C', $f);
      }
    }

    var $widths;
    var $aligns;
    var $fillings;

    function SetWidths($w){
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function SetFillings($f){
        //Set the array of column alignments
        $this->fillings=$f;
    }

    function Row($data){
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        $h=5*$nb;
        $height = 4;
        global $nl;
        
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $f=isset($this->fillings[$i]) ? $this->fillings[$i] : false;
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w,$height,$data[$i],0,$a,$f);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        // global $nl;
        return $nl;
    }
 }



// AJUSTES INICIALES DE LA PÁGINA
$pdf=new PDF('P','mm','Letter');
$pdf->AddPage();
$pdf->AliasNbPages();

$limite_sup = $pdf->GetY();
$limite_inf = 250;

// COLORES
// pimario: (72, 129, 153); // MENTA
// primariodark: (60, 107, 128); // MENTA OSCURO
// secundario: (94, 181, 159); // VERDE
// secundariodark: (73, 140, 123); // VERDE OSCURO
// borde: (50, 50, 50); // GRIS OSCURO
// borde: (206, 216, 226); // GRIS
// aceptado: (166, 244, 164); // VERDE CLARO
// rechazo: (230, 134, 138); // ROJO CLARO

// MEDIDA TOTAL DEL LARGO DE LÍNEA 195

$pdf->SetFont('Helvetica','B',12);
$pdf->SetFillColor(72, 129, 153); // MENTA
$pdf->SetTextColor(255,255,255);  // COLOR BLANCO
$pdf->SetDrawColor(192,204,216);  // COLOR GRIS


$pdf->Cell(195, 8, 'GENERAL DATA', 0, 1, 'C', 1);

// VOLCADO DE DATOS GENERALES DEL MODELO
// ==============================================================
$pdf->SetFont('Helvetica', '', 8);
$pdf->setTextColor(50, 50, 50); // GRIS OSCURO
$pdf->setFillColor(208, 216, 226); // GRIS CLARO

$pdf->SetWidths(array(30, 65, 35, 65));
$pdf->SetAligns(array('R', 'C', 'R', 'C'));

$pdf->Row(array(
  utf8_decode('Report / Inspection'),
  utf8_decode($idinsp),
  utf8_decode('Date'),
  strftime('%d / %m / %Y', strtotime($fecha_insp))
));

$pdf->Row(array(
  utf8_decode('PO'),
  utf8_decode($po),
  utf8_decode('Container'),
  utf8_decode($contenedor)
));

$pdf->Cell(30, 6, 'Supplier', 1, 0, 'R', 0);
$pdf->Cell(165, 6, utf8_decode($proveedor), 1, 1, 'C', 0);

$pdf->Row(array(
  utf8_decode('Description'),
  utf8_decode($descr1),
  utf8_decode('Description 2'),
  utf8_decode($descr2)
));

$pdf->Row(array(
  utf8_decode('Model'),
  utf8_decode($modelo),
  utf8_decode('UPC\'s'),
  utf8_decode($upc)
));

$pdf->Row(array(
  utf8_decode('No. Department'),
  utf8_decode($depto),
  utf8_decode('Squad'),
  utf8_decode($squad)
));

$pdf->Row(array(
  utf8_decode('Tribe'),
  utf8_decode($tribu),
  utf8_decode('Tribal leader'),
  utf8_decode($resp_tribu)
));

$pdf->Row(array(
  utf8_decode('System'),
  utf8_decode($sistema),
  utf8_decode('Merchandise type'),
  utf8_decode($tipo_mercancia)
));

$pdf->Row(array(
  utf8_decode('Company'),
  utf8_decode($operadora),
  utf8_decode('Place of inspection'),
  utf8_decode($lugar_insp)
));

$pdf->Row(array(
  utf8_decode('Unit cost'),
  'MXN $ ' . number_format($costo_unit,2),
  utf8_decode('PO cost'),
  'MXN $ ' . number_format($costo_po, 2)
));

$pdf->Row(array(
  utf8_decode('Colors'),
  utf8_decode('VARIOUS COLORS'),
  utf8_decode('Sizes'),
  utf8_decode($tallas)
));

$pdf->Row(array(
  utf8_decode('Follow up category'),
  utf8_decode($cat_seguimiento),
  utf8_decode('Country'),
  utf8_decode($paisorig)
));

$pdf->Row(array(
  utf8_decode('Brand'),
  utf8_decode($marca),
  utf8_decode('Brand type'),
  utf8_decode($tipomarca)
));

$pdf->Row(array(
  utf8_decode('Stage'),
  utf8_decode($escenario),
  utf8_decode('Stage responsible'),
  utf8_decode($resp_escenario)
));

$pdf->Row(array(
  utf8_decode('Silica container'),
  utf8_decode($silica_pza),
  utf8_decode('Silica Master Pack (Box)'),
  utf8_decode($silica_caja)
));

$pdf->Row(array(
  utf8_decode('Silica Piece'),
  utf8_decode($silica_pza2),
  utf8_decode('Desiccant type'),
  utf8_decode($desecante)
));

$pdf->Row(array(
  utf8_decode('Download start'),
  strftime("%I:%M %p", strtotime($inidesc) ),
  utf8_decode('End of download'),
  strftime("%I:%M %p", strtotime($findesc) )
));

$pdf->Row(array(
  utf8_decode('Total pieces'),
  number_format($pzas,0),
  utf8_decode('Inspected pieces'),
  number_format($pzas_insp,0)
));

$pdf->Row(array(
  '',
  '',
  utf8_decode('Remaining pieces'),
  number_format($pzas_rem,0)
));

$pdf->Row(array(
  utf8_decode('Total boxes'),
  number_format($cajas,0),
  utf8_decode('Inspected boxes'),
  number_format($cajas_insp,0)
));

$pdf->Row(array(
  '',
  '',
  utf8_decode('Remaining boxes'),
  number_format($cajas_rem,0)
));

$pdf->Row(array(
  utf8_decode('Inspection level'),
  utf8_decode($nivelinsp),
  utf8_decode('Can remnants be sanitized?'),
  utf8_decode($sanit)
));

$pdf->Cell(30, 6, 'Final remarks', 1, 0, 'R', 0);
$pdf->MultiCell(165, 6, utf8_decode($obs), 1, 'C', 0);

$pdf->SetFillColor(166, 244, 164); // VERDE CLARO
if ($estatus_final == 'REJECT') {
  $pdf->SetFillColor(230, 134, 138); // ROJO CLARO
}
$pdf->SetFillings(array(0, 0, 0, 1));
$pdf->Row(array(
  utf8_decode('Findings'),
  utf8_decode($findings),
  utf8_decode('Final status'),
  utf8_decode($estatus_final)
));
// $pdf->Cell(30, 6, utf8_decode('Findings'), 1, 0, 'R', 0);
// $pdf->Cell(65, 6, utf8_decode($findings), 1, 0, 'C', 0);
// $pdf->Cell(35, 6, utf8_decode('Final status'), 1, 0, 'R', 0);
// $pdf->Cell(65, 6, utf8_decode($estatus_final), 1, 1, 'C', 1);

$pdf->Ln(10);



## =========================================== ##
##         TABLA DE DEFECTOS PIEZAS            ##
## =========================================== ##
$pdf->SetFont('Helvetica','B',12);
$pdf->SetFillColor(72, 129, 153); // MENTA
$pdf->SetTextColor(255,255,255);  // COLOR BLANCO

$pdf->Cell(195, 8, utf8_decode('PIECES SAMPLING: ') . $muestreopzas, 0, 1, 'C', 1);

$qtotales = $conexion->query(" SELECT 
              A.totales AS totalesvisual,
              B.totales AS totalesetiquetado

              FROM $db.inspecciones02 A

              LEFT JOIN $db.inspecciones03 B
              ON A.partida = B.partida
              AND (A.idinsp = B.idinsp
              AND A.idbase = B.idbase)

              WHERE A.idinsp = $idinsp
              AND A.idbase = $idbase
              AND (A.totales > 0
              OR B.totales > 0)  ")
    or die ("Error al buscar el total de defectos para piezas");
$totdefpiezas = $qtotales->num_rows;
if ($totdefpiezas > 0) {


$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetFillColor(94, 181, 159); // VERDE
$pdf->SetTextColor(50,50,50);    // COLOR NEGRO

$pdf->Cell(45, 6, utf8_decode('Defective'), 1, 0, 'C', 1);
$pdf->Cell(50, 6, utf8_decode('Pieces'), 1, 0, 'C', 1);
$pdf->Cell(50, 6, utf8_decode('Percentage'), 1, 0, 'C', 1);
$pdf->Cell(50, 6, utf8_decode('Percentage of pieces'), 1, 1, 'C', 1);

// Defectos visuales
for ($i=1; $i <= 10 ; $i++) {
  $consultadef = 'def'.$i;
  // echo $consultadef."<br>";
  $def = 0;
  $cantdef = 0;
  $porcdef = 0;
  $porcpzas = 0;
  $qdef = $conexion->query("SELECT
              A.$consultadef,
              COUNT('A.$consultadef') AS cantdef,

              B.description

              FROM inspecciones02 A

              LEFT JOIN $db.defectivos B
              ON A.$consultadef = B.clave

              WHERE idinsp = $idinsp
              AND idbase = $idbase
              AND totales > 0
              AND $consultadef != '-'
              GROUP BY $consultadef ")
      or die ("Error al obtener los defectos visuales (INSPECCIONES02) <br> " . mysqli_error($conexion) );
  if ($qdef->num_rows > 0){
      while ($fdef = $qdef->fetch_assoc()){
        $def = $fdef[$consultadef];
        $descrdef = $fdef['description'];
        $cantdef = $fdef['cantdef'];
        $porcdef = ($cantdef * 100) / $muestreopzas;
        // $porcpzas = ($porcdef * $pzas_insp) / 100;
        $porcpzas = ($porcdef / 100) * $pzas;

$pdf->SetFont('Helvetica', '', 8);

$pdf->Cell(45, 6, utf8_decode($descrdef), 1, 0, 'C', 0);
$pdf->Cell(50, 6, $cantdef, 1, 0, 'C', 0);
$pdf->Cell(50, 6, number_format($porcdef,2).'%', 1, 0, 'C', 0);
$pdf->Cell(50, 6, number_format(ceil($porcpzas), 0), 1, 1, 'C', 0);


      } // fin del while
      $qdef->free();
  }  // fin del if
}  // fin del for (defectos visuales)


// Defectos de etiquetado
for ($i=1; $i <= 10 ; $i++) {
  $consultadef = 'def'.$i;
  // echo $consultadef."<br>";
  $def = 0;
  $cantdef = 0;
  $porcdef = 0;
  $porcpzas = 0;
  $qdef = $conexion->query("SELECT
              A.$consultadef,
              COUNT('A.$consultadef') AS cantdef,

              B.description

              FROM inspecciones03 A

              LEFT JOIN $db.defectivos B
              ON A.$consultadef = B.clave

              WHERE idinsp = $idinsp
              AND idbase = $idbase
              AND totales > 0
              AND $consultadef != '-'
              GROUP BY $consultadef ")
      or die ("Error al obtener los defectos de etiquetado (INSPECCIONES03) <br> " . mysqli_error($conexion) );
  if ($qdef->num_rows > 0){
      while ($fdef = $qdef->fetch_assoc()){
        $def = $fdef[$consultadef];
        $descrdef = $fdef['description'];
        $cantdef = $fdef['cantdef'];
        $porcdef = ($cantdef * 100) / $muestreopzas;
        // $porcpzas = ($porcdef * $pzas_insp) / 100;
        $porcpzas = ($porcdef / 100) * $pzas;


$pdf->Cell(45, 6, utf8_decode($descrdef), 1, 0, 'C', 0);
$pdf->Cell(50, 6, $cantdef, 1, 0, 'C', 0);
$pdf->Cell(50, 6, number_format($porcdef,2).'%', 1, 0, 'C', 0);
$pdf->Cell(50, 6, number_format(ceil($porcpzas), 0), 1, 1, 'C', 0);


      } // fin del while
      $qdef->free();
  }  // fin del if
}  // fin del for (defectos de etiquetado)

$qtotales->free();
} else {

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO

$pdf->Cell(195, 6, utf8_decode('No defectives found'), 1, 0, 'C', 0);

}

$pdf->Ln(10);




## =========================================== ##
##         TABLA DE DEFECTOS CAJAS             ##
## =========================================== ##
$pdf->SetFont('Helvetica','B', 12);
$pdf->SetFillColor(72, 129, 153); // MENTA
$pdf->SetTextColor(255,255,255);  // COLOR BLANCO

$pdf->Cell(195, 8, utf8_decode('BOX SAMPLING: ') . $muestreocajas, 0, 1, 'C', 1);


$qtotales = $conexion->query(" SELECT 
              totales
              FROM $db.inspecciones01
              WHERE idinsp = $idinsp
              AND idbase = $idbase
              AND totales > 0  ")
    or die ("Error al buscar el total de defectos para cajas");
$totdefcajas = $qtotales->num_rows;
if ($totdefcajas > 0) {


$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetFillColor(94, 181, 159); // VERDE
$pdf->SetTextColor(50,50,50);    // COLOR NEGRO

$pdf->Cell(45, 6, utf8_decode('Defective'), 1, 0, 'C', 1);
$pdf->Cell(50, 6, utf8_decode('Boxes'), 1, 0, 'C', 1);
$pdf->Cell(50, 6, utf8_decode('Percentage'), 1, 0, 'C', 1);
$pdf->Cell(50, 6, utf8_decode('Percentage of boxes'), 1, 1, 'C', 1);

// Defectos de empaque
for ($i=1; $i <= 10 ; $i++) {
  $consultadef = 'def'.$i;
  // echo $consultadef."<br>";
  $def = 0;
  $cantdef = 0;
  $porcdef = 0;
  $porcpzas = 0;
  $qdef = $conexion->query("SELECT
              A.$consultadef,
              COUNT('A.$consultadef') AS cantdef,

              B.description

              FROM inspecciones01 A

              LEFT JOIN $db.defectivos B
              ON A.$consultadef = B.clave

              WHERE idinsp = $idinsp
              AND idbase = $idbase
              AND totales > 0
              AND $consultadef != '-'
              GROUP BY $consultadef ")
      or die ("Error al obtener los defectos de empaque(INSPECCIONES01) <br> " . mysqli_error($conexion) );
  if ($qdef->num_rows > 0){
      while ($fdef = $qdef->fetch_assoc()){
        $def = $fdef[$consultadef];
        $descrdef = $fdef['description'];
        $cantdef = $fdef['cantdef'];
        $porcdef = ($cantdef * 100) / $muestreocajas;
        // $porcpzas = ($porcdef * $cajas_insp) / 100;
        $porcpzas = ($porcdef / 100) * $cajas;

$pdf->SetFont('Helvetica', '', 8);

$pdf->Cell(45, 6, utf8_decode($descrdef), 1, 0, 'C', 0);
$pdf->Cell(50, 6, $cantdef, 1, 0, 'C', 0);
$pdf->Cell(50, 6, number_format($porcdef,2).'%', 1, 0, 'C', 0);
$pdf->Cell(50, 6, number_format(ceil($porcpzas), 0), 1, 1, 'C', 0);


      } // fin del while
      $qdef->free();
  }  // fin del if
}  // fin del for (defectos de empaque)


$qtotales->free();
} else {

$pdf->SetFont('Helvetica', 'B', 8);
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO
$pdf->Cell(195, 6, utf8_decode('No defectives found'), 1, 0, 'C', 0);

}

$pdf->Ln(15);






## ******************************** ##
##   TABLA RELACIÓN CAJAS / PIEZAS  ##
## ******************************** ##
//Obtener la posición en Y para poner imágenes
if ($estatus_final == 'RECHAZO'){

$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$querycajas = " SELECT * FROM $db.inspecciones01 
      WHERE idinsp = $idinsp AND idbase = $idbase ";


// Obtener los tipos de defecto en cajas y armarlos en un array
$array_cajas = [];
$array_pzasxcaja = [];
$querycajas = $conexion->query(" SELECT
          id,
          partida,
          numcarton

          FROM $db.inspecciones01

          WHERE idinsp = $idinsp
          AND idbase = $idbase ")
    or die ("Error al obtener los número de cajas y cartón (INSPECCIONES01) <br> " . mysqli_error($conexion) );

while ($filacajas = $querycajas->fetch_assoc()) {
  $idcaja = $filacajas['id'];
  $numcaja = $filacajas['partida'];
  $numcarton = $filacajas['numcarton'];

  $qpzasxcaja = $conexion->query("SELECT
                COUNT(totales) AS pzasxcaja
                FROM $db.inspecciones02
                WHERE partida_caja = $numcaja
                AND idinsp = $idinsp
                AND idbase = $idbase ")
      or die ("Error al obtener las piezas x caja (INSPECCIONES03) <br> " . mysqli_error($conexion));
          
  $filapzasxcaja = $qpzasxcaja->fetch_assoc();
  $pzasxcaja = $filapzasxcaja['pzasxcaja'];
  $qpzasxcaja->free();
  
  $array_cajas[$numcaja]['numcaja'] = $numcaja;
  $array_cajas[$numcaja]['carton'] = $numcarton;
  $array_cajas[$numcaja]['pzasxcaja'] = $pzasxcaja;
  $array_cajas[$numcaja]['defectos'] = [];

  $array_pzasxcaja[] = $pzasxcaja;

  for ($i=1; $i <= 10 ; $i++) {
      $consultadef = 'def'.$i;
      $querydefcajas = $conexion->query(" SELECT 
                  A.$consultadef,
                  B.description

                  FROM $db.inspecciones01 A

                  JOIN $db.defectivos B
                  ON A.$consultadef = B.clave

                  WHERE A.id = $idcaja
                  ")
          or die ("Error al obtener los defectos de cada caja <br>" . mysqli_error($conexion) );
      if ($querydefcajas->num_rows > 0) {
          while ($filadefcajas = $querydefcajas->fetch_assoc()) {
              $array_cajas[$numcaja]['defectos'][] = $filadefcajas['description'];
          }
          $querydefcajas->free();
      }
  }
      
  $array_cajas[$numcaja]['defectos'] = implode(" / ", $array_cajas[$numcaja]['defectos']);
  
}
$querycajas->free();

$maxpzasxcaja = max($array_pzasxcaja);


// Obtener los tipos de defecto en piezas y armarlos en un array
$array_pzas = [];
$querypzas = $conexion->query(" SELECT
              id,
              partida,
              partida_caja

              FROM $db.inspecciones02

              WHERE idinsp = $idinsp
              AND idbase = $idbase ")
    or die ("Error al obtener los número de pzas (INSPECCIONES02) <br> " . mysqli_error($conexion) );

while ($filapzas = $querypzas->fetch_assoc()) {
    $idpza = $filapzas['id'];
    $numpza = $filapzas['partida'];
    $caja = $filapzas['partida_caja'];

    $array_pzas[$numpza]['numpza'] = $numpza;
    $array_pzas[$numpza]['caja'] = $caja;
    $array_pzas[$numpza]['defpza'] = [];

    for ($i=1; $i <= 10 ; $i++) { 
        $defpza = 'def'.$i;

        // Obtener defectos visuales
        $querydefpzas = $conexion->query(" SELECT
                      A.$defpza,
                      C.description

                      FROM $db.inspecciones02 A

                      JOIN $db.defectivos C
                      ON A.$defpza = C.clave

                      WHERE A.id = $idpza;
                      ")
          or die ("Error al obtener los defectos visuales de cada pieza <br>" . mysqli_error($conexion) );
        if ($querydefpzas->num_rows > 0) {
            while ($filadefpzas = $querydefpzas->fetch_assoc()) {
                $array_pzas[$numpza]['defpza'][] = $filadefpzas['description'];
            }
            $querydefpzas->free();
        }



        // Obtener defectos de etiquetatado
        $querydefpzas = $conexion->query(" SELECT
                      A.$defpza,
                      C.description

                      FROM $db.inspecciones03 A

                      JOIN $db.defectivos C
                      ON A.$defpza = C.clave

                      WHERE A.id = $idpza;
                      ")
          or die ("Error al obtener los defectos de etiquetado de cada pieza <br>" . mysqli_error($conexion) );
        if ($querydefpzas->num_rows > 0) {
            while ($filadefpzas = $querydefpzas->fetch_assoc()) {
                $array_pzas[$numpza]['defpza'][] = $filadefpzas['description'];
            }
            $querydefpzas->free();
        }
    }

    $array_pzas[$numpza]['defpza'] = implode(" / ", $array_pzas[$numpza]['defpza']);
  
}

$querypzas->free();



for ($i=1; $i <= count($array_pzas) ; $i++) {
    for ($j=1; $j <= count($array_cajas) ; $j++) { 
        if ($array_pzas[$i]['caja'] == $array_cajas[$j]['numcaja']) {
            $array_pzas[$i]['carton'] = $array_cajas[$j]['carton'];
            $array_pzas[$i]['defcaja'] = $array_cajas[$j]['defectos'];
        }
    }
}



// $pdf->AliasNbPages();
$pdf->SetFont('Helvetica','B',12);
$pdf->SetFillColor(72, 129, 153); // MENTA
$pdf->SetTextColor(255,255,255);  // COLOR BLANCO

$pdf->Cell(195, 8, 'Box & pieces related list', 1, 1, 'C', 1);

$pdf->SetFont('Helvetica','B', 8);
$pdf->SetFillColor(94, 181, 159); // VERDE
$pdf->SetTextColor(50, 50, 50); // GRIS OSCURO

$pdf->Cell(55, 6, utf8_decode('Box'), 1, 0, 'C', 1);
$pdf->Cell(60, 6, utf8_decode('Box defective'), 1, 0, 'C', 1);
$pdf->Cell(20, 6, utf8_decode('Piece'), 1, 0, 'C', 1);
$pdf->Cell(60, 6, utf8_decode('Piece defective'), 1, 1, 'C', 1);

$pdf->SetFont('Helvetica','', 8);
$pdf->SetWidths(array(55, 60, 20, 60));
$pdf->SetFillings(array(0, 0, 0, 0));
$pdf->SetAligns(array('C', 'L', 'C', 'L'));

foreach($array_pzas as $key => $value){
  $yini = $pdf->GetY();
  if($yini > 260){
    $pdf->AddPage();

    $pdf->SetFont('Helvetica','B', 8);

    $pdf->Cell(195, 6, utf8_decode('...box & pieces related list continuation'), 1, 1, 'C', 1);

    $pdf->SetFont('Helvetica','', 8);
  }

  
  $pdf->Row(array(
    utf8_decode('Box ' . $value['caja'] . ' / Cardboard ' . $value['carton']),
    utf8_decode($value['defcaja']),
    utf8_decode($value['numpza']),
    utf8_decode($value['defpza'])
  ));


}

$pdf->Ln(10);
}





## ***************************************** ##
##                    FOTOS                  ##
## ***************************************** ##
$pdf->SetFont('Helvetica','B',12);
$pdf->SetTextColor(72, 129, 153); // MENTA

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();
if($yini > 180){ $pdf->AddPage(); }


$pdf->Cell(195, 8, utf8_decode('PHOTOGRAPHIC EVIDENCE'), 0, 1, 'C', 0);

$pdf->Ln(5);



## ----------------------------------------- ##
//       Fotos de producto y etiquetas       //
## ----------------------------------------- ##
$pdf->SetTextColor(255, 255, 255); // COLOR BLANCO
$pdf->SetFillColor(72, 129, 153); // MENTA

$pdf->Cell(195, 8, utf8_decode('PRODUCT & LABELLING'), 0, 1, 'C', 1);

$prefijo = "PRODUCTO/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";
$ruta = "../../img/inspecciones/";

$qfotosprenda = $conexion->query("SELECT *
            FROM $db.inspecciones04
            WHERE idinsp = $idinsp
            AND idbase = $idbase
            AND tipo = 'PRODUCTO' ")
    or die ("Error al obtener las fotos del producto (INSPECCIONES04) <br> " . mysqli_error($conexion) );
$ffotosprenda = $qfotosprenda->fetch_assoc();

$foto1 = ($ffotosprenda['foto1']) ? $ruta.$prefijo.$ffotosprenda['foto1'] : $ruta."camara_bn.jpg";
$foto2 = ($ffotosprenda['foto2']) ? $ruta.$prefijo.$ffotosprenda['foto2'] : $ruta."camara_bn.jpg";
$foto3 = ($ffotosprenda['foto3']) ? $ruta.$prefijo.$ffotosprenda['foto3'] : $ruta."camara_bn.jpg";
$foto4 = ($ffotosprenda['foto4']) ? $ruta.$prefijo.$ffotosprenda['foto4'] : $ruta."camara_bn.jpg";
$foto5 = ($ffotosprenda['foto5']) ? $ruta.$prefijo.$ffotosprenda['foto5'] : $ruta."camara_bn.jpg";
$foto6 = ($ffotosprenda['foto6']) ? $ruta.$prefijo.$ffotosprenda['foto6'] : $ruta."camara_bn.jpg";

$qfotosprenda->free();

$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(94, 181, 159); // VERDE
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(65, 6, 'Photo 1', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 2', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 3', 1, 1, 'C', 1);

$yini = $pdf->GetY();


$pdf->Image($foto1, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto2, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto3, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$yini = $pdf->GetY();
if($yini > 200){
  $pdf->AddPage();

  $pdf->Cell(195, 6, utf8_decode('...product & labelling continuation'), 1, 1, 'C', 1);
}


$pdf->Cell(95, 6, 'Photo 4', 1, 0, 'C', 1);
$pdf->Cell(100, 6, 'Photo 5', 1, 1, 'C', 1);

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();

$pdf->Image($foto4, 30, $yini, 60, 45, 'JPG');
$pdf->Image($foto5, 125, $yini, 60, 45, 'JPG');

$pdf->Ln(45);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$pdf->Ln(15);   // Salto de línea (separación)

## FIN DE FOTOGRAFÍAS DE PRODUCTO Y ETIQUETAS
## ************************************************************





## ----------------------------------------- ##
//        Fotos de microscopio               //
## ----------------------------------------- ##
$pdf->SetFont('Helvetica','B',12);
$pdf->SetTextColor(255, 255, 255); // COLOR BLANCO
$pdf->SetFillColor(72, 129, 153); // MENTA

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(195, 8, utf8_decode('MICROSCOPE'), 1, 1, 'C', 1);

// Consulta para obtener fotos del microscopio
$prefijo = "MICROSCOPIO/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";

$qfotosmicro = $conexion->query("SELECT *
        FROM $db.inspecciones04
        WHERE idinsp = $idinsp
        AND idbase = $idbase
        AND tipo = 'MICROSCOPIO' ")
    or die ("Error al obtener las fotos del producto (INSPECCIONES04) <br> " . mysqli_error($conexion) );

if ($qfotosmicro->num_rows > 0) {

$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO
$pdf->SetFillColor(94, 181, 159); // VERDE

    $ffotosmicro = $qfotosmicro->fetch_assoc();
    $foto1 = ($ffotosmicro['foto1'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto1'] : $ruta."camara_bn.jpg";
    $foto2 = ($ffotosmicro['foto2'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto2'] : $ruta."camara_bn.jpg";
    $foto3 = ($ffotosmicro['foto3'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto3'] : $ruta."camara_bn.jpg";
    $foto4 = ($ffotosmicro['foto4'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto4'] : $ruta."camara_bn.jpg";
    $foto5 = ($ffotosmicro['foto5'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto5'] : $ruta."camara_bn.jpg";
    $foto6 = ($ffotosmicro['foto6'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto6'] : $ruta."camara_bn.jpg";
    $qfotosmicro->free();


$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(65, 6, 'Photo 1', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 2', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 3', 1, 1, 'C', 1);

$yini = $pdf->GetY();


$pdf->Image($foto1, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto2, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto3, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$yini = $pdf->GetY();
if($yini > 200){
  $pdf->AddPage();

  $pdf->Cell(195, 6, utf8_decode('...microscope continuation'), 1, 1, 'C', 1);
}


$pdf->Cell(65, 6, 'Photo 4', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 5', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 6', 1, 1, 'C', 1);

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();

$pdf->Image($foto4, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto5, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto6, 145, $yini, 60, 45, 'JPG');

$pdf->Ln(45);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)


} else {

  $pdf->SetFont('Helvetica','B',8);
  $pdf->SetTextColor(50, 50, 50); // COLOR GRIS OSCURO

  $pdf->Cell(195, 8, utf8_decode('No photographic evidence to show'), 1, 1, 'C', 0);

}

$pdf->Ln(15);   // Salto de línea (separación)


## FIN DE FOTOGRAFÍAS DE MICROSCOPIO
## ************************************************************







## ----------------------------------------- ##
//          Fotos de contenedor              //
## ----------------------------------------- ##
$pdf->SetFont('Helvetica','B',12);
$pdf->SetTextColor(255, 255, 255); // COLOR BLANCO
$pdf->SetFillColor(72, 129, 153); // MENTA

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();
if($yini > 220){ $pdf->AddPage(); }

$pdf->Cell(195, 8, utf8_decode('CONTAINER'), 1, 1, 'C', 1);

// Consulta para obtener fotos del contenedor
$prefijo = "CONTENEDOR/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";

$qfotosmicro = $conexion->query("SELECT *
        FROM $db.inspecciones04
        WHERE idinsp = $idinsp
        AND idbase = $idbase
        AND tipo = 'CONTENEDOR' ")
    or die ("Error al obtener las fotos del producto (INSPECCIONES04) <br> " . mysqli_error($conexion) );

if ($qfotosmicro->num_rows > 0) {

$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO
$pdf->SetFillColor(94, 181, 159); // VERDE

    $ffotosmicro = $qfotosmicro->fetch_assoc();
    $foto1 = ($ffotosmicro['foto1'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto1'] : $ruta."camara_bn.jpg";
    $foto2 = ($ffotosmicro['foto2'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto2'] : $ruta."camara_bn.jpg";
    $foto3 = ($ffotosmicro['foto3'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto3'] : $ruta."camara_bn.jpg";
    $foto4 = ($ffotosmicro['foto4'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto4'] : $ruta."camara_bn.jpg";
    $foto5 = ($ffotosmicro['foto5'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto5'] : $ruta."camara_bn.jpg";
    $foto6 = ($ffotosmicro['foto6'] != '-') ? $ruta.$prefijo.$ffotosmicro['foto6'] : $ruta."camara_bn.jpg";
    $qfotosmicro->free();


$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(65, 6, 'Photo 1', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 2', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 3', 1, 1, 'C', 1);

$yini = $pdf->GetY();


$pdf->Image($foto1, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto2, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto3, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$yini = $pdf->GetY();
if($yini > 200){
  $pdf->AddPage();

  $pdf->Cell(195, 6, utf8_decode('...container continuation'), 1, 1, 'C', 1);
}


$pdf->Cell(65, 6, 'Photo 4', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 5', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 6', 1, 1, 'C', 1);

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();

$pdf->Image($foto4, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto5, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto6, 145, $yini, 60, 45, 'JPG');

$pdf->Ln(45);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)


} else {

  $pdf->SetFont('Helvetica','B',8);
  $pdf->SetTextColor(50, 50, 50); // COLOR GRIS OSCURO

  $pdf->Cell(195, 8, utf8_decode('No photographic evidence to show'), 1, 1, 'C', 0);

}

$pdf->Ln(15);   // Salto de línea (separación)


## FIN DE FOTOGRAFÍAS DE CONTENEDOR
## ************************************************************





## ----------------------------------------- ##
//       Fotos de sílica / desecante         //
## ----------------------------------------- ##
if ($silica_pza == 'YES' || $silica_caja == 'YES' || $silica_pza2 == 'YES'){

$pdf->SetFont('Helvetica','B',12);
$pdf->SetTextColor(255, 255, 255); // COLOR BLANCO
$pdf->SetFillColor(72, 129, 153); // MENTA

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(195, 8, utf8_decode('DESICCANT PHOTOS: ' . $desecante), 1, 1, 'C', 1);

$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO
$pdf->SetFillColor(94, 181, 159); // VERDE

// $pdf->Cell(95, 6, 'Container', 1, 0, 'C', 1);
// $pdf->Cell(100, 6, 'Box', 1, 1, 'C', 1);
$pdf->Cell(65, 6, 'Container', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Box', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Piece', 1, 1, 'C', 1);


// Consulta para obtener fotos de las silicas
$foto_silicap = $ruta."camara_bn.jpg";
$foto_silicac = $ruta."camara_bn.jpg";
$foto_silicap2 = $ruta."camara_bn.jpg";

$qfotos_silica = $conexion->query(" SELECT tipo, foto1
                  FROM $db.inspecciones04
                  WHERE idinsp = $idinsp
                  AND idbase = $idbase
                  AND (tipo = 'SILPZA' OR 
                       tipo = 'SILCAJA' OR
                       tipo = 'SILPZA2')
                  ")
              or die ("Error al obtener las fotos de la sílica (INSPECCIONES04) <br> " . mysqli_error($conexion) );
// echo $qfotos_silica->num_rows;
while ($ffotos_silica = $qfotos_silica->fetch_assoc()) {
  $tipo_silica = $ffotos_silica['tipo'];

  switch ($tipo_silica) {
    case 'SILPZA':
      $prefijo = "SILPZA/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";
      $foto_silicap = $ruta.$prefijo.$ffotos_silica['foto1'];
      break;

    case 'SILCAJA':
      $prefijo = "SILCAJA/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";
      $foto_silicac = $ruta.$prefijo.$ffotos_silica['foto1'];
      break;

    case 'SILPZA2':
      $prefijo = "SILPZA2/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";
      $foto_silicap2 = $ruta.$prefijo.$ffotos_silica['foto1'];
      break;
  }
}
$qfotos_silica->free();

$yini = $pdf->GetY();

// $pdf->Image($foto_silicap, 30, $yini, 60, 45, 'JPG');
// $pdf->Image($foto_silicac, 125, $yini, 60, 45, 'JPG');
$pdf->Image($foto_silicap, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto_silicac, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto_silicap2, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)


$pdf->Ln(15);   // Salto de línea (separación)

}
## FIN DE FOTOGRAFÍAS DE SILICA / DESECANTE
## ************************************************************





## ***************************************** ##
##        FOTOS DE LOS DEFECTOS              ##
## ***************************************** ##
if ($totdefpiezas > 0 || $totdefcajas > 0) {
$yini = $pdf->GetY();
if ($yini > 220){ $pdf->AddPage(); }


$pdf->SetFont('Helvetica','B',12);
$pdf->SetFillColor(72, 129, 153); // MENTA
$pdf->SetTextColor(255, 255, 255); // BLANCO

$pdf->Cell(195, 8, utf8_decode('DEFECTIVES'), 1, 1, 'C', 1);



## ----------------------------------------- ##
//    Fotos de defectos de empaque           //
## ----------------------------------------- ##
if ($empaque == 'S') {
  $prefijo = "EMPAQUE/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";

  $qfotosempaque = $conexion->query("SELECT A.*,
        B.description

        FROM $db.inspecciones04 A

        LEFT JOIN $db.defectivos B
        ON A.defectivo = B.clave

        WHERE idinsp = $idinsp
        AND idbase = $idbase
        AND A.tipo = 'EMPAQUE' ")
    or die ("Error al obtener las fotos del producto (INSPECCIONES04) <br> " . mysqli_error($conexion) );

$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(60, 107, 128); // MENTA OSCURO
$pdf->SetTextColor(255,255,255);  // BLANCO

$pdf->Cell(195, 6, 'Packaging defectives', 1, 1, 'C', 1);

  if ($qfotosempaque->num_rows > 0) {
      while ($ffotosempaque = $qfotosempaque->fetch_assoc()) {
        $descripcion = $ffotosempaque['description'];
        $foto1 = ($ffotosempaque['foto1'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto1'] : $ruta."camara_bn.jpg";
        $foto2 = ($ffotosempaque['foto2'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto2'] : $ruta."camara_bn.jpg";
        $foto3 = ($ffotosempaque['foto3'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto3'] : $ruta."camara_bn.jpg";
        $foto4 = ($ffotosempaque['foto4'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto4'] : $ruta."camara_bn.jpg";
        $foto5 = ($ffotosempaque['foto5'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto5'] : $ruta."camara_bn.jpg";
        $foto6 = ($ffotosempaque['foto6'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto6'] : $ruta."camara_bn.jpg";


$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(73, 140, 123); // VERDE OSCURO
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO

$pdf->Cell(195,6, utf8_decode($descripcion), 1, 1, 'C', 1);

$pdf->SetFillColor(94, 181, 159); // VERDE


$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(65, 6, 'Photo 1', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 2', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 3', 1, 1, 'C', 1);

$yini = $pdf->GetY();


$pdf->Image($foto1, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto2, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto3, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$yini = $pdf->GetY();
if($yini > 200){
  $pdf->AddPage();

  $pdf->Cell(195, 6, utf8_decode('...defective continuation'), 1, 1, 'C', 1);
}


$pdf->Cell(65, 6, 'Photo 4', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 5', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 6', 1, 1, 'C', 1);

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();

$pdf->Image($foto4, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto5, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto6, 145, $yini, 60, 45, 'JPG');

$pdf->Ln(45);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)
$pdf->Ln(5);

      } // Fin del if en caso de haber defectos
      $qfotosempaque->free();
  } else {

$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(50,50,50);  // GRIS OSCURO

$pdf->Cell(195, 6, 'No photographic evidence to show', 1, 1, 'C', 0);

  }

}
## FIN DE FOTOGRAFÍAS DE DEFECTOS DE EMPAQUE
## ************************************************************






## ----------------------------------------- ##
//       Fotos de defectos visuales          //
## ----------------------------------------- ##
if ($visual == 'S') {
  $prefijo = "VISUAL/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";

  $qfotosempaque = $conexion->query("SELECT A.*,
        B.description

        FROM $db.inspecciones04 A

        LEFT JOIN $db.defectivos B
        ON A.defectivo = B.clave

        WHERE idinsp = $idinsp
        AND idbase = $idbase
        AND A.tipo = 'VISUAL' ")
    or die ("Error al obtener las fotos de los defectos visuales (INSPECCIONES04) <br> " . mysqli_error($conexion) );

$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(60, 107, 128); // MENTA OSCURO
$pdf->SetTextColor(255,255,255);  // BLANCO

$pdf->Cell(195, 6, 'Visual defectives', 1, 1, 'C', 1);

  if ($qfotosempaque->num_rows > 0) {
      while ($ffotosempaque = $qfotosempaque->fetch_assoc()) {
        $descripcion = $ffotosempaque['description'];
        $foto1 = ($ffotosempaque['foto1'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto1'] : $ruta."camara_bn.jpg";
        $foto2 = ($ffotosempaque['foto2'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto2'] : $ruta."camara_bn.jpg";
        $foto3 = ($ffotosempaque['foto3'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto3'] : $ruta."camara_bn.jpg";
        $foto4 = ($ffotosempaque['foto4'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto4'] : $ruta."camara_bn.jpg";
        $foto5 = ($ffotosempaque['foto5'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto5'] : $ruta."camara_bn.jpg";
        $foto6 = ($ffotosempaque['foto6'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto6'] : $ruta."camara_bn.jpg";


$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(73, 140, 123); // VERDE OSCURO
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO

$pdf->Cell(195,6, utf8_decode($descripcion), 1, 1, 'C', 1);

$pdf->SetFillColor(94, 181, 159); // VERDE


$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(65, 6, 'Photo 1', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 2', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 3', 1, 1, 'C', 1);

$yini = $pdf->GetY();


$pdf->Image($foto1, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto2, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto3, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$yini = $pdf->GetY();
if($yini > 200){
  $pdf->AddPage();

  $pdf->Cell(195, 6, utf8_decode('...defective continuation'), 1, 1, 'C', 1);
}


$pdf->Cell(65, 6, 'Photo 4', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 5', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 6', 1, 1, 'C', 1);

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();

$pdf->Image($foto4, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto5, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto6, 145, $yini, 60, 45, 'JPG');

$pdf->Ln(45);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)
$pdf->Ln(5);

      } // Fin del if en caso de haber defectos
      $qfotosempaque->free();
  } else {

$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(50,50,50);  // GRIS OSCURO

$pdf->Cell(195, 6, 'No photographic evidence to show', 1, 1, 'C', 0);

  }

}
## FIN DE FOTOGRAFÍAS VISUAL
## ************************************************************







## ----------------------------------------- ##
//     Fotos de defectos de etiquetado       //
## ----------------------------------------- ##
if ($etiquetado == 'S') {
  $prefijo = "ETIQUETADO/INSP_" . $idinsp . "_IDBASE_" . $idbase . "_";

  $qfotosempaque = $conexion->query("SELECT A.*,
        B.description

        FROM $db.inspecciones04 A

        LEFT JOIN $db.defectivos B
        ON A.defectivo = B.clave

        WHERE idinsp = $idinsp
        AND idbase = $idbase
        AND A.tipo = 'ETIQUETADO' ")
    or die ("Error al obtener las fotos de los defectos de etiquetado (INSPECCIONES04) <br> " . mysqli_error($conexion) );

$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(60, 107, 128); // MENTA OSCURO
$pdf->SetTextColor(255,255,255);  // BLANCO

$pdf->Cell(195, 6, 'Labelling defectives', 1, 1, 'C', 1);

  if ($qfotosempaque->num_rows > 0) {
      while ($ffotosempaque = $qfotosempaque->fetch_assoc()) {
        $descripcion = $ffotosempaque['description'];
        $foto1 = ($ffotosempaque['foto1'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto1'] : $ruta."camara_bn.jpg";
        $foto2 = ($ffotosempaque['foto2'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto2'] : $ruta."camara_bn.jpg";
        $foto3 = ($ffotosempaque['foto3'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto3'] : $ruta."camara_bn.jpg";
        $foto4 = ($ffotosempaque['foto4'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto4'] : $ruta."camara_bn.jpg";
        $foto5 = ($ffotosempaque['foto5'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto5'] : $ruta."camara_bn.jpg";
        $foto6 = ($ffotosempaque['foto6'] != '-') ? $ruta.$prefijo.$ffotosempaque['foto6'] : $ruta."camara_bn.jpg";


$pdf->SetFont('Helvetica','B',8);
$pdf->SetFillColor(73, 140, 123); // VERDE OSCURO
$pdf->SetTextColor(50, 50, 50); // GRIS OBSCURO

$pdf->Cell(195,6, utf8_decode($descripcion), 1, 1, 'C', 1);

$pdf->SetFillColor(94, 181, 159); // VERDE


$yini = $pdf->GetY();
if($yini > 200){ $pdf->AddPage(); }

$pdf->Cell(65, 6, 'Photo 1', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 2', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 3', 1, 1, 'C', 1);

$yini = $pdf->GetY();


$pdf->Image($foto1, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto2, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto3, 145, $yini, 60, 45, 'JPG');


$pdf->Ln(45+5);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)

$yini = $pdf->GetY();
if($yini > 200){
  $pdf->AddPage();

  $pdf->Cell(195, 6, utf8_decode('...defective continuation'), 1, 1, 'C', 1);
}


$pdf->Cell(65, 6, 'Photo 4', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 5', 1, 0, 'C', 1);
$pdf->Cell(65, 6, 'Photo 6', 1, 1, 'C', 1);

//Obtener la posición en Y para poner imágenes
$yini = $pdf->GetY();

$pdf->Image($foto4, 10, $yini, 60, 45, 'JPG');
$pdf->Image($foto5, 78, $yini, 60, 45, 'JPG');
$pdf->Image($foto6, 145, $yini, 60, 45, 'JPG');

$pdf->Ln(45);   // Es el alto de las fotografías +  el título (para que queden justamente debajo de las fotos anteriores)
$pdf->Ln(5);

      } // Fin del if en caso de haber defectos
      $qfotosempaque->free();
  } else {

$pdf->SetFont('Helvetica','B',8);
$pdf->SetTextColor(50,50,50);  // GRIS OSCURO

$pdf->Cell(195, 6, 'No photographic evidence to show', 1, 1, 'C', 0);

  }

}
## FIN DE FOTOGRAFÍAS VISUAL
## ************************************************************

}
## FIN DE FOTOGRAFÍAS DE DEFECTOS
## ************************************************************

$rutainforme = "../../archivos/informes/insp/IDINSP_".$idinsp.".pdf";

$pdf->close();
$pdf->Output($rutainforme, 'F');
$pdf->Output();