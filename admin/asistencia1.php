<?php

  include_once('../php/conexion.php');
  

  // var_dump($_POST);

  // VARIABLES QUE SIEMPRE VAMOS A RECIBIR
  $evento = $_POST['evento'];
  $comunidad = $_POST['comunidad'];
  $historial = $_POST['historial'];
  $sexo = $_POST['sexo'];
  $nombre = mb_strtoupper(trim(filter_var($_POST['nombre'], FILTER_SANITIZE_STRING)));
  $ap_paterno = mb_strtoupper(trim(filter_var($_POST['ap_paterno'], FILTER_SANITIZE_STRING)));
  $ap_materno = mb_strtoupper(trim(filter_var($_POST['ap_materno'], FILTER_SANITIZE_STRING)));
  $email = $_POST['email'];

  $cuenta_validar = isset($_POST['cuenta']) ? $_POST['cuenta'] : 'SIN CUENTA';
  $ex_cuenta_validar = isset($_POST['ex_cuenta']) ? $_POST['ex_cuenta'] : 'SIN CUENTA';

  // VALIDAR QUE SI NO HAN CONTESTADO Y EXISTE UN ASISTENTE 
  // CON EL MISMO CORREO O NUMERO DE CUENTA 
  // MANDAR MENSAJE QUE YA HAN CONTESTADO ANTERIORMENTE
  if($historial == 'NO'){

    $validar = $conexion->query(
      "SELECT id 
       FROM $db.asistentes 
       WHERE email = '$email' OR cuenta = '$cuenta_validar' OR cuenta = '$ex_cuenta_validar'" 
    ) or die("Error al validar existencia de correo o cuenta <br>" . mysqli_error($conexion));
  
    if($validar->num_rows > 0){
      header("Location: ../asistencia/index.php?existente=false");
      $validar->free();
      die;
    } 
  }


  // PRIMER CASO:
  // HISTORIAL: NO, COMUNIDAD: ALUMNO (1)
  if($comunidad == 1 && $historial == 'NO'){
    $carrera = $_POST['carrera'];
    $cuenta = $_POST['cuenta'];

    $query = $conexion->query(
      "INSERT INTO $db.asistentes (comunidad, nombre, ap_paterno, ap_materno, email, sexo, carrera_ac, cuenta)
       VALUES ('$comunidad', '$nombre', '$ap_paterno', '$ap_materno', '$email', '$sexo', '$carrera', '$cuenta')"
    ) or die("Error al insertar los datos del asistente  <br>" . mysqli_error($conexion));
    
  } 
  // HISTORIAL: NO, COMUNIDAD: ACADEMICO (2)
  else if($historial == 'NO' && $comunidad == 2) {
    $carrera = ', ' . mb_strtoupper(trim(filter_var($_POST['carrera'], FILTER_SANITIZE_STRING)));

    $query = $conexion->query(
      "INSERT INTO $db.asistentes (comunidad, nombre, ap_paterno, ap_materno, email, sexo, carrera_ac)
       VALUES ('$comunidad', '$nombre', '$ap_paterno', '$ap_materno', '$email', '$sexo', '$carrera')"
    ) or die("Error al insertar los datos del asistente  <br>" . mysqli_error($conexion));
  }
  // HISTORIAL: NO, COMUNIDAD: EXALUMNO (3)
  else if($historial == 'NO' && $comunidad == 3) {
    $cuenta = $_POST['ex_cuenta'];

    $query = $conexion->query(
      "INSERT INTO $db.asistentes (comunidad, nombre, ap_paterno, ap_materno, email, sexo, cuenta)
       VALUES ('$comunidad', '$nombre', '$ap_paterno', '$ap_materno', '$email', '$sexo', '$cuenta')"
    ) or die("Error al insertar los datos del asistente  <br>" . mysqli_error($conexion));
  }
  // HISTORIAL: NO, COMUNIDAD: TRABAJADOR (5)
  else if($historial == 'NO' && $comunidad == 4) {
    $query = $conexion->query(
      "INSERT INTO $db.asistentes (comunidad, nombre, ap_paterno, ap_materno, email, sexo)
       VALUES ('$comunidad', '$nombre', '$ap_paterno', '$ap_materno', '$email', '$sexo')"
    ) or die("Error al insertar los datos del asistente  <br>" . mysqli_error($conexion)); 
  }
  // HISTORIAL: NO, COMUNIDAD: PUBLICO EXTERNO (5)
  else if($historial == 'NO' && $comunidad == 5) {
    $descripcion = $_POST['descr'];
    $query = $conexion->query(
      "INSERT INTO $db.asistentes (comunidad, nombre, ap_paterno, ap_materno, email, sexo, descripcion)
       VALUES ('$comunidad', '$nombre', '$ap_paterno', '$ap_materno', '$email', '$sexo', '$descripcion')"
    ) or die("Error al insertar los datos del asistente  <br>" . mysqli_error($conexion));
  }
  
  
  // REGISTRAR COMO ASISTENTE
  if($historial == 'NO'){
    $asistente = $conexion->insert_id;
  }

  else {
    $asistente = $_POST['asistente'];    

    // VALIDAR QUE NO ESTE REGISTRADO ESTE ASISTENTE CON ESTE EVENTO
    $validar = $conexion->query(
      "SELECT id 
       FROM $db.asistencias 
       WHERE id_evento = '$evento' AND id_asistente = '$asistente'"
    ) or die("Error al validar asistencia <br>" . mysqli_error($conexion));
    if($validar->num_rows > 0){
      header("Location: ../asistencia/index.php?asistencia=true");
      $validar->free();
      die;
    } 
  }
  
  
  // REGISTRAR ASISTENCIA AL EVENTO 
  $statement = $conexion-> prepare(
    "INSERT INTO $db.asistencias (id_evento, id_asistente)
     VALUES (?, ?)" 
  )or die("Error al insertar asitencia a evento <br>" . mysqli_error($conexion));

  $statement->bind_param("ii", $evento, $asistente);
  $statement->execute();


  // ACTUALIZAR INDICADORES 
  $prefijos = [
    1 => 'hombres_',
    2 => 'mujeres_',
    3 => 'se_',
    4 => 'inter_'
  ];

  $posfijos = [
    1 => 'al',
    2 => 'ac',
    3 => 'ex',
    4 => 't',
    5 => 'p',
  ];

  $campo_general = $prefijos[$sexo] . 'g';
  $campo_esp = $prefijos[$sexo] . $posfijos[$comunidad];  

  // echo $campo_general;
  // echo $campo_esp;

  // OBTENER DATOS DE TABLA INDICADORES
  $query2 = $conexion->query(
    "SELECT $campo_general, $campo_esp 
     FROM $db.indicadores 
     WHERE id_evento = '$evento'"
  ) or die("Error al obtener los datos de indicadores <br>" . mysqli_error($conexion));

  $fila2 = $query2->fetch_assoc();
  $query2->free();
  $campo_general_valor = $fila2[$campo_general] + 1;
  $campo_esp_valor = $fila2[$campo_esp] + 1;

  // echo $campo_general . ' = ' . $campo_general_valor . '<br>';
  // echo $campo_esp . ' = ' . $campo_esp_valor;

  // ACTUALIZAR INDICADORES 
  $query2 = $conexion->query(
    "UPDATE $db.indicadores 
     SET $campo_general = '$campo_general_valor', $campo_esp = '$campo_esp_valor'
     WHERE id_evento = '$evento'"
  ) or die("Error al obtener los datos de indicadores <br>" . mysqli_error($conexion));

  header("Location: ../asistencia/index.php?create=true");