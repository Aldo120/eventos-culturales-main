<?php

  // Iniciar sesion
  session_start();



  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  $hora_local = date('H:i:s', time());
  // echo $hora_local;

  $salida = '';

  // var_dump($_POST);
  $nombre = (!isset($_POST['nombre'])) ? '' : $_POST["nombre"];
  $fecha = (!isset($_POST['fecha'])) ? '' : $_POST["fecha"];

  $consulta = "SELECT *
               FROM $db.eventos
               ORDER BY id DESC";

  if($nombre != '' && $fecha == ''){
    $consulta = "SELECT *
                 FROM $db.eventos
                 WHERE nombre LIKE '%".$nombre."%'
                 ORDER BY id DESC";
  }

  if($nombre == '' && $fecha != ''){
    $consulta = "SELECT *
                 FROM $db.eventos
                 WHERE fecha = '$fecha'
                 ORDER BY id DESC";
  }

  if($nombre != '' && $fecha != ''){
    $consulta = "SELECT *
                 FROM $db.eventos
                 WHERE nombre LIKE '%".$nombre."%'
                 AND fecha = '$fecha'
                 ORDER BY id DESC";
  }


  $query = $conexion->query($consulta)or die("Error al mostrar eventos <br>" . mysqli_error($conexion));
  $total = $query->num_rows;

  if($total > 0) {
    // if($nombre != '' && $fecha != ''){
    //   $salida .= "<h2>Eventos disponibles para mostrar</h2>";
    // } else {
    //   $salida .= "<h2>Eventos disponibles para mostrar con el criterio de búsqueda $total</h2>";
    // }

    $salida .= '
    <table class="tabla-datos">
      <caption>Últimos eventos registrados</caption>
      
      <colgroup>
        <col style="width: 80px">
        <col style="width: 250px">
        <col style="width: 150px">
        <col style="width: 150px">
        <col style="width: 70px">
        <col style="width: 70px">
        <col style="width: 70px">
        <col style="width: 70px">
      </colgroup>

      <thead>
        <tr>
          <th rowspan="2">No.</th>
          <th rowspan="2">Evento</th>
          <th rowspan="2">Fecha de evento</th>
          <th rowspan="2">Hora de evento</th>
          <th colspan="4">Acciones</th>
        </tr>

        <tr>
          <th>Consultar</th>
          <th>Editar</th>
          <th>Eliminar</th>
          <th>Informe</th>
        </tr>
      </thead>

      <tbody>';



  $n = 0;
  while($q = $query->fetch_assoc()){
    $n++;
    $id_evento = $q['id'];
    $nombre = $q['nombre'];
    $fecha = strftime('%Y-%m-%d', strtotime($q['fecha']));
    $hora = $q['hora'];
    $hora_fin = $q['hora_fin'];

    //CONSULTA A LA BD DE ASISTENCIAS.
    $consulta = $conexion -> query(
      "SELECT id
      FROM $db.asistencias
      WHERE id_evento = '$id_evento'"
    ) or die("Error al obtener los datos <br>" . mysqli_error($conexion));

    // COLOCAR ICONOS EDITAR,ELIMINAR Y VER INFORME
    $editar = '<td>
                <a href="editar_evento.php?id='.$id_evento.'" class="ico-edita"
                   title="Editar Evento">
                   <i class="fas fa-pencil-alt"></i>
                </a>
               </td>';


    $eliminar = '<td>
                  <a onclick="abrirModalElimina('.$id_evento.')" class="ico-elimina"
                      title="Eliminar Evento">
                  <i class="fas fa-trash-alt"></i>
                  </a>
                </td>';

    $informe = '<td>
                  <a href="imprime_informe.php?id='.$id_evento.'" class="ico-informe"
                      title="Ver Informe del Evento" target="_blank">
                    <i class="fas fa-file-alt"></i>
                  </a>
                </td>';


    // SI NO HAY ASISTENTES, MOSTRAR UN TACHE EN EL INFORME
    if($consulta->num_rows == 0) {
      $informe = '<td>
                    <a class="ico-elimina"
                      title="No hay datos para ver informe">
                      <i class="fas fa-times"></i>
                    </a>
                  </td>';
    }



    // EVENTOS PASADOS NO PERMITIR EDITAR Y ELIMINAR
    if($fecha < $fechahoy){
      $editar = '<td>
                  <a class="ico-elimina"
                     title="No se puede editar este evento">
                     <i class="fas fa-times"></i>
                  </a>
                 </td>';

      $eliminar = '<td>
                 <a class="ico-elimina"
                    title="No se puede eliminar este evento">
                    <i class="fas fa-times"></i>
                 </a>
                </td>';
    }


    // EVENTOS DE HOY, VALIDAR POR HORAS 
    if($fecha == $fechahoy && $hora_local > $hora_fin){
      $editar = '<td>
                  <a class="ico-elimina"
                     title="No se puede editar este evento">
                     <i class="fas fa-times"></i>
                  </a>  
                 </td>';

      $eliminar = '<td>
                 <a class="ico-elimina"
                    title="No se puede eliminar este evento">
                    <i class="fas fa-times"></i>
                 </a>
                </td>';

    } else if($fecha == $fechahoy && ($hora_local > $hora && $hora_local < $hora_fin)){
      $editar = '<td>
                  <a class="ico-elimina"
                    title="No se puede editar este evento">
                    <i class="fas fa-times"></i>
                  </a>  
                </td>';

      $eliminar = '<td>
                <a class="ico-elimina"
                    title="No se puede eliminar este evento">
                    <i class="fas fa-times"></i>
                </a>
                </td>';

      $informe = '<td>
                    <a class="ico-elimina"
                      title="El evento esta en curso">
                      <i class="fas fa-times"></i>
                    </a>
                  </td>';
    }
  
    // CONTENIDO DE LA TABLA
    $salida .='
          <tr>
            <td>'.$n.'</td>
            <td>'.$nombre.'</td>
            <td>'.$fecha.'</td>
            <td>'.$hora.' hrs</td>
            <td>
              <a onclick="abrirModal('.$id_evento.')" class="ico-consulta"
                  title="Consultar Evento">
                <i class="fas fa-eye"></i>
              </a>
            </td>
            '.$editar.'
            '.$eliminar.'
            '.$informe.'
          </tr>';


  }
  $salida .='    
      </tbody>
    </table>
    ';
  }

  echo $salida;
  $conexion->close();

  