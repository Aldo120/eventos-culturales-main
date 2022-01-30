<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');
  require('../librerias/fpdf/fpdf.php');

  $id_evento = $_GET['id'];
  
  $qevento = $conexion->query(
    "SELECT *
     FROM $db.eventos
     WHERE id = '$id_evento'"
  ) or die("Error al obtener los datos del evento <br>". mysqli_error($conexion));

  $fevento = $qevento->fetch_assoc();
  $qevento->free();
  $nombre = $fevento['nombre'];
  $fecha = strftime("%d/%m/%Y", strtotime($fevento['fecha']));
  $hora = $fevento['hora'];
  $hora_fin = $fevento['hora_fin'];
  $descripcion_evento = $fevento['descripcion'];
  


  // obtener informacion de los asistentes

  // OBTENER LISTA DE ALUMNOS QUE INGRESARON AL EVENTO
  $qalumnos = $conexion->query(
    "SELECT * FROM $db.asistencias A 

    JOIN $db.asistentes B
    ON A.id_asistente = B.id
    
    WHERE A.id_evento = '$id_evento' AND B.comunidad = 1"
  )  or die("Error al obtener los alumnos que ingresaron al evento <br>" . mysqli_error($conexion));

  
  // OBTENER LISTA DE ACADEMICOS QUE INGRESARON AL EVENTO
  $qacademicos = $conexion->query(
    "SELECT * FROM $db.asistencias A 

    JOIN $db.asistentes B
    ON A.id_asistente = B.id
    
    WHERE A.id_evento = '$id_evento' AND B.comunidad = 2"
  )  or die("Error al obtener los academicos que ingresaron al evento <br>" . mysqli_error($conexion));

  // OBTENER LISTA DE EX ALUMNOS QUE INGRESARON AL EVENTO
  $qexalumnos = $conexion->query(
    "SELECT * FROM $db.asistencias A 

    JOIN $db.asistentes B
    ON A.id_asistente = B.id
    
    WHERE A.id_evento = '$id_evento' AND B.comunidad = 3"
  )  or die("Error al obtener los exalumnos que ingresaron al evento <br>" . mysqli_error($conexion));
  
  // OBTENER LISTA DE TRABAJADORES QUE INGRESARON AL EVENTO
  $qtrabajadores = $conexion->query(
    "SELECT * FROM $db.asistencias A 

    JOIN $db.asistentes B
    ON A.id_asistente = B.id
    
    WHERE A.id_evento = '$id_evento' AND B.comunidad = 4"
  )  or die("Error al obtener los trabajadores que ingresaron al evento <br>" . mysqli_error($conexion));

  // OBTENER LISTA DE PUBLICO EXTERNO QUE INGRESARON AL EVENTO
  $qexternos = $conexion->query(
    "SELECT * FROM $db.asistencias A 

    JOIN $db.asistentes B
    ON A.id_asistente = B.id
    
    WHERE A.id_evento = '$id_evento' AND B.comunidad = 5"
  )  or die("Error al obtener los externos que ingresaron al evento <br>" . mysqli_error($conexion));





  // OBTENER TODOS LOS DATOS DE INDICADORES
  $qindicadores = $conexion->query(
    "SELECT * FROM $db.indicadores 
    
    WHERE id_evento = '$id_evento'"
  )  or die("Error al obtener los datos de indicadores <br>" . mysqli_error($conexion));

  $findicadores = $qindicadores->fetch_assoc();
  $qindicadores->free();

  // VALORES GENERALES
  $hombres_g = $findicadores['hombres_g'];
  $mujeres_g = $findicadores['mujeres_g'];
  $se_g = $findicadores['se_g'];
  $inter_g = $findicadores['inter_g'];

  $total_g = $hombres_g + $mujeres_g + $se_g + $inter_g;

  $hombres_g_porc = 0;
  $mujeres_g_porc = 0;
  $se_g_porc = 0;
  $inter_g_porc = 0;


  if($total_g == 0){
    header("Location: actuales.php?informe=false");
  } 

  else if($total_g > 0){
    $hombres_g_porc = number_format(($hombres_g * 100) / $total_g, 1);
    $mujeres_g_porc = number_format(($mujeres_g * 100) / $total_g, 1);
    $se_g_porc = number_format(($se_g * 100) / $total_g, 1);
    $inter_g_porc = number_format(($inter_g * 100) / $total_g, 1);
  }

 
  // VALORES DE ALUMNOS
  $hombres_al = $findicadores['hombres_al'];
  $mujeres_al = $findicadores['mujeres_al'];

  $total_al = $hombres_al + $mujeres_al;

  $hombres_al_porc = 0;
  $mujeres_al_porc = 0;

  if($total_al > 0){
    $hombres_al_porc = number_format(($hombres_al * 100) / $total_g, 1);
    $mujeres_al_porc = number_format(($mujeres_al * 100) / $total_g, 1);

    $hombres_al_porc_real = number_format(($hombres_al * 100) / $total_al, 1);
    $mujeres_al_porc_real = number_format(($mujeres_al * 100) / $total_al, 1);

    $alumnos_porc = ($hombres_al_porc + $mujeres_al_porc);
  }


  // VALORES DE ACADEMICOS
  $hombres_ac = $findicadores['hombres_ac'];
  $mujeres_ac = $findicadores['mujeres_ac'];
  $se_ac = $findicadores['se_ac'];
  $inter_ac = $findicadores['inter_ac'];

  $total_ac = $hombres_ac + $mujeres_ac + $se_ac + $inter_ac;

  $hombres_ac_porc = 0;
  $mujeres_ac_porc = 0;
  $se_ac_porc = 0;
  $inter_ac_porc = 0;

  if($total_ac > 0){
    $hombres_ac_porc = number_format(($hombres_ac * 100) / $total_g, 1);
    $mujeres_ac_porc = number_format(($mujeres_ac * 100) / $total_g, 1);
    $se_ac_porc = number_format(($se_ac * 100) / $total_g, 1);
    $inter_ac_porc = number_format(($inter_ac * 100) / $total_g, 1);

    $hombres_ac_porc_real = number_format(($hombres_ac * 100) / $total_ac, 1);
    $mujeres_ac_porc_real = number_format(($mujeres_ac * 100) / $total_ac, 1);
    $se_ac_porc_real = number_format(($se_ac * 100) / $total_ac, 1);
    $inter_ac_porc_real = number_format(($inter_ac * 100) / $total_ac, 1);

    $academicos_porc = ($hombres_ac_porc + $mujeres_ac_porc + $inter_ac_porc + $se_ac_porc);
  }



  // VALORES DE EX ALUMNOS
  $hombres_ex = $findicadores['hombres_ex'];
  $mujeres_ex = $findicadores['mujeres_ex'];
  $se_ex = $findicadores['se_ex'];
  $inter_ex = $findicadores['inter_ex'];

  $total_ex = $hombres_ex + $mujeres_ex + $se_ex + $inter_ex;

  $hombres_ex_porc = 0;
  $mujeres_ex_porc = 0;
  $se_ex_porc = 0;
  $inter_ex_ancho = 0;

  if($total_ex > 0){
    $hombres_ex_porc = number_format(($hombres_ex * 100) / $total_g, 1);
    $mujeres_ex_porc = number_format(($mujeres_ex * 100) / $total_g, 1);
    $se_ex_porc = number_format(($se_ex * 100) / $total_g, 1);
    $inter_ex_porc = number_format(($inter_ex * 100) / $total_g, 1);

    $hombres_ex_porc_real = number_format(($hombres_ex * 100) / $total_ex, 1);
    $mujeres_ex_porc_real = number_format(($mujeres_ex * 100) / $total_ex, 1);
    $se_ex_porc_real = number_format(($se_ex * 100) / $total_ex, 1);
    $inter_ex_porc_real = number_format(($inter_ex * 100) / $total_ex, 1);
    
    $ex_porc = ($hombres_ex_porc + $mujeres_ex_porc + $inter_ex_porc + $se_ex_porc);
  }



  // VALORES DE TRABAJADORES
  $hombres_t = $findicadores['hombres_t'];
  $mujeres_t = $findicadores['mujeres_t'];
  $se_t = $findicadores['se_t'];
  $inter_t = $findicadores['inter_t'];

  $total_t = $hombres_t + $mujeres_t + $se_t + $inter_t;

  $hombres_t_porc = 0;
  $mujeres_t_porc = 0;
  $se_t_porc = 0;
  $inter_t_porc = 0;

  if($total_t > 0){
    $hombres_t_porc = number_format(($hombres_t * 100) / $total_g, 1);
    $mujeres_t_porc = number_format(($mujeres_t * 100) / $total_g, 1);
    $se_t_porc = number_format(($se_t * 100) / $total_g, 1);
    $inter_t_porc = number_format(($inter_t * 100) / $total_g, 1);

    $hombres_t_porc_real = number_format(($hombres_t * 100) / $total_t, 1);
    $mujeres_t_porc_real = number_format(($mujeres_t * 100) / $total_t, 1);
    $se_t_porc_real = number_format(($se_t * 100) / $total_t, 1);
    $inter_t_porc_real = number_format(($inter_t * 100) / $total_t, 1);

    $trabajadores_porc = ($hombres_t_porc + $mujeres_t_porc + $inter_t_porc + $se_t_porc);
  }



  // VALORES DE PUBLICO EN GENERAL
  $hombres_p = $findicadores['hombres_p'];
  $mujeres_p = $findicadores['mujeres_p'];
  $se_p = $findicadores['se_p'];
  $inter_p = $findicadores['inter_p'];


  $total_p = $hombres_p + $mujeres_p + $se_p + $inter_p;

  $hombres_p_porc = 0;
  $mujeres_p_porc = 0;
  $se_p_porc = 0;
  $inter_p_porc = 0;

  if($total_p > 0){
    $hombres_p_porc = number_format(($hombres_p * 100) / $total_g, 1);
    $mujeres_p_porc = number_format(($mujeres_p * 100) / $total_g, 1);
    $se_p_porc = number_format(($se_p * 100) / $total_g, 1);
    $inter_p_porc = number_format(($inter_p * 100) / $total_g, 1);

    $hombres_p_porc_real = number_format(($hombres_p * 100) / $total_p, 1);
    $mujeres_p_porc_real = number_format(($mujeres_p * 100) / $total_p, 1);
    $se_p_porc_real = number_format(($se_p * 100) / $total_p, 1);
    $inter_p_porc_real = number_format(($inter_p * 100) / $total_p, 1);

    $publico_porc = ($hombres_p_porc + $mujeres_p_porc + $inter_p_porc + $se_p_porc);
  } 


