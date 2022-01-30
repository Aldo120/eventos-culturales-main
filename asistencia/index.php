<?php

  include_once("../php/conexion.php");

  $hora = date('H:i:s', time());
  //echo $hora;

  

  $eventos = $conexion->query(
    "SELECT *
     FROM $db.eventos
     WHERE fecha = '$fechahoy'
     AND (hora <= '$hora' AND hora_fin >= '$hora')"
  ) or die("Error al obtener los eventos del dia de hoy <br>" . mysqli_error($conexion));


    
  $tipo = '';
  $msj1 = '';
  $msj2 = '';

  if(isset($_GET['create'])){
    $tipo = 'create';
    $msj1 = 'Asistencia registrada con éxito.';
    $msj2 = '';
  }

  else if(isset($_GET['asistencia'])){
    $tipo = 'asistencia';
    $msj1 = 'Ya has registrado tú asistencia a este evento.';
    $msj2 = '';
  }

  else if(isset($_GET['existente'])){
    $tipo = 'existente';
    $msj1 = '';
    $msj2 = 'Ya existe un registro con este correo o #cuenta. ';
  }




?>

<!DOCTYPE html>
<html lang="es-MX">
<head>
  <!-- Metadatos básicos y codificación -->
  <!-- ================================================== -->
  <meta http-equiv="Content-Type" content="text/html; charset=utf8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


  <!-- Metadatos específicos para móvil -->
  <!-- ================================================== -->
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


  <!-- Metadatos para posicionamiento en buscadores -->
  <!-- ================================================== -->
  <meta name="author" content="Raymundo Antonio Flores D.">
  <meta name="author" content="Aldo Gallegos Gallegos">


  <!-- Iconos para página y dispositivos móviles -->
  <!-- ================================================== -->
  <link rel="icon" href="../img/favicon2.ico" type="image/x-icon" />
  <meta name="msapplication-TileImage" content="../img/favicon2.ico" />
  <meta name="msapplication-TileColor" content="#488199" />
  

  <!-- BOOTSTRAP -->
  <!-- ================================================== -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css">


  <!-- FONTAWESOME -->
  <!-- ================================================== -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">


  <!-- Latest compiled and minified JavaScript -->
  <!-- ================================================== -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  
  <!-- Libreria GSAP para movimientos -->
  <!-- ================================================== -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.0/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.0/ScrollTrigger.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.1/ScrollToPlugin.min.js"></script>


  <!-- FUENTES -->
  <!-- ================================================== -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=News+Cycle&family=Staatliches&display=swap" rel="stylesheet">


  <!-- Estilos CSS -->
  <!-- ================================================== -->
  <link rel="stylesheet" type="text/css" href="../css/estilos-asistencia.css" />

  <!-- Titulo de la pagina -->
  <!-- ================================================== -->
  <title>Registro de Asistencia</title>
