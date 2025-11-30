import { cookiesCarrito } from "./cookiesCarrito.js";

document.getElementById("cerrarSesion").addEventListener("click", function () {
  window.location.href = "../../../index.php";
});

function anadirReservaAlCarrito() {
  const habitacion = document.getElementById("idhabitacion").value;
  cookiesCarrito.agregarHabitacionAlCarrito(habitacion);

  const entrada = document.getElementById("entrada").value;
  document.cookie = `entrada${habitacion}=${entrada}; expires=${cookiesCarrito.fechaExpiracionDefault()}; path=/`;

  const salida = document.getElementById("salida").value;
  document.cookie = `salida${habitacion}=${salida}; expires=${cookiesCarrito.fechaExpiracionDefault()}; path=/`;

  const numPersonas = document.getElementById("numPersonas").value;
  document.cookie = `habitacion${habitacion}=${numPersonas}; expires=${cookiesCarrito.fechaExpiracionDefault()}; path=/`;

  const total = document.getElementById("total").textContent;
  document.cookie = `total${habitacion}=${total}; expires=${cookiesCarrito.fechaExpiracionDefault()}; path=/`;
}

function verificar() {
  const entrada = document.getElementById("entrada").value;
  const salida = document.getElementById("salida").value;
  const numPersonas = parseInt(
    document.getElementById("numPersonas").value,
    10
  );
  const capacidad = parseInt(document.getElementById("capacidad").value, 10);

  let mensajeError = "";

  const fechaActual = new Date().toISOString().split("T")[0];

  if (!entrada) {
    mensajeError += "El día de entrada es obligatorio. <br>";
  } else if (entrada < fechaActual) {
    mensajeError +=
      "La fecha de entrada no puede ser anterior a la fecha actual. <br>";
  }

  if (!salida) {
    mensajeError += "El día de salida es obligatorio. <br>";
  } else if (entrada && salida && new Date(entrada) > new Date(salida)) {
    mensajeError +=
      "La fecha de salida no puede ser anterior a la fecha de entrada. <br>";
  }

  if (!numPersonas) {
    mensajeError += "ingrese una cantidad de personas";
  } else if (numPersonas > capacidad) {
    mensajeError += "la cantidad de personas supera la capacidad";
  }

  const mensajeDiv = document.getElementById("mensaje");

  if (mensajeError) {
    mensajeDiv.innerHTML = mensajeError;
    mensajeDiv.style.color = "red";
    return false;
  } else {
    mensajeDiv.innerHTML = "Formulario validado correctamente.";
    mensajeDiv.style.color = "green";
    return true;
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const elements = {
    entrada: document.getElementById("entrada"),
    salida: document.getElementById("salida"),
    numPersonas: document.getElementById("numPersonas"),
    costoPorNoche: document.getElementById("costoPorNoche").textContent,
    totalDiv: document.getElementById("total"), // Cambió de etiqueta a un div
  };

  elements.totalDiv.textContent = "0.00";

  const calcularTotal = () => {
    const fechaEntrada = new Date(elements.entrada.value);
    const fechaSalida = new Date(elements.salida.value);
    const personas = parseInt(elements.numPersonas.value) || 1;

    if (fechaEntrada && fechaSalida && fechaSalida > fechaEntrada) {
      const tiempoEstadia = Math.ceil(
        (fechaSalida - fechaEntrada) / (1000 * 60 * 60 * 24)
      ); // Días
      const total = tiempoEstadia * parseFloat(elements.costoPorNoche);
      elements.totalDiv.textContent = `${total.toFixed(2)}`; // Usa el div aquí
    } else {
      elements.totalDiv.textContent = "0.00";
    }
  };

  ["change", "input"].forEach((evento) => {
    elements.entrada.addEventListener(evento, calcularTotal);
    elements.salida.addEventListener(evento, calcularTotal);
    elements.numPersonas.addEventListener(evento, calcularTotal);
  });

  calcularTotal();

  document.querySelector("#btn_anadir").onclick = function () {
    if (verificar()) {
      // Solo si pasa la verificación
      anadirReservaAlCarrito();
      alert("reservacion agregada al carrito adecuadamente");
      window.location.replace("../view/pagar.php");
    }
  };
});

// Exportar funciones SOLO para pruebas
export { verificar, anadirReservaAlCarrito };
