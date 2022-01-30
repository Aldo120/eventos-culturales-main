<?php

  // Iniciar sesion
  session_start();

  if(!isset($_SESSION) || !$_SESSION || $_SESSION['administrador'] == ''){
    header("Location: ../index.php");
    die;
  }

  include_once('../php/conexion.php');

  //var_dump($_SESSION);

  $tipo = '';
  $msj1 = '';
  $msj2 = '';

  if(isset($_GET['edit'])){
    $tipo = 'edit';
    $msj1 = 'Datos de usuario editados con éxito.';
    $msj2 = 'Error al editar datos de usuario.';
  }

  $usuario = $_SESSION['usuario'];

  $consulta = $conexion -> query(
    "SELECT A.*,
            B.usuario, 
            B.pwd
            
     FROM $db.administradores A

     JOIN $db.usuarios B 
     ON A.id = B.administrador
     WHERE B.usuario = '$usuario'"
  ) or die("Error al obtener datos del usuario <br>" . mysqli_error($conexion));

  $resultado = $consulta->fetch_assoc();
  $nombreBase = $resultado['nombre'];
  $ap_paterno = $resultado['ap_paterno'];
  $ap_materno = $resultado['ap_materno'];
  $pwd = $resultado['pwd'];

  
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
  <title>Perfil</title>

</head>
<body>

  <?php include("includes/header.php") ?>

  <img src="../img/torres.jpg" alt="Torres Fes" class="marcaAgua">

  <main>
    <h2>Información del usuario: <?php echo $usuario ?></h2>

    <form action="perfil1.php" method="POST" id="form">
      <input type="hidden"
             name="accion"
             value="1">
      <table class="tabla-formulario">

        <colgroup>
          <col style="width: 30%">
          <col style="width: 70%">
        </colgroup>

        <tbody>
          <tr>
            <th><label for="nombre">Nombre*:</label></th>
            <td>
              <input type="text"
                     name="nombre"
                     id="nombre"
                     class="form-control required"
                     title="Editar nombre de usuario"
                     placeholder="Editar nombre de usuario"
                     value="<?php echo $nombreBase ?>"
                     autofocus
                     required>
            </td>
          </tr>

          <tr>
            <th><label for="ap_paterno">Apellido Paterno*:</label></th>
            <td>
              <input type="text"
                     name="ap_paterno"
                     id="ap_paterno"
                     class="form-control required"
                     title="Editar Apellido Paterno"
                     placeholder="Editar Apellido Paterno"
                     value="<?php echo $ap_paterno ?>"
                     required>
            </td>
          </tr>

          <tr>
            <th><label for="ap_materno">Apellido Materno*:</label></th>
            <td>
              <input type="text"
                     name="ap_materno"
                     id="ap_materno"
                     class="form-control required"
                     title="Editar Apellido materno"
                     placeholder="Editar Apellido materno"
                     value="<?php echo $ap_materno ?>"
                     required>
            </td>
          </tr>

          <tr>
            <th><label for="pwd">Ingrese Contraseña Actual*: <i class="fas fa-eye"></i></label></th>
            <td>
              <input type="password"
                     name="pwd"
                     id="pwd"
                     class="form-control required"
                     title="Contraseña Actual"
                     placeholder="Contraseña Actual"
                     required>
            </td>
          </tr>
          
          <tr>
            <th><label for="pwd2">Ingrese Contraseña Nueva: <i class="fas fa-eye"></i></label></th>
            <td>
              <input type="password"
                     name="pwd2"
                     id="pwd2"
                     class="form-control"
                     title="Contraseña Nueva"
                     placeholder="Contraseña Nueva">
            </td>
          </tr>
          

        </tbody>
      </table>

      <div class="submit">
        <button id="submitForm" 
                class="btn btn-info"
                onclick="validarFormulario(event)">Editar Datos de Perfil</button>
        <!--<input type="submit" 
               class="btn btn-info"
               value="Editar Datos de Perfil">-->
      </div>
    </form>
  </main>
  

  <?php include 'includes/footer.html'?>

  <script src="../js/alerta-error.js"></script>
  <script>
    // ALERTA
    let tipo = "<?php echo $tipo ?>",
        msj1 = "<?php echo $msj1 ?>",
        msj2 = "<?php echo $msj2 ?>";

    if(tipo != ""){
      Alerta(msj1,msj2,tipo);
    }

    // Funcion para marcar menu
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelector(".cuatro").classList.add("active");
    })

    const d = document,
          $ojos = d.querySelectorAll('.fa-eye'),
          $inputs = d.querySelectorAll('.required'),
          $pwd2 = d.getElementById('pwd2'),
          pwdActual = "<?php echo $pwd ?>";

    // FUNCION PARA DETECTAR CAMPOS VACIOS Y PONERLOS EN ROJO
    $inputs.forEach(input => {
      input.addEventListener('change', () => {
        if(input.value == ''){
          input.style.border = '1px solid red';
        } else{ input.style.border = '1px solid #cccccc';}
      })
    })    

    
    //FUNCIONALIDAD A ICONOS PARA MOSTRAR/OCULTAR CONTRASEÑA
    $ojos.forEach(el => {
      el.onclick = () => {
        let padre = el.parentNode.parentNode.parentNode,
            input = padre.querySelector('input')

        if(input.type == "password") input.type = 'text'
        else input.type = 'password'
      }
    })

    // VALIDAR FORMULARIO
    const validarFormulario = e => {
      e.preventDefault()
      
      let vacios = 0

      $inputs.forEach(el => {
        if(el.value == '') vacios++
      })
      
      let $pwd = d.getElementById('pwd')
      
      if(vacios == 1) Alerta1(`No se puede continuar, hay ${vacios} campo vacío`, 'error')
      else if (vacios > 1) Alerta1(`No se puede continuar, hay ${vacios} campos vacíos`, 'error')
      // SI TODOS LOS CAMPOS ESTAN LLENOS
      else if(vacios == 0){
        // VERIFICAR QUE LA CONTRASEÑA ACTUAL SEA CORRECTA
        if($pwd.value != pwdActual) Alerta1("Contraseña Incorrecta", 'error')

        // SI ES CORRECTA
        else { 
          if($pwd2.value != '' && $pwd2.value.length < 8) Alerta1("La contraseña nueva debe tener mínimo 8 caractéres", 'error')
          else { 
            d.getElementById('form').submit();
          }
        } 
      }
    }
          
    
  </script>
</body>
</html>