class PDF extends FPDF
{
  // Cabecera de pagina
  function Header()
  {
    // Logo
    // $this->Image(DIRECCION DE LA IAGEN, POSICION DESDE LA IZQUERDA, POSICION DESDE ARRIBA, ANCHO DE LA IMAGEN);
    $this->Image('../img/logo-unam.png', 20, 8, 17);
    $this->Image('../img/logo.png', 170, 8, 20);
    // Arial bold 15
    $this->SetFont('Arial','', 10);
    $this->setTextColor(2, 38, 82);

    // titulo
    $this->Cell(20);
    $this->Cell(140, 5, utf8_decode('Universidad Nacional Autónoma de México'), 0, 1, 'C');
    $this->Cell(20);
    $this->Cell(140, 5, utf8_decode('Facultad de Estudios Superiores "Aragón"'), 0, 1, 'C');
    $this->Cell(20);
    $this->Cell(140, 5, utf8_decode('Extensión Universitaria FES "Aragón"'), 0, 1, 'C');
    
    $this->Ln(10);
  }

  // Pie de pagina
  function Footer()
  {
    // posicion: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // numero de pagina
    $this->Cell(0, 10, utf8_decode('Página '.$this->PageNo().'/{nb}'), 0, 0, 'C');
  }
}

// creacion del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// BODY DE LA PAGINA
$pdf->SetFont('Times','B',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(190, 7, utf8_decode("Reporte de Asistencia al Evento: $nombre"), 0, 1, 'C', 0);

$pdf->SetFont('Arial','', 7);
$pdf->SetTextColor(115,115,115);
$pdf->Cell(190, 5, utf8_decode("Información del Evento. Fecha: $fecha, Hora: $hora a $hora_fin"), 0, 1, 'L');
$pdf->Cell(190, 5, utf8_decode("Descipción: $descripcion_evento"), 0, 1, 'L');
$pdf->Ln(4);

// $pdf->SetFont('Arial','B',8);
// $pdf->setTextColor(2, 38, 82);
// $pdf->Cell(190, 6, utf8_decode("Resumen de Asistencias. Total: $total_g Asistentes"), 0, 1, 'L', 0);
// $pdf->Ln(2);

// ENCABEZADO
$pdf->SetFillColor(2, 38, 82); 
$pdf->SetDrawColor(192,204,216);  // COLOR AZUL PAGINA
$pdf->SetFont('Arial','B', 8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(190, 6, utf8_decode("Resumen de Asistencias. Total: $total_g personas"), 0, 1, 'C', 1);

$pdf->Cell(31, 6, utf8_decode("Alumnos(as)"), 0, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("Académicos(as) "), 0, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("Ex alumnos(as)"), 0, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("Trabajadores(as)"), 0, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("Público Externo"), 0, 0, 'C', 1);
$pdf->Cell(35, 6, utf8_decode("Total por Sexo"), 0, 1, 'C', 1);

// SEXO FEMENINO 
$pdf->SetTextColor(49,49,49);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->Cell(155, 6, utf8_decode("Femenino: $mujeres_g personas"), 1, 0, 'C', 1);

$pdf->SetFillColor(2, 38, 82); 
$pdf->SetTextColor(255,255,255);
$pdf->Cell(35, 6, utf8_decode("Femenino"), 1, 1, 'C', 1);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->SetTextColor(49,49,49);

$pdf->Cell(31, 6, utf8_decode("$mujeres_al"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$mujeres_ac"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$mujeres_ex"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$mujeres_t"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$mujeres_p"), 1, 0, 'C', 0);

$pdf->SetFillColor(154, 249, 160); 
$pdf->Cell(35, 6, utf8_decode("$mujeres_g"), 1, 1, 'C', 1);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 

// $pdf->Ln(2);

// SEXO MASCULINO 
$pdf->SetTextColor(49,49,49);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->Cell(155, 6, utf8_decode("Masculino: $hombres_g personas"), 1, 0, 'C', 1);

$pdf->SetFillColor(2, 38, 82); 
$pdf->SetTextColor(255,255,255);
$pdf->Cell(35, 6, utf8_decode("Masculino"), 1, 1, 'C', 1);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->SetTextColor(49,49,49);

$pdf->Cell(31, 6, utf8_decode("$hombres_al"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$hombres_ac"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$hombres_ex"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$hombres_t"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$hombres_p"), 1, 0, 'C', 0);
$pdf->SetFillColor(154, 249, 160); 
$pdf->Cell(35, 6, utf8_decode("$hombres_g"), 1, 1, 'C', 1);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 

// $pdf->Ln(2);

// SEXO INTERSEX
$pdf->SetTextColor(49,49,49);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->Cell(155, 6, utf8_decode("Intersex: $inter_g personas"), 1, 0, 'C', 1);

$pdf->SetFillColor(2, 38, 82); 
$pdf->SetTextColor(255,255,255);
$pdf->Cell(35, 6, utf8_decode("Intersex"), 1, 1, 'C', 1);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->SetTextColor(49,49,49);

$pdf->Cell(31, 6, utf8_decode(" 0 "), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$inter_ac"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$inter_ex"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$inter_t"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$inter_p"), 1, 0, 'C', 0);  
$pdf->SetFillColor(154, 249, 160); 
$pdf->Cell(35, 6, utf8_decode("$inter_g"), 1, 1, 'C', 1);  
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 

// $pdf->Ln(2);

// SEXO SIN ESPECIFICAR
$pdf->SetTextColor(49,49,49);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->Cell(155, 6, utf8_decode("Prefiero no Decirlo: $se_g personas"), 1, 0, 'C', 1);

$pdf->SetFillColor(2, 38, 82); 
$pdf->SetTextColor(255,255,255);
$pdf->Cell(35, 6, utf8_decode("Prefiero no Decirlo"), 1, 1, 'C', 1);
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 
$pdf->SetTextColor(49,49,49);


$pdf->Cell(31, 6, utf8_decode(" 0 "), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$se_ac"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$se_ex"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$se_t"), 1, 0, 'C', 0);
$pdf->Cell(31, 6, utf8_decode("$se_p"), 1, 0, 'C', 0);  
$pdf->SetFillColor(154, 249, 160); 
$pdf->Cell(35, 6, utf8_decode("$se_g"), 1, 1, 'C', 1);  
$pdf->SetFillColor(217, 255, 242); // COLOR MENTA 

// $pdf->Ln(2);

$pdf->SetFillColor(2, 38, 82); 
$pdf->SetDrawColor(192,204,216);  // COLOR AZUL PAGINA
$pdf->SetFont('Arial','B', 8);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(155, 6, utf8_decode("Total por Comunidad"), 1, 0, 'C', 1);
$pdf->Cell(35, 6, utf8_decode("Total General"), 1, 1, 'C', 1);


$pdf->SetTextColor(49,49,49);
$pdf->SetFillColor(154, 249, 160); 
$pdf->Cell(31, 6, utf8_decode("$total_al"), 1, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("$total_ac"), 1, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("$total_ex"), 1, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("$total_t"), 1, 0, 'C', 1);
$pdf->Cell(31, 6, utf8_decode("$total_p"), 1, 0, 'C', 1); 
$pdf->Cell(35, 6, utf8_decode("$total_g"), 1, 1, 'C', 1); 


$pdf->Ln(10);

// HEADER PAGINAS
$pdf->SetFont('Times','B',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(190, 7, utf8_decode("Representación Visual de Asistencia al Evento: "), 0, 1, 'C', 0);

$pdf->SetFont('Arial','B',8);
$pdf->setTextColor(2, 38, 82);
$pdf->Cell(190, 6, utf8_decode("Resumen de Asistencias. Total: $total_g Asistentes"), 0, 1, 'L', 0);
$pdf->Ln(2);

// $pdf->SetFont('Times','B',10);
// $pdf->SetTextColor(0,0,0);
// $pdf->Cell(190, 7, utf8_decode("Representación Visual  en Asistentes."), 0, 1, 'C', 0);


// INDICE DE COLORES ASISTENTES
$pdf->Cell(30);

$pdf->SetFont('Arial','',8);

$pdf->SetFillColor(214, 163, 250); // rosa
$pdf->Cell(20, 5, utf8_decode("Femenino:"), 0, 0, 'R', 0);
$pdf->Cell(5, 5, "", 1, 0, 'R', 1);

$pdf->SetFillColor(108, 156, 234); // azul
$pdf->Cell(20, 5, utf8_decode("Masculino:"), 0, 0, 'R', 0);
$pdf->Cell(5, 5, "", 1, 0, 'R', 1);

$pdf->SetFillColor(241, 246, 96); // amarillo
$pdf->Cell(20, 5, utf8_decode("Intersex:"), 0, 0, 'R', 0);
$pdf->Cell(5, 5, "", 1, 0, 'R', 1);

$pdf->SetFillColor(207, 207, 207); // gris
$pdf->Cell(30, 5, utf8_decode("Prefiero no decirlo:"), 0, 0, 'R', 0);
$pdf->Cell(5, 5, "", 1, 1, 'R', 1);


// OBTENER PORCENTAJES
if($total_g > 0){
  
  // OBTENER ANCHO DE CADA COLUMNA
  $hombres_g_ancho = intval(($hombres_g_porc * 190) / 100);
  $mujeres_g_ancho = intval(($mujeres_g_porc * 190) / 100);
  $se_g_ancho = intval(($se_g_porc * 190) / 100);
  $inter_g_ancho = intval(($inter_g_porc * 190) / 100);

  $pdf->SetFont('Arial','B',8);
  $pdf->setTextColor(0, 0, 0);
  $pdf->Cell(190, 6, utf8_decode("Indicador General."), 0, 1, 'L', 0);
  $pdf->Ln(2);
  
  if($mujeres_g != 0){
    $pdf->SetFillColor(214, 163, 250); // rosa
    $pdf->Cell($mujeres_g_ancho, 6, "$mujeres_g_porc %", 0, 0, 'C', 1);    
  }

  if($hombres_g != 0){
    $pdf->SetFillColor(108, 156, 234); // azul
    $pdf->Cell($hombres_g_ancho, 6, "$hombres_g_porc %", 0, 0, 'C', 1);
  }
  
  if($inter_g != 0){
    $pdf->SetFillColor(241, 246, 96); // amarillo
    $pdf->Cell($inter_g_ancho, 6, "$inter_g_porc %", 0, 0, 'C', 1);
  }
  
  if($se_g != 0){
    $pdf->SetFillColor(207, 207, 207); // gris
    $pdf->Cell($se_g_ancho, 6, "$se_g_porc %", 0, 0, 'C', 1);
  }

  $pdf->Ln(16);
}



// OBTENER PORCENTAJES ALUMNOS
if($total_al > 0){

  // OBTENER ANCHO DE CADA COLUMNA
  $hombres_al_ancho = intval(($hombres_al_porc_real * 190) / 100);
  $mujeres_al_ancho = intval(($mujeres_al_porc_real * 190) / 100);

  $pdf->SetFont('Arial','B',8);
  $pdf->setTextColor(2, 38, 82);
  $pdf->Cell(190, 6, utf8_decode("Indicador Alumnos(as): $alumnos_porc %"), 0, 1, 'L', 0);
  $pdf->Ln(2);

  if($mujeres_al != 0){
    $pdf->SetFillColor(214, 163, 250); // rosa
    $pdf->Cell($mujeres_al_ancho, 6, "$mujeres_al_porc %", 0, 0, 'C', 1);    
  }
  
  if($hombres_al != 0){
    $pdf->SetFillColor(108, 156, 234); // azul
    $pdf->Cell($hombres_al_ancho, 6, "$hombres_al_porc %", 0, 0, 'C', 1);
  }
    
  $pdf->Ln(8);
}




// OBTENER PORCENTAJES ACADEMICOS
if($total_ac > 0){

  
  // OBTENER ANCHO DE CADA COLUMNA
  $hombres_ac_ancho = intval(($hombres_ac_porc_real * 190) / 100);
  $mujeres_ac_ancho = intval(($mujeres_ac_porc_real * 190) / 100);
  $se_ac_ancho = intval(($se_ac_porc_real * 190) / 100);
  $inter_ac_ancho = intval(($inter_ac_porc_real * 190) / 100);
  
  $pdf->SetFont('Arial','B',8);
  $pdf->setTextColor(2, 38, 82);
  // $pdf->Cell($hombres_al_ancho + $mujeres_al_ancho);
  $pdf->Cell(190, 6, utf8_decode("Indicador Académicos(as) : $academicos_porc %"), 0, 1, 'L', 0);
  $pdf->Ln(2);
  
  // $pdf->Cell($hombres_al_ancho + $mujeres_al_ancho);
  if($mujeres_ac != 0){
    $pdf->SetFillColor(214, 163, 250); // rosa
    $pdf->Cell($mujeres_ac_ancho, 6, "$mujeres_ac_porc %", 0, 0, 'C', 1);    
  }
  
  if($hombres_ac != 0){
    $pdf->SetFillColor(108, 156, 234); // azul
    $pdf->Cell($hombres_ac_ancho, 6, "$hombres_ac_porc %", 0, 0, 'C', 1);
  }
    
  if($inter_ac != 0){
    $pdf->SetFillColor(241, 246, 96); // amarillo
    $pdf->Cell($inter_ac_ancho, 6, "$inter_ac_porc %", 0, 0, 'C', 1);
  }

  if($se_ac != 0){
    $pdf->SetFillColor(207, 207, 207); // gris
    $pdf->Cell($se_ac_ancho, 6, "$se_ac_porc %", 0, 0, 'C', 1);
  }
  $pdf->Ln(8);
}







// OBTENER PORCENTAJES
if($total_ex > 0){
  
  // OBTENER ANCHO DE CADA COLUMNA
  $hombres_ex_ancho = intval(($hombres_ex_porc_real * 190) / 100);
  $mujeres_ex_ancho = intval(($mujeres_ex_porc_real * 190) / 100);
  $se_ex_ancho = intval(($se_ex_porc_real * 190) / 100);
  $inter_ex_ancho = intval(($inter_ex_porc_real * 190) / 100);
  
  $pdf->SetFont('Arial','B',8);
  $pdf->setTextColor(2, 38, 82);
  // $pdf->Cell($hombres_al_ancho + $mujeres_al_ancho + $hombres_ac_ancho + $mujeres_ac_ancho + $inter_ac_ancho + $se_ac_ancho);
  $pdf->Cell(190, 6, utf8_decode("Indicador Ex-Alumnos(as)  : $ex_porc %"), 0, 1, 'L', 0);
  $pdf->Ln(2);

  // $pdf->Cell($hombres_al_ancho + $mujeres_al_ancho + $hombres_ac_ancho + $mujeres_ac_ancho + $inter_ac_ancho + $se_ac_ancho);
  if($mujeres_ex != 0){
    $pdf->SetFillColor(214, 163, 250); // rosa
    $pdf->Cell($mujeres_ex_ancho, 6, "$mujeres_ex_porc %", 0, 0, 'C', 1);    
  }
  
  if($hombres_ex != 0){
    $pdf->SetFillColor(108, 156, 234); // azul
    $pdf->Cell($hombres_ex_ancho, 6, "$hombres_ex_porc %", 0, 0, 'C', 1);
  }  

  if($inter_ex != 0){
    $pdf->SetFillColor(241, 246, 96); // amarillo
    $pdf->Cell($inter_ex_ancho, 6, "$inter_ex_porc %", 0, 0, 'C', 1);
  }

  if($se_ex != 0){
    $pdf->SetFillColor(207, 207, 207); // gris
    $pdf->Cell($se_ex_ancho, 6, "$se_ex_porc %", 0, 0, 'C', 1);
  }

  $pdf->Ln(8);
}





// OBTENER PORCENTAJES
if($total_t > 0){
  
  $hombres_t_ancho = intval(($hombres_t_porc_real * 190) / 100);
  $mujeres_t_ancho = intval(($mujeres_t_porc_real * 190) / 100);
  $se_t_ancho = intval(($se_t_porc_real * 190) / 100);
  $inter_t_ancho = intval(($inter_t_porc_real * 190) / 100);

  $pdf->SetFont('Arial','B',8);
  $pdf->setTextColor(2, 38, 82);
  // $pdf->Cell($hombres_al_ancho + $mujeres_al_ancho + $hombres_ac_ancho + $mujeres_ac_ancho + $inter_ac_ancho + $se_ac_ancho + $hombres_ex_ancho + $mujeres_ex_ancho + $inter_ex_ancho + $se_ex_ancho);
  $pdf->Cell(190, 6, utf8_decode("Indicador Trabajadores(as) : $trabajadores_porc %"), 0, 1, 'L', 0);
  $pdf->Ln(2);
  
  // $pdf->Cell($hombres_al_ancho + $mujeres_al_ancho + $hombres_ac_ancho + $mujeres_ac_ancho + $inter_ac_ancho + $se_ac_ancho + $hombres_ex_ancho + $mujeres_ex_ancho + $inter_ex_ancho + $se_ex_ancho);
  if($mujeres_t != 0){
    $pdf->SetFillColor(214, 163, 250); // rosa
    $pdf->Cell($mujeres_t_ancho, 6, "$mujeres_t_porc %", 0, 0, 'C', 1);    
  }

  if($hombres_t != 0){
    $pdf->SetFillColor(108, 156, 234); // azul
    $pdf->Cell($hombres_t_ancho, 6, "$hombres_t_porc %", 0, 0, 'C', 1);
  }
  
  if($inter_t != 0){
    $pdf->SetFillColor(241, 246, 96); // amarillo
    $pdf->Cell($inter_t_ancho, 6, "$inter_t_porc %", 0, 0, 'C', 1);
  }
  
  if($se_t != 0){
    $pdf->SetFillColor(207, 207, 207); // gris
    $pdf->Cell($se_t_ancho, 6, "$se_t_porc %", 0, 0, 'C', 1);
  }

  $pdf->Ln(8);
}






// OBTENER PORCENTAJES
if($total_p > 0){
  
  // OBTENER ANCHO DE CADA COLUMNA
  $hombres_p_ancho = intval(($hombres_p_porc_real * 190) / 100);
  $mujeres_p_ancho = intval(($mujeres_p_porc_real * 190) / 100);
  $se_p_ancho = intval(($se_p_porc_real * 190) / 100);
  $inter_p_ancho = intval(($inter_p_porc_real * 190) / 100);
  
  $pdf->SetFont('Arial','B',8);
  $pdf->setTextColor(2, 38, 82);
  // $pdf->Cell( $hombres_al_ancho + $mujeres_al_ancho + $hombres_ac_ancho + $mujeres_ac_ancho + $inter_ac_ancho + $se_ac_ancho + $hombres_ex_ancho + $mujeres_ex_ancho + $inter_ex_ancho + $se_ex_ancho + $hombres_t_ancho + $mujeres_t_ancho + $inter_t_ancho + $se_t_ancho);
  $pdf->Cell(190, 6, utf8_decode("Indicador Público Externo : $publico_porc %"), 0, 1, 'L', 0);
  $pdf->Ln(2);

  // $pdf->Cell( $hombres_al_ancho + $mujeres_al_ancho + $hombres_ac_ancho + $mujeres_ac_ancho + $inter_ac_ancho + $se_ac_ancho + $hombres_ex_ancho + $mujeres_ex_ancho + $inter_ex_ancho + $se_ex_ancho + $hombres_t_ancho + $mujeres_t_ancho + $inter_t_ancho + $se_t_ancho);
  if($mujeres_p != 0){
    $pdf->SetFillColor(214, 163, 250); // rosa
    $pdf->Cell($mujeres_p_ancho, 6, "$mujeres_p_porc %", 0, 0, 'C', 1);    
  }
  
  if($hombres_p != 0){
    $pdf->SetFillColor(108, 156, 234); // azul
    $pdf->Cell($hombres_p_ancho, 6, "$hombres_p_porc %", 0, 0, 'C', 1);
  }
  
  if($inter_p != 0){
      $pdf->SetFillColor(241, 246, 96); // amarillo
      $pdf->Cell($inter_p_ancho, 6, "$inter_p_porc %", 0, 0, 'C', 1);
    }

  if($se_p != 0){
    $pdf->SetFillColor(207, 207, 207); // gris
    $pdf->Cell($se_p_ancho, 6, "$se_p_porc %", 0, 0, 'C', 1);
  }
  $pdf->Ln(8);
}


// HOJA DE DATOS
$pdf->AddPage();

// TITULO DESGLOCE MÁS ESPECIFICO
$pdf->SetFont('Times','B',10);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(190, 7, utf8_decode("Información por Persona y Comunidad de los Asistentes"), 0, 1, 'C', 0);

$pdf->SetFont('Arial','', 7);
$pdf->SetTextColor(115,115,115);
$pdf->Cell(190, 5, utf8_decode("Información del Evento. Fecha: $fecha, Hora: $hora a $hora_fin"), 0, 1, 'L');
$pdf->Cell(190, 5, utf8_decode("Descipción: $descripcion_evento"), 0, 1, 'L');
$pdf->Ln(8);

// ALUMNOS 
// VALIDAR QUE HAYA ALUMNOS QUE INGRESARON AL EVENTO
if($qalumnos->num_rows > 0){
  // ENCABEZADO
  $pdf->SetFillColor(2, 38, 82); 
  $pdf->SetDrawColor(192,204,216);  // COLOR GRIS
  $pdf->SetFont('Arial','B', 8);
  $pdf->SetTextColor(255,255,255);
  $pdf->Cell(190, 6, utf8_decode("Alumnos(as)"), 0, 1, 'C', 1);
  
  // LISTA DE ALUMNOS
  // COLOR DE LETRA GRIS OSCURO
  $pdf->SetTextColor(49,49,49);
  $pdf->SetFillColor(217, 255, 242); 
  $pdf->Cell(55, 6, utf8_decode("Nombre"), 1, 0, 'C', 1);
  $pdf->Cell(20, 6, utf8_decode("# Cuenta"), 1, 0, 'C', 1);
  $pdf->Cell(40, 6, utf8_decode("Carrera " ), 1, 0, 'C', 1);
  $pdf->Cell(55, 6, utf8_decode("Correo Electrónico "), 1, 0, 'C', 1);
  $pdf->Cell(20, 6, utf8_decode("Sexo"), 1, 1, 'C', 1);

  $pdf->SetFont('Arial','', 7);
  while($falumno = $qalumnos->fetch_assoc()){
    $nombre_completo = $falumno['nombre'] . ' ' . $falumno['ap_paterno'] . ' ' . $falumno['ap_materno'];
    $cuenta = $falumno['cuenta'];
    $carrera = $falumno['carrera_ac'];
    $email = $falumno['email'];
    $sexo = $falumno['sexo'] == 1 ? 'Maculino' : 'Femenino';

    $pdf->Cell(55, 6, utf8_decode($nombre_completo), 1, 0, 'C', 0);
    $pdf->Cell(20, 6, utf8_decode($cuenta), 1, 0, 'C', 0);
    $pdf->Cell(40, 6, utf8_decode($carrera), 1, 0, 'C', 0);
    $pdf->Cell(55, 6, utf8_decode($email), 1, 0, 'C', 0);
    $pdf->Cell(20, 6, utf8_decode($sexo), 1, 1, 'C', 0);
  }
  $pdf->Ln(5);
}


// ACADEMICOS
// VALIDAR QUE HAYA ACADEMICOS QUE INGRESARON AL EVENTO
if($qacademicos->num_rows > 0){
  // ENCABEZADO
  $pdf->SetFillColor(2, 38, 82); 
  $pdf->SetDrawColor(192,204,216);  // COLOR GRIS
  $pdf->SetFont('Arial','B', 8);
  $pdf->SetTextColor(255,255,255);
  $pdf->Cell(190, 6, utf8_decode("Académicos(as)"), 0, 1, 'C', 1);
  
  // LISTA DE ALUMNOS
  // COLOR DE LETRA GRIS OSCURO
  $pdf->SetTextColor(49,49,49);
  $pdf->SetFillColor(217, 255, 242); 
  $pdf->Cell(55, 6, utf8_decode("Nombre"), 1, 0, 'C', 1);
  $pdf->Cell(55, 6, utf8_decode("Carrera que Imparte" ), 1, 0, 'C', 1);
  $pdf->Cell(55, 6, utf8_decode("Correo Electrónico "), 1, 0, 'C', 1);
  $pdf->Cell(25, 6, utf8_decode("Sexo"), 1, 1, 'C', 1);

  $pdf->SetFont('Arial','', 7);
  while($facademico = $qacademicos->fetch_assoc()){
    $nombre_completo = $facademico['nombre'] . ' ' . $facademico['ap_paterno'] . ' ' . $facademico['ap_materno'];
    $carrera = $facademico['carrera_ac'];
    $email = $facademico['email'];

    $sexos = [
      1 => 'Masculino',
      2 => 'Femenino',
      3 => 'Prefiero no decirlo',
      4 => 'Intersex'
    ];
    $sexo = $sexos[$facademico['sexo']];

    $pdf->Cell(55, 6, utf8_decode($nombre_completo), 1, 0, 'C', 0);
    $pdf->Cell(55, 6, utf8_decode($carrera), 1, 0, 'C', 0);
    $pdf->Cell(55, 6, utf8_decode($email), 1, 0, 'C', 0);
    $pdf->Cell(25, 6, utf8_decode($sexo), 1, 1, 'C', 0);
  }
  $pdf->Ln(5);
}



// EX-ALUMNOS
// VALIDAR QUE HAYA EX.ALUMNOS QUE INGRESARON AL EVENTO
if($qexalumnos->num_rows > 0){
  // ENCABEZADO
  $pdf->SetFillColor(2, 38, 82); 
  $pdf->SetDrawColor(192,204,216);  // COLOR GRIS
  $pdf->SetFont('Arial','B', 8);
  $pdf->SetTextColor(255,255,255);
  $pdf->Cell(190, 6, utf8_decode("Ex-alumnos(as)"), 0, 1, 'C', 1);
  
  // LISTA DE EX-ALUMNOS
  // COLOR DE LETRA GRIS OSCURO
  $pdf->SetTextColor(49,49,49);
  $pdf->SetFillColor(217, 255, 242); 
  $pdf->Cell(55, 6, utf8_decode("Nombre"), 1, 0, 'C', 1);
  $pdf->Cell(55, 6, utf8_decode("# Cuenta" ), 1, 0, 'C', 1);
  $pdf->Cell(55, 6, utf8_decode("Correo Electrónico "), 1, 0, 'C', 1);
  $pdf->Cell(25, 6, utf8_decode("Sexo"), 1, 1, 'C', 1);

  $pdf->SetFont('Arial','', 7);
  while($fexalumno = $qexalumnos->fetch_assoc()){
    $nombre_completo = $fexalumno['nombre'] . ' ' . $fexalumno['ap_paterno'] . ' ' . $fexalumno['ap_materno'];
    $cuenta = $fexalumno['cuenta'];
    $email = $fexalumno['email'];

    $sexos = [
      1 => 'Masculino',
      2 => 'Femenino',
      3 => 'Prefiero no decirlo',
      4 => 'Intersex'
    ];
    $sexo = $sexos[$fexalumno['sexo']];

    $pdf->Cell(55, 6, utf8_decode($nombre_completo), 1, 0, 'C', 0);
    $pdf->Cell(55, 6, utf8_decode($cuenta), 1, 0, 'C', 0);
    $pdf->Cell(55, 6, utf8_decode($email), 1, 0, 'C', 0);
    $pdf->Cell(25, 6, utf8_decode($sexo), 1, 1, 'C', 0);
  }
  $pdf->Ln(5);
}



// TRABAJADORES
// VALIDAR QUE HAYA TRABAJADORES QUE INGRESARON AL EVENTO
if($qtrabajadores->num_rows > 0){
  // ENCABEZADO
  $pdf->SetFillColor(2, 38, 82); 
  $pdf->SetDrawColor(192,204,216);  // COLOR GRIS
  $pdf->SetFont('Arial','B', 8);
  $pdf->SetTextColor(255,255,255);
  $pdf->Cell(190, 6, utf8_decode("Trabajadores(as)"), 0, 1, 'C', 1);
  
  // LISTA DE ALUMNOS
  // COLOR DE LETRA GRIS OSCURO
  $pdf->SetTextColor(49,49,49);
  $pdf->SetFillColor(217, 255, 242); 
  $pdf->Cell(75, 6, utf8_decode("Nombre"), 1, 0, 'C', 1);
  $pdf->Cell(75, 6, utf8_decode("Correo Electrónico "), 1, 0, 'C', 1);
  $pdf->Cell(40, 6, utf8_decode("Sexo"), 1, 1, 'C', 1);

  $pdf->SetFont('Arial','', 7);
  while($ftrabajador = $qtrabajadores->fetch_assoc()){
    $nombre_completo = $ftrabajador['nombre'] . ' ' . $ftrabajador['ap_paterno'] . ' ' . $ftrabajador['ap_materno'];
    $cuenta = $ftrabajador['cuenta'];
    $email = $ftrabajador['email'];

    $sexos = [
      1 => 'Masculino',
      2 => 'Femenino',
      3 => 'Prefiero no decirlo',
      4 => 'Intersex'
    ];
    $sexo = $sexos[$ftrabajador['sexo']];

    $pdf->Cell(75, 6, utf8_decode($nombre_completo), 1, 0, 'C', 0);
    $pdf->Cell(75, 6, utf8_decode($email), 1, 0, 'C', 0);
    $pdf->Cell(40, 6, utf8_decode($sexo), 1, 1, 'C', 0);
  }
  $pdf->Ln(5);
}




// PÚBLICO EXTERNO
// VALIDAR QUE HAYA EX.ALUMNOS QUE INGRESARON AL EVENTO
if($qexternos->num_rows > 0){
  // ENCABEZADO
  $pdf->SetFillColor(2, 38, 82); 
  $pdf->SetDrawColor(192,204,216);  // COLOR GRIS
  $pdf->SetFont('Arial','B', 8);
  $pdf->SetTextColor(255,255,255);
  $pdf->Cell(190, 6, utf8_decode("Público Externo"), 0, 1, 'C', 1);
  
  // LISTA DE ALUMNOS
  // COLOR DE LETRA GRIS OSCURO
  $pdf->SetTextColor(49,49,49);
  $pdf->SetFillColor(217, 255, 242); 
  $pdf->Cell(55, 6, utf8_decode("Nombre"), 1, 0, 'C', 1);
  $pdf->Cell(55, 6, utf8_decode("Descripción" ), 1, 0, 'C', 1);
  $pdf->Cell(60, 6, utf8_decode("Correo Electrónico "), 1, 0, 'C', 1);
  $pdf->Cell(20, 6, utf8_decode("Sexo"), 1, 1, 'C', 1);

  $pdf->SetFont('Arial','', 7);
  while($fexterno = $qexternos->fetch_assoc()){
    $nombre_completo = $fexterno['nombre'] . ' ' . $fexterno['ap_paterno'] . ' ' . $fexterno['ap_materno'];
    $descripcion = $fexterno['descripcion'];
    $email = $fexterno['email'];

    $sexos = [
      1 => 'Masculino',
      2 => 'Femenino',
      3 => 'Prefiero no decirlo',
      4 => 'Intersex'
    ];
    $sexo = $sexos[$fexterno['sexo']];

    $pdf->Cell(55, 6, utf8_decode($nombre_completo), 1, 0, 'C', 0);
    $pdf->Cell(55, 6, utf8_decode($descripcion), 1, 0, 'C', 0);
    $pdf->Cell(60, 6, utf8_decode($email), 1, 0, 'C', 0);
    $pdf->Cell(20, 6, utf8_decode($sexo), 1, 1, 'C', 0);
  }
  $pdf->Ln(5);
}




$titulo = $nombre . '_' . $fecha . '.pdf';

$pdf->Output($titulo, 'I');