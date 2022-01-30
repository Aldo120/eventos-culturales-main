<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');
  
  $tipo = '';
  $msj1 = '';
  $msj2 = '';

  if(isset($_GET['create'])){
    $tipo = 'create';
    $msj1 = 'Evento creado con éxito.';
    $msj2 = 'Error al crear evento.';
  }

  else if(isset($_GET['edit'])){
    $tipo = 'edit';
    $msj1 = 'Evento editado con éxito.';
    $msj2 = 'Error al editar evento.';
  }

  else if(isset($_GET['delete'])){
    $tipo = 'delete';
    $msj1 = 'Evento eliminado con éxito.';
    $msj2 = 'Error al eliminar evento.';
  }
    


?>

<!DOCTYPE html>
<html lang="es-MX">
<head>
  <?php include ('includes/head.php') ?>

  <!-- Estilos CSS -->
  <!-- ================================================== -->
  <link rel="stylesheet" type="text/css" href="../css/estilos-eventos.css" />

  <!-- Titulo de la pagina -->
  <!-- ================================================== -->
  <title>Eventos</title>

</head>
<body>

  <?php include("includes/header.php") ?>
  
  <img src="../img/torres.jpg" alt="Torres Fes" class="marcaAgua">

  <main>
    <h2>Eventos</h2>

    <div class="botonera">
      <a href="crear_evento.php" class="btn btn-primary">Crear Evento</a>
    </div>

    <!-- TABLA PARA FILTRO DE BUSQUEDA -->
    <table class="tabla-consulta">
      <caption><h4>Busca un evento por su nombre o fecha</h4></caption>
      <colgroup>
        <col style="width: 20%">
        <col style="width: 30%">
        <col style="width: 20%">
        <col style="width: 30%">
      </colgroup>

      <tbody>
        <tr>
          <th>Nombre del evento:</th>
          <td>
            <input type="text" 
                   name="nombre_evento" 
                   id="nombre_evento"
                   class="entrada"
                   title="Busque un evento por su nombre"
                   placeholder="Nombre del evento" 
                   autofocus>
          </td>
          <th>Fecha del evento:</th>
          <td>
            <input type="date"
                   name="fecha_evento"
                   id="fecha_evento"
                   class="entrada"
                   title="Busque un evento por su fecha">
          </td>
        </tr>
      </tbody>
    </table>


    <div class="tabla-scroll-container" id="tabla-contenedor">
      Aqui van a aparecer los resultados
    </div>
  </main>

  <?php include 'includes/footer.html'?>

  <div id="divModal">
            
  </div>

  <script src="../js/alerta-error.js"></script>

  <script>
    // ALERTA
    let tipo = "<?php echo $tipo ?>",
        msj1 = "<?php echo $msj1 ?>",
        msj2 = "<?php echo $msj2 ?>";

    if(tipo != ""){
      Alerta(msj1,msj2,tipo);
    }

    // 
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector(".dos").classList.add("active");
      buscaEventos()
    })

    const d = document,
          $nombreEvento = d.getElementById('nombre_evento'),
          $fechaEvento = d.getElementById('fecha_evento');

    // AJAX PARA OBTENER EVENTO POR FILTRO
    const buscaEventos = (nombre = '', fecha ='') => {
      $.ajax({
        url: 'busca_eventos.php',
        type: 'POST',
        dataType: 'html',
        data: {
          nombre: nombre, 
          fecha: fecha
        }
      })


      .done(function(respuesta){
        $("#tabla-contenedor").html(respuesta);
      })

      .fail(function(){
        console.log("error")
      })
    }

    $nombreEvento.addEventListener('keyup', () => {
      buscaEventos($nombreEvento.value, $fechaEvento.value)
      console.log('nombre ' + $nombreEvento.value + ' fecha ' + $fechaEvento.value)
    })

    $fechaEvento.addEventListener('change', () => {
      buscaEventos($nombreEvento.value, $fechaEvento.value)
      console.log('nombre ' + $nombreEvento.value + ' fecha ' + $fechaEvento.value)
    })
    
    // FUNCION PARA ABRIR EL MODAL
    const abrirModal = (id) => {
      let ruta = 'modal.php?id=' + id;

      $.get(ruta, function (data) {
        $('#divModal').html(data);
        $('#myModal').modal('show');
      });
    }   
    
    // FUNCION PARA ABRIR EL MODAL
    const abrirModalElimina = (id) => {
      let ruta = 'modal.php?id=' + id;

      $.get(ruta, function (data) {
        $('#divModal').html(data);
        $('#myModalElimina').modal('show');
      });
    }   
  </script>
</body>
</html>