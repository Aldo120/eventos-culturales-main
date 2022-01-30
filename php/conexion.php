<?php 

$host = 'localhost';
$user = 'root';
$pw = '';
$db = 'culturales';

$conexion = mysqli_connect($host, $user, $pw, $db);

if(!$conexion){
  echo 'No se pudo conectar con la base de datos ' . PHP_EOL;
  echo 'No. de error: ' . mysqli_connect_erno() . PHP_EOL;
  echo 'El error consiste en que: ' . mysqli_connect_erno() . PHP_EOL;
  die();

} else {
  $conexion->set_charset('utf8mb4');
  date_default_timezone_set('America/Mexico_City');
  setlocale(LC_ALL, 'es_MX');

  $fechahoy = date('Y-m-d');
}
