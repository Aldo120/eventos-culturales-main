<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  // var_dump($_POST);

  if(isset($_GET['id'])){
    $accion = 3;
    $id = $_GET['id'];

  } else {

    // RECIBIMOS VARIABLES POR METODO POST
    $nombre = mb_strtoupper(trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING)));
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $hora_fin = $_POST['hora_fin'];
    $descripcion = trim(filter_var($_POST['descripcion'], FILTER_SANITIZE_STRING));
    $descripcion = ($descripcion == '') ? 'SIN DESCRIPCIÓN' : $descripcion;
    $imagen = $_POST['imagen'];
  
    $accion = (int)$_POST['accion'];
    $usuario = $_SESSION['usuario'];
    $id = $_POST['id'];
  }
  

  // CREAR EVENTO
  if($accion == 1){
    $tipo = 'create';
    $statement = $conexion->prepare(
          "INSERT INTO $db.eventos (nombre, fecha, hora, hora_fin, descripcion, imagen, usuario)
           VALUES (?, ?, ?, ?, ?, ?, ?)"
    ) or die ("Error al crear el evento <br>" . mysqli_error($conexion));
  
    $statement->bind_param("sssssss", $nombre, $fecha, $hora, $hora_fin, $descripcion, $imagen, $usuario);
  }


  // EDITAR EVENTO
  else if($accion == 2) {
    $tipo = 'edit';
    $id = $_POST['id'];

    $statement = $conexion->prepare(
      "UPDATE  $db.eventos
       SET nombre = ?, fecha = ?, hora = ?, hora_fin = ?, descripcion = ?, imagen = ?, usuario = ?
       WHERE id = ?"
    ) or die ("Error al editar el evento <br>" . mysqli_error($conexion));

    $statement->bind_param("sssssssi", $nombre, $fecha, $hora, $hora_fin, $descripcion, $imagen, $usuario, $id);
  }


  // ELIMINAR EVENTO
  else if($accion == 3){
    $tipo = 'delete';
    // $id = $_POST['id'];

    $statement = $conexion->prepare(
      "DELETE FROM $db.eventos
       WHERE id = ?"
    ) or die ("Error al editar el evento <br>" . mysqli_error($conexion));

    $statement->bind_param("i", $id);
  }

  if(!$statement->execute()){
    header("Location: eventos.php?$tipo=false");
     //echo 'Ocurrio un error <br>';
    //die(mysqli_error($conexion));
  } else {
    
    if($tipo == 'create'){
      // CREAR REGISTRO DE INDICADORES
      $evento_ingresado = $conexion->insert_id;
      $statement2 = $conexion->prepare(
        "INSERT INTO $db.indicadores (id_evento)
         VALUES (?)"
      ) or die ("Error al crear el evento <br>" . mysqli_error($conexion));

      $statement2->bind_param("i", $evento_ingresado);
      $statement2->execute();
    }
    
    header("Location: eventos.php?$tipo=true");
  }
  

?>