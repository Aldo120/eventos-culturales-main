<?php
  include_once('../php/conexion.php');

  //var_dump($_GET);
  //$id = $_GET['id'];

  $evento = $_GET['evento'];
  $historial = $_GET['historial'];
  $comunidad = $_GET['comunidad'];
  $campoExtra = $_GET['campoExtra'];

  $msj = '';

  // SI ES ALUMNO Y NUNCA A CONTESTADO 
  if($historial == 'NO'){
    $datos = $conexion->query(
      "SELECT * 
       FROM $db.alumnos
       WHERE cuenta = '$campoExtra'"
    ) or die("Error al obtener los datos del alumno <br>" . mysqli_error($conexion));

    if($datos->num_rows > 0){
      $fila = $datos->fetch_assoc();
      $nombre = $fila['nombre'];
      $paterno = $fila['paterno'];
      $materno = $fila['materno'];
      $sexo = $fila['sexo'] == 'M' ? 1 : 2;
      $email = $fila['email_dominio'];
      $carrera = $fila['carrera'];
      $cuenta = $campoExtra;
    } else {
      $msj = 'No se encontro registro de este número de cuenta';
    }
  } 
  
  // EN CASO DE QUE YA HAYAN CONTESTADO
  else if ($historial == 'SI'){

    $datos = $conexion->query(
      "SELECT *
       FROM $db.asistentes
       WHERE comunidad = '$comunidad' 
       AND (email = '$campoExtra' or cuenta = '$campoExtra')"
    ) or die("Error al obtener los datos del asistente <br>" . mysql_error($conexion)); 

    if($datos->num_rows > 0){
      $fila = $datos->fetch_assoc();
      $id_asistente = $fila['id'];
      $nombre = $fila['nombre'];
      $paterno = $fila['ap_paterno'];
      $materno = $fila['ap_materno'];
      $sexo = $fila['sexo'];
      $email = $fila['email'];
      
      if($comunidad == 1){  
        $cuenta = $fila['cuenta'];  
        $carrera = $fila['carrera_ac'];  
      } else if ($comunidad == 2) $carrera = $fila['carrera_ac'];  
      else if ($comunidad == 3) $cuenta = $fila['cuenta'];  
      else if ($comunidad == 5) $descripcion = $fila['descripcion'];
      
    } else {
      $msj = 'No se encontro registro con la información ingresada';
    }
  }
  
  

  
  ?>


<!-- Modal Pendientes de almacenar -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

      </div>

      <form action="../admin/asistencia1.php" method="post">
        <input type="hidden"
               name="evento"
               id="evento"
               value="<?php echo $evento ?>">

        <input type="hidden"
               name="comunidad"
               id="comunidad"
               value="<?php echo $comunidad ?>">       
        <input type="hidden"
               name="historial"
               id="historial"
               value="<?php echo $historial ?>">

        <input type="hidden"
               name="sexo"
               id="sexo"
               value="<?php echo $sexo ?>">

        <?php
          if($historial == 'SI'){
        ?>
          <input type="hidden"
                 name="asistente"
                 id="asistente"
                 value="<?php echo $id_asistente ?>">
        <?php
          }
        ?>
               
               
               
        <div class="modal-body">  
          <?php 
            if($datos->num_rows > 0){ 
          ?>

              <div class="row">
                <p>Nombre(s):</p>
                <input type="text"
                       name="nombre"
                       id="nombre"
                       value="<?php echo $nombre ?>"
                       readonly>
              </div>

              <div class="row">
                <p>Apellido Paterno:</p>
                <input type="text"
                       name="ap_paterno"
                       id="ap_paterno"
                       value="<?php echo $paterno ?>"
                       readonly>
              </div>
                
              <div class="row">
                <p>Apellido Materno:</p>
                <input type="text"
                       name="ap_materno"
                       id="ap_materno"
                       value="<?php echo $materno ?>"
                       readonly>
              </div>

              <div class="row">
                <p>Correo electrónico:</p>
                <input type="text"
                       name="email"
                       id="email"
                       value="<?php echo $email ?>"
                       readonly>
              </div>
              
            <!-- VALIDACIONES DE LA COMUNIDAD, PARA MOSTRAR INFO EN MODAL -->
            <?php
              if($comunidad == 1){
            ?>
              <div class="row">
                <p># Cuenta:</p>
                <input type="text"
                       name="cuenta"
                       id="cuenta"
                       value="<?php echo $cuenta ?>"
                       readonly>
              </div>
              <div class="row">
                <p>Carrera:</p>
                <input type="text"
                       name="carrera"
                       id="carrera"
                       value="<?php echo $carrera ?>"
                       readonly>
              </div>                
            <?php
              } else if($comunidad == 2) {
            ?>
              <div class="row">
                <p>Carrera que Impartes:</p>
                <input type="text"
                       name="carrera"
                       id="carrera"
                       value="<?php echo $carrera ?>"
                       readonly>
              </div>
            <?php  
              } else if($comunidad == 3) {
            ?>
              
              <div class="row">
                <p>Número de Cuenta:</p>
                <input type="text"
                       name="cuenta"
                       id="cuenta"
                       value="<?php echo $cuenta ?>"
                       readonly>
              </div>
                    
            <?php  
              } else if($comunidad == 5) {
            ?>
            <p><?php echo $descripcion ?></p>
            <div class="row">
                <p>Descripción:</p>
                <input type="text"
                       name="descripcion"
                       id="descripcion"
                       value="<?php echo $descripcion ?>"
                       readonly>
              </div>
                    
          <?php 
              }
            } else {
          ?>
            </p><?php echo $msj; ?></p>
          <?php 
            } 
          ?>
        </div>

        <div class="modal-footer">
          <div class="btn-group">
            <button type="button" class="btn btn-info" data-dismiss="modal">Cerrar</button>
            <?php
            if($datos->num_rows > 0) echo '<input type="submit" value="Confirmar" class="btn btn-success">';
            ?>
          </div>
        </div>
      </form>




    </div>
  </div>
</div>