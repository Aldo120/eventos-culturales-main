// OBTENER DATOS DE LA URL
const Alerta = (mensaje1, mensaje2, tipo) => {
  const valores = window.location.search
  // console.log(valores);
  
  
  if(valores !== ""){
    const urlParam = new URLSearchParams(valores)
    let estatus = urlParam.get(tipo)
  
    let alertaFlotante = document.createElement("div") // Crear el div para el error
    alertaFlotante.className = "alerta-flotante" // Agregar la clase error
    if(estatus == "true"){
      let icon = document.createElement("i"),
          textError = document.createTextNode(mensaje1) // Creamos el texto del error
          
      icon.className = "fas fa-check-circle ico-exito"
      alertaFlotante.appendChild(textError)
      alertaFlotante.appendChild(icon)
      alertaFlotante.classList.add('alerta-flotante-exito')
      // alertaFlotante.className = "alerta-flotante-exito" // Agregar la clase error
      
      
      
    } else if(estatus == 'false'){
      let icon = document.createElement("i"),
          textError = document.createTextNode(mensaje2) // Creamos el texto del error
      
      
      icon.className = "fas fa-exclamation-circle ico-error"
      // alertaFlotante.appendChild(textError)
      alertaFlotante.innerHTML = mensaje2
      alertaFlotante.appendChild(icon)
      // alertaFlotante.insertBefore(icon, textError)
      alertaFlotante.classList.add('alerta-flotante-error')
      // alertaFlotante.className = "error alert alert-danger" // Agregar la clase error
    } 
    
    document.body.appendChild(alertaFlotante)
  
    let tl = gsap.timeline({ repeat: 1, repeatDelay: 3, yoyo: true });
    tl.to(alertaFlotante, {
      duration: 1.5,
      x: 0,
      ease: 'expo'
    })
  }
}

const Alerta1 = (mensaje, tipo) => {
let alertaFlotante = document.createElement("div"), // Crear el div para el mensaje
    icon = document.createElement("i"),
    text = document.createTextNode(mensaje) // Crear mensaje de alerta
    
alertaFlotante.className = "alerta-flotante" 

if(tipo == 'error'){
  claseIcon = 'fas fa-exclamation-circle ico-error',
  claseAlerta = 'alerta-flotante-error'
} else {
  claseIcon = 'fas fa-exclamation-circle ico-exito',
  claseAlerta = 'alerta-flotante-exito'
}
    

icon.className = claseIcon
alertaFlotante.appendChild(text)
alertaFlotante.appendChild(icon)
alertaFlotante.classList.add(claseAlerta) // Agregar la clase error

document.body.appendChild(alertaFlotante)

let tl = gsap.timeline({ repeat: 1, repeatDelay: 3, yoyo: true });
tl.to(alertaFlotante, {
  duration: 1.5,
  x: 0,
  ease: 'expo'
})
}


const AlertaSencillo = (mensaje) => {
let alertaFlotante = document.createElement("div"), // Crear el div para el error
    icon = document.createElement("i"),
    textError = document.createTextNode(mensaje) // Creamos el texto del error

    icon.className = "fas fa-exclamation-circle ico-error"
    alertaFlotante.appendChild(textError)
    alertaFlotante.appendChild(icon)
    alertaFlotante.className = "error alert alert-danger" // Agregar la clase error

    document.body.appendChild(alertaFlotante)

    let tl = gsap.timeline({ repeat: 1, repeatDelay: 3, yoyo: true });
    tl.to(alertaFlotante, {
      duration: 1.5,
      x: 0,
      ease: 'expo'
    })
}