<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  $fechahoy = date("Y-m-d");

  $fecha_prox_1 = date("Y-m-d",strtotime($fechahoy."+ 1 days")); 
  $fecha_prox_2 = date("Y-m-d",strtotime($fechahoy."+ 6 days")); 

  $fecha_ant_1 = date("Y-m-d",strtotime($fechahoy."- 1 days"));
  $fecha_ant_2 = date("Y-m-d",strtotime($fechahoy."- 6 days")); 

  // echo $fecha_ant_1 . "<br>";
  // echo $fecha_ant_2 . "<br>";

  $eventos_hoy = $conexion->query(
        "SELECT * FROM $db.eventos
         WHERE fecha = '$fechahoy'"
  ) or die("Error al obtener eventos de hoy <br>" . mysqli_error($conexion));

  $eventos_recientes = $conexion->query(
    "SELECT * FROM $db.eventos
     WHERE fecha BETWEEN '$fecha_ant_2' AND '$fecha_ant_1'"
) or die("Error al obtener eventos recientes <br>" . mysqli_error($conexion));

$eventos_proximos = $conexion->query(
  "SELECT * FROM $db.eventos
   WHERE fecha BETWEEN '$fecha_prox_1' AND '$fecha_prox_2'"
) or die("Error al obtener eventos proximos <br>" . mysqli_error($conexion));

  // var_dump($_SESSION);
  
?>

<!DOCTYPE html>
<html lang="es-MX">
<head>
  <?php include ('includes/head.php') ?>

  <!-- ESTILOS DEL CARRUSEL-->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.css">

  <!-- Estilos CSS -->
  <!-- ================================================== -->
  <link rel="stylesheet" type="text/css" href="../css/estilos-actuales.css" />
  <link rel="stylesheet" type="text/css" href="../css/modal-actuales.css" />


  <!-- Titulo de la pagina -->
  <!-- ================================================== -->
  <title>Inicio</title>

