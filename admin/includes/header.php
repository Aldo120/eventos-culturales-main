<?php

    $usuario = $_SESSION['usuario'];

    $query = $conexion->query(
      "SELECT nombre
       FROM $db.usuarios
       WHERE usuario = '$usuario'"
    )or die("Error al obtener nombre de usuario <br>" . mysqli_error($conexion));

    $resultado = $query->fetch_assoc(); 
    $nombre = $resultado['nombre'];
?>
<!-- HEADER -->

<header>
  <div class="menu">

    <h1>Sistema de Medición de Espectadores</h1>
  </div>

  <div class="torres"> </div>

  <div class="datos">
    <div>
      <span>Sistema de Medición de Espectadores</span>
      <p>Administrador(a)</p>
      <p><?php echo $nombre ?></p>
      <p><?php echo $fechahoy ?>  </p>
    </div>

    <!--<div class="menu-right">
      <i class="fas fa-bars iconoMenu"></i>
    </div>-->
  </div>
</header>
<i class="fas fa-bars iconoMenu"></i>



<!-- MENU DEL HEADER -->
<div class="side-bar">
  <div class="nav-menu">

  <div class="menu-header">
    <img src="../img/logo.png" class="img-responsive"alt="">
  </div>

  <div class="menu-body">
    <ul>
      <li class="uno">
        <a href="actuales.php">Eventos de Hoy</a>
      </li>
      <li class="dos">
        <a href="eventos.php">Eventos</a>
      </li>
      <li class="tres">
        <a href="crear_evento.php">Crear Evento</a>
      </li>
      <li class="cuatro">
        <a href="perfil.php">Perfil</a>
      </li>
      <li class="cinco">
        <a href="../php/logout.php">Cerrar Sesión</a>
      </li>
    </ul>
  </div>

  </div>
    
  <!-- Seccion trasparente para cerrar sidebar -->
  <div class="transparent">
    <div class="icono-cerrar">
      <i class="fas fa-chevron-left" 
        id="iconoCerrar"></i>
    </div>
  </div>

</div>




<script>
  const $iconoMenu = document.querySelectorAll('.iconoMenu'),
        $iconoCerrar = document.querySelector('.transparent')

  let tl = gsap.timeline({})

  $iconoMenu.forEach(el => {
    el.addEventListener('click', () => {
      tl.to(('.side-bar'), { duration: 1.5, x: 0, opacity: 1, ease: 'expo' })
    })
  })

  $iconoCerrar.addEventListener('click', () => {
    tl.to(('.side-bar'), { duration: 2, x: -2000, opacity: 0, ease: 'expo' })
  })

</script>