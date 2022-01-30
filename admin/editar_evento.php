<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  $id = $_GET['id'];

  $query = $conexion->query(
      "SELECT * FROM $db.eventos
       WHERE id = '$id'"
  ) or die("Error al obtener los datos del evento <br>" . mysqli_error($conexion));

  $fila = $query->fetch_assoc();
  $nombre_evento = $fila['nombre'];
  $fecha = $fila['fecha'];
  $hora = $fila['hora'];
  $hora_fin = $fila['hora_fin'];
  $descripcion = $fila['descripcion'];
  $imagen = $fila['imagen'];
  $ruta = ($imagen == 'evento-10') ? $imagen . '.png' : $imagen . '.jpg';

  // var_dump($_SESSION);
  
?>


<!DOCTYPE html>
<html lang="es-MX">
<head>
  <?php include ('includes/head.php') ?>

  
  <!-- Estilos CSS -->
  <!-- ================================================== -->
  <link rel="stylesheet" type="text/css" href="../css/estilos-crear-evento.css" />

  <!-- Titulo de la pagina -->
  <!-- ================================================== -->
  <title>Editar Evento</title>

</head>
<body>

  <?php include("includes/header.php") ?>


  <main>
    <h2>Editar evento</h2>

    <form action="eventos1.php" method="POST">
      <input type="hidden"
             name="accion"
             value="2">
      <input type="hidden"
             name="id"
             value="<?php echo $id ?>">
      <table class="tabla-formulario">

        <colgroup>
          <col style="width: 20%">
          <col style="width: 30%">
          <col style="width: 20%">
          <col style="width: 30%">
        </colgroup>

        <tbody>
          <tr>
            <th><label for="nombre">Nombre del evento:</label></th>
            <td colspan="3">
              <input type="text"
                     name="nombre"
                     id="nombre"
                     class="form-control"
                     title="Ingrese nombre del evento"
                     placeholder="Ingrese Nombre del evento"
                     value="<?php echo $nombre_evento ?>"
                     required>
            </td>
          </tr>

          <tr>
            <th><label for="fecha">Fecha del evento:</label></th>
            <td colspan="3">
              <input type="date"
                     name="fecha"
                     id="fecha"
                     class="form-control"
                     title="Ingrese fecha del evento"
                     value="<?php echo $fecha ?>"
                     required>
            </td>
          </tr>

          <tr>
            <th><label for="hora">Hora que inicia el evento:</label></th>
            <td>
              <input type="time"
                     name="hora"
                     id="hora"
                     class="form-control"
                     title="Ingrese hora del evento"
                     value="<?php echo $hora ?>"
                     required>
            </td>

            <th><label for="hora_fin">¿A qué hora acaba?:</label></th>
            <td>
              <input type="time"
                     name="hora_fin"
                     id="hora_fin"
                     class="form-control"
                     title="Ingrese hora del evento"
                     value="<?php echo $hora_fin ?>"
                     required>
            </td>     
            
          </tr>
          
          <tr>
            <th><label for="descripcion">Descripción del evento: <span>(opcional)</span></label></th>
            <td colspan="3">
              <textarea name="descripcion" 
                        id="desripcion" 
                        maxlength="120"
                        cols="30" 
                        title="Ingrese descripción del evento"
                        placeholder="Ingrese descripción del evento"
                        rows="3"><?php echo $descripcion ?></textarea>            
            </td>
          </tr>
        </tbody>
      </table>

      <h2>Seleccione imagen relacionada al evento</h2>

      <div class="contenedor-img"> 

        <?php 
        for ($i = 1; $i <= 10 ; $i++) { 
          $cadena = 'evento-' . $i;
          if($cadena == $imagen) echo '<div class="image evento-'.$i.' active"></div>';
          else echo '<div class="image evento-'.$i.'"></div>';
        }
        ?>
      </div>

      <input type="hidden"
             id="imagen"
             name="imagen"
             value="<?php echo $imagen ?>"> 

      <div class="submit">
        <input type="submit" 
               class="btn btn-info"
               value="Editar Evento">
      </div>
    </form>
  </main>
  

  <?php include 'includes/footer.html'?>


  <script src="../js/alerta-error.js"></script>
  <script>
    const d = document,
          $images = d.querySelectorAll(".image"),
          $inputImg = d.getElementById("imagen"),
          $hora_ini = d.getElementById("hora"),
          $hora_fin = d.getElementById("hora_fin");
          
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector(".tres").classList.add("active");
      $hora_fin.disabled = true;
    })

    // VALIDACION DE HORAS
    $hora_ini.onblur= () => {
      $hora_fin.disabled = false;
      $hora_fin.focus();
      $hora_fin.min = $hora_ini.value;
    } 
    
    $hora_fin.onblur = () => {
      if($hora_fin.value <= $hora_ini.value){
        Alerta1('La hora final del evento no puede ser antes de que inicie.', 'error')
      }
    }

    // FUNCIONALIDAD DE GALERIA (SELECCIONAR)
    const quitarActive = () => {
      $images.forEach(item => item.classList.remove("active"))
    }

    $images.forEach(item => {
      item.addEventListener('click', () => {
        quitarActive()
        item.classList.add('active')
        $inputImg.value = item.classList[1]
      })
    })
  </script>
</body>
</html>