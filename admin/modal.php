<<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  $id = $_GET['id'];
  $hora_actual = date('H:i:s', time());

  $query = $conexion->query (
      "SELECT *
      FROM $db.eventos
      WHERE id = '$id'
      ORDER BY id DESC"
  )or die("Error al mostrar eventos <br>" . mysqli_error($conexion));

  $fila = $query->fetch_assoc();
  $nombre = $fila['nombre'];
  $fecha = $fila['fecha'];
  $hora = $fila['hora'];
  $hora_fin = $fila['hora_fin'];
  $descripcion = $fila['descripcion'];
  $imagen = $fila['imagen'];
  $ruta = ($imagen == 'evento-10') ? $imagen . '.png' : $imagen . '.jpg';

  $fecha = strftime("%Y-%m-%d", strtotime($fecha));
  $fechahoy = date("Y-m-d");

  if($fechahoy > $fecha) $tiempo = 'PASADO';
  else if($fecha == $fechahoy) $tiempo = 'PRESENTE';
  else if($fecha > $fechahoy) $tiempo = 'FUTURO';

  
  ?>


<!-- Modal Pendientes de almacenar -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

      </div>

      <div class="modal-body">  
        <div class="card">

          <div class="card-img">
            <img src="../img/eventos/<?php echo $ruta ?>" alt="<?php echo $imagen ?>">
          </div>  
          
          <div class="info">
            <h3><?php echo $nombre ?></h3>
            <h5><?php echo strftime("%d/%m/%Y", strtotime($fecha)) . ' ' . $hora . ' hrs' ?></h5>
            <p><?php echo $descripcion ?></p>
            
          </div>
  
        </div>
      </div>

      <div class="modal-footer">
        <div class="btn-group">
          <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
        </div>
      </div>

    </div>
  </div>
</div>



<!-- Modal Para Eliminar Evento -->
<div class="modal fade" id="myModalElimina" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

      </div>

      <div class="modal-body">  
        <div class="card">

          <div class="card-img">
            <img src="../img/eventos/<?php echo $ruta ?>" alt="<?php echo $imagen ?>">
          </div>  
          
          <div class="info">
            <h3><?php echo $nombre ?></h3>
            <h5><?php echo strftime("%d/%m/%Y", strtotime($fecha)) . ' ' . $hora . ' hrs' ?></h5>
            <p><?php echo $descripcion ?></p>
            
          </div>
  
        </div>
      </div>

      <div class="modal-footer">
        <div class="btn-group">
          <form action="eventos1.php" method="post">
            <input type="hidden" 
                   name="accion"
                   value="3">
            <input type="hidden" 
                   name="id"
                   value="<?php echo $id ?>">
            <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
            <input type="submit" 
                   class="btn btn-danger"
                   value="Eliminar Evento">
          </form>
        </div>
      </div>

    </div>
  </div>
</div>




<!-- Modal Para acciones en actuales -->
<div class="modal fade" id="modalAcciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header"> 
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3>Acciones para <?php echo $nombre ?></h3>
      </div>

      <div class="modal-body">  
        <div class="acciones">
          <?php
            if($tiempo == 'FUTURO'){
          ?>
              <a href="editar_evento.php?id=<?php echo $id ?>" title="Editar Evento"
                 class="ico-edita"><i class="fas fa-pencil-alt"></i></a>
              <a href="eventos1.php?id=<?php echo $id ?>" title="Eliminar Evento"
                 class="ico-elimina"><i class="fas fa-trash-alt"></i></a>
          <?php  
            }          
          ?>  

          <?php
            if($tiempo == 'PASADO'){
              // BUSCAR QUE EL EVENTO TENGA ASISTENCIAS 
              $qasistencias = $conexion->query(
                "SELECT id FROM $db.asistencias
                  WHERE id_evento = '$id'"
              ) or die("Error al obtener asistencias del evento <br>" . mysqli_error($conexion));
              
              // SI TIENE INDICADORES MOSTRAR INFORME
              if($qasistencias->num_rows > 0){
                echo '<a href="imprime_informe.php?id='.$id.'" title="Imprimir Informe de Evento"
                class="ico-informe" target="_blank"><i class="fas fa-file-alt"></i></a>';
              }

              // SI NO TIENE INDICADORES MOSTRAR ALERTA AMARILLA
              else {
                echo '<div class="alert alert-warning" role="alert" style="display: flex; justify-content: center; align-items:center; text-align: center;">
                No es posible mostrar un informe, dado que no se registro ninguna asistencia al evento.
                      </div>';
              }
          ?>
            <!--<a href="imprime_informe.php?id=<?php echo $id ?>" title="Imprimir Informe de Evento"
               class="ico-informe" target="_blank"><i class="fas fa-file-alt"></i></a>-->
            <!-- <a href="" title="Ver Gráficos del Evento"
               class="ico-grafico"><i class="fas fa-chart-pie"></i></a> -->
          <?php
            } else if($tiempo == 'PRESENTE'){

              // EL EVENTO AUN NO INICIALES
              if($hora_actual < $hora){
                echo '<a href="editar_evento.php?id=<?php echo $id ?>" title="Editar Evento"
                         class="ico-edita"><i class="fas fa-pencil-alt"></i></a>
                      <a href="eventos1.php?id=<?php echo $id ?>" title="Eliminar Evento"
                         class="ico-elimina"><i class="fas fa-trash-alt"></i></a>';
              } 

              // EL EVENTO YA INICIO 
              else if($hora_actual >= $hora && $hora_actual <= $hora_fin){
                echo '<div class="alert alert-warning" role="alert" style="display: flex; justify-content: center; align-items:center; text-align: center;">
                        No es posible realiazar acciones en este evento, ya que se encuentra en curso.
                      </div>';
              } 

              // EL EVENTO YA TERMINO
              else if($hora_actual > $hora){

                // BUSCAR QUE EL EVENTO TENGA ASISTENCIAS 
                $qasistencias = $conexion->query(
                  "SELECT id FROM $db.asistencias
                   WHERE id_evento = '$id'"
                ) or die("Error al obtener asistencias del evento <br>" . mysqli_error($conexion));
                
                // SI TIENE INDICADORES MOSTRAR INFORME
                if($qasistencias->num_rows > 0){
                  echo '<a href="imprime_informe.php?id='.$id.'" title="Imprimir Informe de Evento"
                  class="ico-informe" target="_blank"><i class="fas fa-file-alt"></i></a>';
                }

                // SI NO TIENE INDICADORES MOSTRAR ALERTA AMARILLA
                else {
                  echo '<div class="alert alert-warning" role="alert" style="display: flex; justify-content: center; align-items:center; text-align: center;">
                  No es posible mostrar un informe, dado que no se registro ninguna asistencia al evento.
                        </div>';
                }
              } 

            }
          ?>
        </div>
      </div>

    </div>
  </div>
</div>