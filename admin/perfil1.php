<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  $usuario = $_SESSION['usuario'];
  // var_dump($_POST);

  $nombre = mb_strtoupper(trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING)));
  $ap_paterno = mb_strtoupper(trim(filter_var($_POST['ap_paterno'], FILTER_SANITIZE_STRING)));
  $ap_materno = mb_strtoupper(trim(filter_var($_POST['ap_materno'], FILTER_SANITIZE_STRING)));
  $pwd2 = $_POST['pwd2'];

  $nombre_completo = "$nombre $ap_paterno $ap_materno";
  // NO HAY CONTRASEÑA NUEVA
  if($pwd2 == ''){
    $actualiza1 = "UPDATE $db.administradores 
                   SET nombre = '$nombre', ap_paterno = '$ap_paterno', ap_materno = '$ap_materno'
                   WHERE usuario = ?";

  $actualiza2 = "UPDATE $db.usuarios 
                 SET nombre = '$nombre_completo'
                 WHERE usuario = ?";

  $location = "perfil.php?edit=true";
  }

  // HAY CONTRASEÑA NUEVA
  else {
    $actualiza1 = "UPDATE $db.administradores 
                   SET nombre = '$nombre', ap_paterno = '$ap_paterno', ap_materno = '$ap_materno'
                   WHERE usuario = ?";

    $actualiza2 = "UPDATE $db.usuarios 
                   SET nombre = '$nombre_completo', pwd = '$pwd2' 
                   WHERE usuario = ?";

    $location = "../php/logout.php";
  }

  try {
    $conexion->begin_transaction();

    // ACTUALIZAMOS ADMINISTRADORES 
    $statement1 = $conexion->prepare($actualiza1);
    $statement1->bind_param("s", $usuario);
    $statement1->execute();

    // ACTUALIZAMOS USUARIOS
    $statement2 = $conexion->prepare($actualiza2);
    $statement2->bind_param("s", $usuario);
    $statement2->execute();

    $conexion->commit();
    header("Location: $location");
    
  } catch (Exception $e) {
    $conexion->rollback();

    header("Location: perfil.php?edit=false");
    die;
  }

  $conexion->close();