</head>
<body>

  <header class="header">
    <div class="header-content">
      <h1 class="title">UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO</h1>
      <h1 class="title">FACULTAD DE ESTUDIOS SUPERIORES ARAGÓN</h1>
      <h1 class="title">UNIDAD DE EXTENSIÓN UNIVERSITARIA</h1>
      <h1 class="title">DEPARTAMENTO DE ACTIVIDADES CULTURALES</h1>
    </div>
  </header>

  <main class="container">  
    <form action="../admin/asistencia1.php" method="post" id="form">

        <div class="secciones">
          <section class="container-seccion seccion1">   
              <div class="form-part">
                <h2 class="subtitle">Registro de Asistencia a Eventos Culturales</h2>
    
                <table>
                  <tr>
                    <th><label for="evento">Seleccione Evento</label></th>
                    <td>
                      <select name="evento" 
                              id="evento"
                              class="form-control"
                              title="Seleccione evento al que Asistió:">
                        <option value="">Seleccione Evento</option>
                    <?php
                        while($e = $eventos->fetch_assoc()){
                          $id_evento = $e['id'];
                          $nombre = $e['nombre'];

                          echo "<option value='$id_evento'>$nombre</option>";
                        }
                    ?>
                      </select>
                    </td>
                  </tr>
    
                  <tr>
                    <th>¿Has Registrado Asistencia Para Otro Evento?</th>
                    <td>
                      <label for="no">NO</label>
                      <input type="radio" 
                             name="historial" 
                            id="no" 
                            value="NO"
                            checked>
                    
                      <label for="si">SÍ</label>
                      <input type="radio" 
                             name="historial" 
                            id="si" 
                            value="SI">
                    </td>
                  </tr>
    
                  <tr>
                    <th><label for="comunidad">Selecciona Comunidad A La Que Perteneces</label></th>
                    <td>
                      <select name="comunidad" 
                              id="comunidad"
                              class="form-control"
                              title="Seleccione evento al que Asistió">
                        <option value="">Seleccione Comunidad</option>
                        <option value="1">Alumno(a)</option>
                        <option value="2">Académico(a)</option>
                        <option value="3">Ex-Alumno(a)</option>
                        <option value="4">Trabajador(a)</option>
                        <option value="5">Público Externo</option>
                      </select>
                    </td>
                  </tr>
    
                  
                  <tr id="tr_filtro">
                    <th><label for="valor_filtro">Ingrese Su Número De Cuenta ó Correo Electrónico</label></th>
                    <td>
                      <input type="text" 
                             name="valor_filtro" 
                             id="valor_filtro"
                             class="form-control"
                             title="# Cuenta o correo electrónico"
                             placeholder="# Cuenta o correo electrónico">
                    </td>
                  </tr>
    
                                
                  <tr id="num_cuenta">
                    <th><label for="numCuenta">Ingrese Su Número De Cuenta (Sin Guiones)</label></th>
                    <td>
                      <input type="number" 
                             name="numCuenta" 
                             id="numCuenta"
                             class="form-control"
                             title="Ingrese Su Número De Cuenta"
                             placeholder="Ingrese Su Número De Cuenta">
                    </td>
                  </tr>
                </table>
              </div>         
          </section>


          <!-- SECCION 2 DEL FORMULARIO -->
          <section class="container-seccion seccion2">            
            <div class="form-part">
              <h2 class="subtitle">Ingrese Sus Datos</h2>
  
              <table>
                <tr>
                  <th><label for="nombre">Nombre(s)</label></th>
                  <td>
                    <input name="nombre" 
                            id="nombre"
                            class="form-control required"
                            title="Ingrese Nombre(s)"
                            placeholder="Ingrese Nombre(s)">
                  </td>
                </tr>
  
                <tr>
                  <th><label for="ap_paterno">Apellido Paterno</label></th>
                  <td>
                    <input type="text" 
                           name="ap_paterno" 
                           id="ap_paterno"
                           class="form-control required"
                           title="Ingrese Apellido Paterno "
                           placeholder="Ingrese Apellido Paterno">
                  </td>
                </tr>


                <tr>
                  <th><label for="ap_materno">Apellido Materno</label></th>
                  <td>
                    <input type="text" 
                           name="ap_materno" 
                           id="ap_materno"
                           class="form-control required"
                           title="Ingrese Apellido Materno"
                           placeholder="Ingrese Apellido Materno">
                  </td>
                </tr>


                <tr>
                  <th><label for="email">Correo Electrónico</label></th>
                  <td>
                    <input type="email" 
                           class="form-control required"
                           name="email" 
                           id="email"
                           title="Ingrese Correo Electrónico"
                           placeholder="Ingrese Correo Electrónico">
                  </td>
                </tr>

                <tr>
                  <th><label for="sexo">Sexo</label></th>
                  <td>
                    <select 
                           class="form-control required"
                           name="sexo" 
                           id="sexo"
                           title="Ingrese Correo Electrónico">
                      <option value="">Selecciona Sexo</option>
                      <option value="2">Femenino</option>
                      <option value="1">Masculino </option>
                      <option value="4">Intersex</option>
                      <option value="3">Prefiero no decirlo</option>
                    </select>
                  </td>
                </tr>

                <tr id="tr_carrera">
                  <th><label for="carrera">Carrera que Impartes</label></th>
                  <td>
                    <input type="text" 
                           class="form-control"
                           name="carrera" 
                           id="carrera"
                           title="Carrera que Impartes"
                           placeholder="Carrera que Impartes">
                  </td>
                </tr>

                <tr id="tr_ex_cuenta">
                  <th><label for="ex_cuenta">Número de Cuenta</label></th>
                  <td>
                    <input type="text" 
                           class="form-control"
                           name="ex_cuenta" 
                           id="ex_cuenta"
                           title="Número de Cuenta"
                           placeholder="Número de Cuenta">
                  </td>
                </tr>

                <tr id="tr_descr">
                  <th><label for="descr">Descripción</label></th>
                  <td>
                    <input type="text" 
                           class="form-control"
                           name="descr" 
                           id="descr"
                           title="Ingrese una Descripción de sus Actividades"
                           placeholder="Ingrese una Descripción de sus Actividades">
                  </td>
                </tr>
  

              </table>
            </div>         
          </section>
        </div>    
    </form>

    <div class="botones">
      <button class="btn btn-primary registrar">Registrar Asistencia</button>
      <button class="btn btn-primary anterior">Regresar</button>
      <button class="btn btn-primary siguiente">Siguiente</button>
    </div>

  </main>

  <div id="divModal"></div>
 

  <script src="../js/alerta-error.js"></script>
  <script>
    // Alerta
    let tipo = '<?php echo $tipo ?>',
        msj1 = '<?php echo $msj1 ?>',
        msj2 = '<?php echo $msj2 ?>';

    if(tipo != ''){
      Alerta(msj1, msj2, tipo);
    }



    // FUNCION PARA CREAR ANIMACIÓN DE SALIDA DE LOS SECCION
    const tl = gsap.timeline(),
          $siguiente = document.querySelector('.siguiente'),
          $anterior = document.querySelector('.anterior'),
          $registrar = document.querySelector('.registrar');

    const $evento = document.getElementById('evento'),
          $historial = document.querySelector("input[name = historial]"),
          $historialSi = document.getElementById("si"),
          $historialNo = document.getElementById("no"),
          $comunidad = document.getElementById("comunidad"),
          $trFiltro = document.getElementById("tr_filtro"),
          $numCuenta = document.getElementById("num_cuenta"),
          $inputNumCuenta = document.getElementById("numCuenta"),
          $valorFiltro = document.getElementById("valor_filtro"),
          
          // VARIABLES DE SEGUNDA SECCION 
          $nombre = document.getElementById("nombre"),
          $apPaterno = document.getElementById("apPaterno"),
          $apMaterno = document.getElementById("ap_materno"),
          $correo = document.getElementById("correo"),
          $carrera = document.getElementById("carrera"),
          $exCuenta = document.getElementById("ex_cuenta"),
          $descr = document.getElementById("descr"),
          // TRS DE SEGUNDA SECCION
          $trCarrera = document.getElementById("tr_carrera"),
          $trExCuenta = document.getElementById("tr_ex_cuenta"),
          $trDescr = document.getElementById("tr_descr"),
          // CAMPOS REQUERIDOS DE SECCION 2
          $requeridos = document.querySelectorAll(".required");


    // FUNCIONAMIENTO DE FORMULARIO 
    document.addEventListener("DOMContentLoaded", () => {
      // OCULTAR COMPONENTES DE PRIMERA SECCION
      $trFiltro.style.display = "none"
      $numCuenta.style.display = "none"
      $siguiente.style.display = "none"
      $anterior.style.display = "none"
      $registrar.style.display = "none"

      // OCULTAR COMPONENTES DE SEGUNDA SECCION
      $trCarrera.style.display = "none"
      $trExCuenta.style.display = "none"
      $trDescr.style.display = "none"
    })
          

    const salir = (seccion) => {
      tl.to((seccion), { duration: .5, x: 1800, ease: 'expo'});
    }

    const entrar = (seccion) => {
      tl.to((seccion), { duration: .5, x: 0, ease: 'expo'});
    }

    // VALIDAR QUE EVENTO NO SEA VACIO
    $evento.onchange = () => {
        if($evento.value == '') $evento.style.border = "1px solid red"
        else $evento.style.border = "1px solid #cccccc"
    }

    // CUANDO DEN CLICK A SIGUIENTE
    $siguiente.onclick = () => {
      // VALIDAR QUE EL EVENTO NO SEA VACIO
      if($evento.value == ''){
        $evento.style.border = "1px solid red"
        Alerta1("Para continuar, seleccione un evento", "error");
      } 
      
      // CAMBIAR SECCIONES
      else {
        salir(".seccion1")
        entrar(".seccion2")
        $anterior.style.display = "block"
        $siguiente.style.display = "none"
        $registrar.style.display = "block"
      }
    }

    // CUANDO DEN CLICK A BOTON REGRESAR
    $anterior.onclick = () => {
      salir(".seccion2")
      entrar(".seccion1")
      $anterior.style.display = "none"
      $siguiente.style.display = "block"
      $registrar.style.display = "none"
    }

    //CUANDO DEN CLICK A REGISTRAR ASISTENCIA
    $registrar.onclick = () => {
      let historial = document.querySelector("input[name = historial]:checked").value,
          vacios = 0,
          evento = document.getElementById("evento").value, campoExtra = "";

      // VALIDAR SECCION 2
      if($anterior.style.display == "block"){
        $requeridos.forEach(el => {
          if(el.value == ""){
            el.style.border = "1px solid red";
            vacios++
            
          } else {
            el.style.border = "1px solid #cccccc"
          }
        })
        
        if($comunidad.value == '2' && $carrera.value == '') {
          vacios++
          $carrera.style.border = "1px solid red";
        } else {
          $carrera.style.border = "1px solid #cccccc"
        }
        
        if($comunidad.value == '3' && $exCuenta.value == '') {
          vacios++
          $exCuenta.style.border = "1px solid red";
        } else {
          $exCuenta.style.border = "1px solid #cccccc"
        }
        
        if($comunidad.value == '5' && $descr.value == '') {
          vacios++
          $descr.style.border = "1px solid red";
        } else {
          $descr.style.border = "1px solid #cccccc"
        }

        console.log(vacios);
        if(vacios > 0) Alerta1("Revise Que Ningún Campo Este Vacío", "error")
        else document.getElementById("form").submit()
        
      }

      // SI ESTAN EN LA PRIMERA SECCION 
      else {

        campoExtra = $inputNumCuenta.value
        // EN CASO QUE NO HAYA REGISTRADO ANTES Y SEA ALUMNO
        if(historial == 'NO' && $comunidad.value == '1'){
          if(evento == ''){
            vacios++
            $evento.style.border = "1px solid red"
          } else $evento.style.border = "1px solid #cccccc"
  
          if($inputNumCuenta.value == '') {
            vacios++
            $inputNumCuenta.style.border = "1px solid red"
          } else {
            // document.getElementById("form").submit()
            $inputNumCuenta.style.border = "1px solid #cccccc"
          }
        }
  

        // EN CASO DE QUE SI SE HA REGISTRADO ANTES
        if(historial == 'SI'){
          campoExtra = $valorFiltro.value
  
          if(evento == ''){
            vacios++
            $evento.style.border = "1px solid red"
          } else $evento.style.border = "1px solid #cccccc"
  
          if($valorFiltro.value == '') {
            vacios++
            $valorFiltro.style.border = "1px solid red"
          } else {
            // document.getElementById("form").submit()
            $valorFiltro.style.border = "1px solid #cccccc"
          }
        }

        if(vacios > 0) Alerta1("Revise Que Ningún Campo Este Vacío", "error")
        else {
          console.log(campoExtra);
          abrirModal(evento, historial, $comunidad.value, campoExtra)
        }
      }          
      //if(vacios > 0) Alerta1("Revise Que Ningún Campo Este Vacío", "error")
      //else document.getElementById("form").submit()
    }

    // FUNCION PARA ABRIR EL MODAL
    const abrirModal = (evento, historial, comunidad, campoExtra) => {
      let ruta = 'modal.php?evento=' + evento + '&historial=' + historial + '&comunidad=' + comunidad + '&campoExtra=' + campoExtra;

      $.get(ruta, function (data) {
        $('#divModal').html(data);
        $('#myModal').modal('show');
      });
    }   
    


    // SI DAN CLICK AL SI
    $historialSi.onclick = () => {
      let historial = document.querySelector("input[name = historial]:checked").value
      apagadores(historial)
    }

    // SI DAN CLICK AL NO
    $historialNo.onclick = () => {
      let historial = document.querySelector("input[name = historial]:checked").value
      apagadores(historial)
    }

    // SI CAMBIAN DE COMUNIDAD
    $comunidad.onchange = () => {
      let historial = document.querySelector("input[name = historial]:checked").value
      apagadores(historial)

      if(historial == 'NO'){
        if($comunidad.value == '2'){
          $trCarrera.style.display = "table-row"
          $trExCuenta.style.display = "none"
          $trDescr.style.display = "none"
          
        } else if($comunidad.value == '3'){
          $trCarrera.style.display = "none"
          $trExCuenta.style.display = "table-row"
          $trDescr.style.display = "none"

        } else if($comunidad.value == '4'){
          $trCarrera.style.display = "none"
          $trExCuenta.style.display = "none"
          $trDescr.style.display = "none"
          
        } else if($comunidad.value == '5'){
          $trCarrera.style.display = "none"
          $trExCuenta.style.display = "none"
          $trDescr.style.display = "table-row"
        }       
      }
    }

    // FUNCION PARA CONTROLAR BOTONES Y CAMPOS DISTINTOS
    const apagadores = (historial) => {
      if(historial == "NO" && $comunidad.value == "1"){
        $numCuenta.style.display = 'table-row'
        $trFiltro.style.display = 'none'
        $registrar.style.display = "block"
        $siguiente.style.display = "none"
        
      } else if(historial == "NO" && $comunidad.value != "" && $comunidad.value != '1'){
        $numCuenta.style.display = 'none'
        $registrar.style.display = "none"
        $siguiente.style.display = "block"
        $trFiltro.style.display = "none"

        
      } else if(historial == "SI" && $comunidad.value != ""){
        $numCuenta.style.display = 'none'
        $trFiltro.style.display = 'table-row'
        $registrar.style.display = "block"
        $siguiente.style.display = "none"
      }
    }
    
    
    
  </script>
  
</body>
</html>