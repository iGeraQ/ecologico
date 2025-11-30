// Propósito: Proveer funcionalidades para la administración de habitaciones
document.addEventListener("DOMContentLoaded", () => {
  cargarTodasHabitaciones();
});

document.getElementById("cerrarSesion").addEventListener("click", function () {
  window.location.href = "../../../index.php";
});

//Filtra por categorias de habitaciones
const contenedorCuartos = document.getElementById("seccion-habitaciones");
const listaCuartos = contenedorCuartos.querySelector("#lista-cuartos");
const selectCuarto = document.getElementById("Categorias-cuarto");
selectCuarto.addEventListener("change", () => {
  const selectCuarto = document.getElementById("Categorias-cuarto");
  const tipoSeleccionado = selectCuarto.value;
  if (tipoSeleccionado === "Todas") {
    cargarTodasHabitaciones();
    return;
  }
  console.log("Filtrando habitaciones");
  const argumentos = new URLSearchParams();
  argumentos.append("action", "filtrar");
  argumentos.append("categoria", tipoSeleccionado);
  fetch("../../servidor/habitacion.php", {
    method: "POST",
    body: argumentos,
  })
    .then((response) => response.text())
    .then((html) => {
      listaCuartos.innerHTML = html;
      console.log(html);
    })
    .catch((error) => console.error("Error al filtrar habitaciones:", error));
});

function cargarTodasHabitaciones() {
  const selectCuarto = document.getElementById("Categorias-cuarto");
  console.log(selecc_cuarto);
  const contenedorCuartos = document.getElementById("seccion-habitaciones");
  console.log("C1");
  if (selectCuarto && contenedorCuartos) {
    const listaCuartos = contenedorCuartos.querySelector("#lista-cuartos");
    argumentos = new URLSearchParams();
    argumentos.append("action", "listar");
    fetch("../../servidor/habitacion.php", {
      method: "POST",
      body: argumentos,
    })
      .then((response) => response.text())
      .then((html) => {
        listaCuartos.innerHTML = html;
        console.log("Insertando habitaciones desde la base de datos");
      })
      .catch((error) => console.error("Error al cargar habitaciones:", error));
  } else {
    console.log(
      "El elemento 'tipoCuarto' o 'contenedorCuartos' no se encontró."
    );
  }
}

function selecc_cuarto(element) {
  document.querySelectorAll(".habitacion-propiedades").forEach((card) => {
    card.classList.remove("selected");
  });
  element.classList.add("selected");
}

function dirigirAeditar(modo) {
  const HabElegida = document.querySelector(".habitacion-propiedades.selected");

  if (modo === "editar" && !HabElegida) {
    alert("Por favor, selecciona una habitación para editar.");
    return;
  }

  localStorage.setItem("ModoEdit", modo);

  if (modo === "editar" && HabElegida) {
    // Extrae la información de la habitación seleccionada en un objeto
    const HabInfo = {
      descripcion: "",
      codigo: HabElegida.querySelector("#codigo-habitacion").textContent,
      nombre: HabElegida.querySelector("#nombre-habitacion").textContent,
      categoria: HabElegida.querySelector("#categoria-habitacion").textContent,
      cantidad: HabElegida.querySelector("#total-habitaciones").textContent,
      cantidadDisponible: HabElegida.querySelector("#cantidad-disponible")
        .textContent,
      capacidadPersonas:
        HabElegida.querySelector("#cantidad-personas").textContent,
      costo: HabElegida.querySelector("#costo-habitacion")
        .textContent.replace("$", "")
        .trim(),
      imagen: HabElegida.querySelector("img").src,
    };

    localStorage.setItem("HabElegida", JSON.stringify(HabInfo));
  } else {
    localStorage.removeItem("HabElegida");
  }
  window.location.href = "edithabitacion.php";
}

function eliminarHab() {
  const HabElegida = document.querySelector(".habitacion-propiedades.selected");
  if (!HabElegida) {
    alert("Por favor, selecciona una habitación para eliminar.");
    return;
  }
  // Confirmación de eliminación (cambiar)
  const confirmacion = confirm(
    "¿Estás seguro de que deseas eliminar esta habitación?"
  );
  if (!confirmacion) return;
  // Obtener el nombre de la habitación seleccionada
  const nombreHabitacion =
    HabElegida.querySelector("#nombre-habitacion").textContent;
  const idhabitacion =
    HabElegida.querySelector("#codigo-habitacion").textContent;
  // Eliminar la habitación de la base de datos
  const argumentos = new URLSearchParams();
  argumentos.append("action", "eliminar");
  argumentos.append("id", idhabitacion);
  fetch("../../servidor/habitacion.php", {
    method: "POST",
    body: argumentos,
  })
    .then((response) => response.text())
    .then((text) => console.log(text))
    .catch((error) => console.error("Error al eliminar habitación:", error));

  // Obtener la lista de habitaciones de localStorage
  let listaHabitaciones =
    JSON.parse(localStorage.getItem("listaHabitaciones")) || [];
  // Filtrar la habitación a eliminar y actualizar la lista en localStorage
  listaHabitaciones = listaHabitaciones.filter(
    (habitacion) => habitacion.nombre !== nombreHabitacion
  );
  localStorage.setItem("listaHabitaciones", JSON.stringify(listaHabitaciones));
  // Remover la habitación del DOM
  //HabElegida.remove();

  alert(`La habitación "${nombreHabitacion}" ha sido eliminada.`);
  window.location.reload();
}
