<?php

// Aqui agregar toda la logica para autenticacion del login
// del lado del cliente recaptcha: 6LfhmeMcAAAAAO5ip6pHdn4mkCnSeGqwc245meEa
// del lado del servidor recaptcha: 6LfhmeMcAAAAAL_onCs5s6lxVB9-OuKCwqv9LoBD

include_once('conexion.php');

if (isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']) {
  $recaptcha = $_POST['g-recaptcha-response'];
  $secret = "6LfhmeMcAAAAAL_onCs5s6lxVB9-OuKCwqv9LoBD";
  $ip = $_SERVER['REMOTE_ADDR'];
  $var = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha&remoteip=$ip");
  $array = json_decode($var, true);

  if($array['success']){
    session_start();

    if(isset($_SESSION['administrador'])){
      $_SESSION = array();
    }

    $usuario = mysqli_real_escape_string($conexion, $_POST['username']);
    $password = mysqli_real_escape_string($conexion, $_POST['password']);

    // CONSULTA PARA VALIDAR USUARIO Y CONTRASENA
    $query = $conexion->prepare(
          "SELECT A.*,
                  B.clave
          
           FROM $db.usuarios A
           
           JOIN $db.administradores B
           ON A.administrador = B.id
           
           WHERE A.usuario = ?
           AND pwd = ?
           AND disponible = 'SI'" 
    ) or die("Error al obtener el usuario indicado <br>" . mysqli_error($conexion));

    //SE PASAN LOS PARAMETROS DE LA CONSULTA
    $query->bind_param("ss", $usuario, $password);
    $query->execute();

    $resultado = $query->get_result();

    var_dump($resultado);
    
    if($resultado->num_rows == 1){
      $fila = $resultado->fetch_assoc();

      $_SESSION['administrador'] = $fila['nombre'];
      $_SESSION['usuario'] = $fila['usuario'];
      $_SESSION['clave'] = $fila['clave'];
      
      header('Location: ../admin/actuales.php');
    } else {
      
      header('Location: ../index.php?login=false');
      
    }

  } else {
    header('Location: ../index.php?recaptcha=false');
  }
}

else {
  header('Location: ../index.php?recaptcha=false');
}