</head>
<body>

  <?php include("includes/header.php") ?>

  <img src="../img/torres.jpg" alt="Torres Fes" class="marcaAgua">

  <main class="contenedor">

  <!-- PRIMERA SECCION -->
    <section class="seccion">
      <h2 class="subtitulo">Eventos del día de hoy</h2>
      <?php
        if($eventos_hoy->num_rows == 0){
          echo "<h3 style='text-align: center;'>No hay eventos por mostrar </h3>";
        } else {
          echo '<button aria-label="Previous" class="carrusel-ant ant1"><i class="fas fa-chevron-left"></i></button>';
        }
      ?>

      <div class="cards cards1">

      <?php
        while($fila = $eventos_hoy->fetch_assoc()){
          $id_evento = $fila['id'];
          $nombre = $fila['nombre'];
          $fecha = $fila['fecha'];
          $hora = $fila['hora'];
          $hora_fin = $fila['hora_fin'];
          $descripcion = $fila['descripcion'];
          $imagen = $fila['imagen'];
          $ruta = ($imagen == 'evento-10') ? $imagen . '.png' : $imagen . '.jpg';
      ?>
        <a class="card" onclick="abrirModal(<?php echo $id_evento ?>)">
          <div class="card-img">
            <img src="../img/eventos/<?php echo $ruta ?>" alt="<?php echo $imagen ?>">
          </div>  
          
          <div class="info">
            <h3><?php echo $nombre ?></h3>
            <h5><?php echo strftime("%d/%m/%Y", strtotime($fecha)) . ' de: ' . $hora . ' a: ' . $hora_fin?></h3>
            <p><?php echo $descripcion ?></p>
          </div>
        </a>
      <?php
        }
      ?>

      </div>

      <?php
      if($eventos_hoy->num_rows > 0){
          echo '<button aria-label="Next" class="carrusel-sig sig1"><i class="fas fa-chevron-right"></i></button>';
        }
      ?>

    </section>

    <div role="tablist" class="carrusel-indicadores indicadores1"></div>


    <!-- SEGUNDA SECCION -->
    <section class="seccion">
      <h2 class="subtitulo">Eventos recientes</h2>
      <?php
        if($eventos_recientes->num_rows == 0){
          echo "<h3 style='text-align: center;'>No hay eventos por mostrar </h3>";
        } else {
          echo '<button aria-label="Previous" class="carrusel-ant ant2"><i class="fas fa-chevron-left"></i></button>';
        }
      ?>

      

      <div class="cards cards2">

      <?php
        if($eventos_recientes->num_rows > 0){

          while($fila = $eventos_recientes->fetch_assoc()){
            $id_evento = $fila['id'];
            $nombre = $fila['nombre'];
            $fecha = $fila['fecha'];
            $hora = $fila['hora'];
            $hora_fin = $fila['hora_fin'];
            $descripcion = $fila['descripcion'];
            $imagen = $fila['imagen'];
            $ruta = ($imagen == 'evento-10') ? $imagen . '.png' : $imagen . '.jpg';
        ?>
          <a class="card" onclick="abrirModal(<?php echo $id_evento ?>)">
            <div class="card-img">
              <img src="../img/eventos/<?php echo $ruta ?>" alt="<?php echo $imagen ?>">
            </div>  
            
            <div class="info">
              <h3><?php echo $nombre ?></h3>
              <h5><?php echo strftime("%d/%m/%Y", strtotime($fecha)) . ' de: ' . $hora . ' a: ' . $hora_fin?></h3>
              <p><?php echo $descripcion ?></p>
            </div>
          </a>
        <?php
          }
        }
      ?>
      
      </div>

      <?php
      if($eventos_recientes->num_rows > 0){
          echo '<button aria-label="Next" class="carrusel-sig sig2"><i class="fas fa-chevron-right"></i></button>';
        }
      ?>
    </section>

    <div role="tablist" class="carrusel-indicadores indicadores2"></div>



    <!-- TERCERA SECCION -->
    <section class="seccion">
      <h2 class="subtitulo">Eventos próximos</h2>

      <?php
        if($eventos_proximos->num_rows == 0){
          echo "<h3 style='text-align: center;'>No hay eventos por mostrar </h3>";
        } else {
          echo '<button aria-label="Previous" class="carrusel-ant ant3"><i class="fas fa-chevron-left"></i></button>';
        }
      ?>

      <div class="cards cards3">
      
      <?php
        while($fila = $eventos_proximos->fetch_assoc()){
          $id_evento = $fila['id'];
          $nombre = $fila['nombre'];
          $fecha = $fila['fecha'];
          $hora = $fila['hora'];
          $hora_fin = $fila['hora_fin'];
          $descripcion = $fila['descripcion'];
          $imagen = $fila['imagen'];
          $ruta = ($imagen == 'evento-10') ? $imagen . '.png' : $imagen . '.jpg';
      ?>
        <a class="card" onclick="abrirModal(<?php echo $id_evento ?>)">
          <div class="card-img">
            <img src="../img/eventos/<?php echo $ruta ?>" alt="<?php echo $imagen ?>">
          </div>  
          
          <div class="info">
            <h3><?php echo $nombre ?></h3>
            <h5><?php echo strftime("%d/%m/%Y", strtotime($fecha)) . ' de: ' . $hora . ' a: ' . $hora_fin?></h3>
            <p><?php echo $descripcion ?></p>
          </div>
        </a>
      <?php
        }
      ?>

      </div>

      <?php
        if($eventos_proximos->num_rows > 0){
          echo '<button aria-label="Next" class="carrusel-sig sig3"><i class="fas fa-chevron-right"></i></button>';
        }
      ?>
      
    </section>

    <div role="tablist" class="carrusel-indicadores indicadores3"></div>

  </main>

  <div id="divModal"></div>

  <?php include 'includes/footer.html'?>


  


  <script src="https://cdn.jsdelivr.net/npm/glider-js@1.7.7/glider.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector(".uno").classList.add("active");
    })


    // FUNCION PARA GENERAR CARRUSEL A TODOS LOS CONTENEDORES
    const carrusel = (lista, indicadores, anterior, siguiente, columnas) => {
      new Glider(document.querySelector('.'+lista), {
        slidesToShow: columnas,
        slidesToScroll: columnas,
        draggable: true,
        dots: '.'+indicadores,
        arrows: {
          prev: '.'+anterior,
          next: '.'+siguiente
        }
      })
    }

    // FUNCION PARA ASIGNAR LAS FUNCIONALIDAD DE LOS ELEMENTOS.
    window.addEventListener('load', () => {

      let ancho = document.body.clientWidth,
          columnas = 0;

      if(ancho > 1200) columnas = 3
      else if(ancho >= 801 && ancho <= 1200) columnas = 2
      else if(ancho < 801) columnas = 1

      // AGREGAR FUNCIONALIDAD DE CARRUSELES A LOS CONTENEDORES
      carrusel('cards1', 'indicadores1', 'ant1', 'sig1', columnas);
      carrusel('cards2', 'indicadores2', 'ant2', 'sig2', columnas);
      carrusel('cards3', 'indicadores3', 'ant3', 'sig3', columnas);


      // ESCUCHAR SI EL USUARIO CAMBIA DE TAMAÑO LA VISTA
      window.addEventListener("resize", function(event) {
        console.log(document.body.clientWidth);
        ancho = document.body.clientWidth;
        
        // ASIGANAR EL NUMERO DE COLUMNAS A UNA VARIABLE TEMPORAL
        let columnas_tmp = columnas;

        if(ancho > 1200) columnas = 3
        else if(ancho >= 801 && ancho <= 1200) columnas = 2
        else if(ancho < 801) columnas = 1
        
        // SI HAY UN CAMBIO EN COLUMNAS, REFRESCAR LA PAGINA
        if(columnas != columnas_tmp) window.location.href = 'actuales.php'  
        

      })

    })


    // FUNCION PARA ABRIR EL MODAL
    const abrirModal = (id) => {
      console.log('hola')
      let ruta = 'modal.php?id=' + id;

      $.get(ruta, function (data) {
        $('#divModal').html(data);
        $('#modalAcciones').modal('show');
      });
    }   

  </script>
</body>
</html>