document.addEventListener("DOMContentLoaded", () => {
  const modo = localStorage.getItem("ModoEdit");
  const HabInfo = JSON.parse(localStorage.getItem("HabElegida"));
  const carrusel = document.getElementsByClassName("swiper-wrapper");
  const categoriaElemento = document.getElementById("txt_categoria");
  console.log("Antes del if  (modo === 'editar' && HabInfo)");

  if (modo === "editar" && HabInfo) {
    console.log("Estoy en if (modo === 'editar' && HabInfo) ");
    //Recupera la descripcion de la habitacion desde la base de datos
    obtenerDescripcion(HabInfo.codigo, HabInfo);

    // AREA DE PREVISUALIZACION
    document.getElementById("id_habitacion").innerHTML =
      "<b>Id: </b>" + HabInfo.codigo;
    document.getElementById("nombre_habitacion").textContent = HabInfo.nombre;
    document.getElementById("categoria_habitacion").textContent =
      HabInfo.categoria;
    document.getElementById("info_habitaciones").innerHTML =
      "<b>Habitaciones: </b>" + HabInfo.cantidad;
    document.getElementById("info_disponibles").innerHTML =
      "<b>Habitaciones disponibles: </b>" + HabInfo.cantidadDisponible;
    document.getElementById("info_cap_personas").innerHTML =
      "<b>Cap. personas: </b>" + HabInfo.capacidadPersonas;
    document.getElementById("info_Costo").innerHTML =
      "<b>Costo: </b>" + HabInfo.costo;
    document.getElementById("info_descripcion").src = HabInfo.descripcion;
    document.getElementById("info_descripcion").textContent =
      HabInfo.descripcion;

    //AREA DEL EDITOR
    const imagenPrevisualizacion = document.getElementById("imagen-prev");
    document.getElementById("txt_nombre").value = HabInfo.nombre || "";
    document.getElementById("txt_categoria").value = HabInfo.categoria || "";
    document.getElementById("txt_habitaciones").value = HabInfo.cantidad || "";
    document.getElementById("txt_habdisponibles").value =
      HabInfo.cantidadDisponible || "";
    document.getElementById("txt_capacidad").value =
      HabInfo.capacidadPersonas || "";
    document.getElementById("txt_costo").value = HabInfo.costo || "";

    //Insertar imagenes del carrusel
    fetch("../../servidor/habitacion.php", {
      method: "POST",
      body: new URLSearchParams({
        action: "obtenerImagenes",
        id: HabInfo.codigo,
      }),
    })
      .then((respuesta) => respuesta.text())
      .then((imagenes) => {
        carrusel[0].innerHTML = imagenes;
        // Mostrar carrusel
        const swiper = new Swiper(".swiper", {
          loop: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
          allowTouchMove: true, // Permite el deslizamiento manual
        });
        let idImagenSeleccionada = null;
        // Función para actualizar la imagen seleccionada
        function actualizarImagenSeleccionada() {
          // Remover la clase 'seleccionada' de todas las slides
          const slides = document.querySelectorAll(".swiper-slide");
          slides.forEach((slide) => {
            slide.classList.remove("seleccionada");
          });

          // Obtener la slide activa
          const activeSlide = swiper.slides[swiper.activeIndex];
          if (activeSlide) {
            idImagenSeleccionada = activeSlide.getAttribute("data-idimagen");
            activeSlide.classList.add("seleccionada");
            console.log("Imagen seleccionada ID:", idImagenSeleccionada);
          }
        }

        // Inicializar la imagen seleccionada al cargar
        actualizarImagenSeleccionada();

        // Actualizar la imagen seleccionada cuando cambie el slide
        swiper.on("slideChange", function () {
          actualizarImagenSeleccionada();
        });

        // Manejar el botón de eliminación
        document
          .getElementById("btn_eliminar_imagen")
          .addEventListener("click", function () {
            if (idImagenSeleccionada) {
              // Confirmación antes de eliminar (opcional: usa SweetAlert o alert)
              const argumentos = new FormData();
              if (
                confirm("¿Estás seguro de que deseas eliminar esta imagen?")
              ) {
                // Obtener el idHabitacion desde el contexto (puedes pasar este valor desde PHP)
                argumentos.append("action", "eliminarImagen");
                argumentos.append("idImagen", idImagenSeleccionada);
                fetch("../../servidor/habitacion.php", {
                  method: "POST",
                  body: argumentos,
                })
                  .then((response) => response.text())
                  .then((data) => {
                    alert(data);
                    // Remover la imagen del carrusel
                    swiper.removeSlide(swiper.activeIndex);
                    // Resetear la selección
                    idImagenSeleccionada = null;
                    if (swiper.slides.length > 0) {
                      swiper.slideTo(
                        swiper.activeIndex === swiper.slides.length
                          ? swiper.slides.length - 1
                          : swiper.activeIndex
                      );
                      actualizarImagenSeleccionada();
                    } else {
                      alert("No hay más imágenes en el carrusel.");
                    }
                  })
                  .catch((error) => {});
              }
              aAdmin();
            } else {
              alert("Por favor, selecciona una imagen para eliminar.");
            }
          });

        const btnImagenPrederteminada = document.getElementById(
          "btn_predeterminada_imagen"
        );
        if (btnImagenPrederteminada) {
          btnImagenPrederteminada.addEventListener("click", function () {
            if (idImagenSeleccionada) {
              const argumentosPredeterminar = new FormData();
              argumentosPredeterminar.append("action", "predeterminarImagen");
              argumentosPredeterminar.append("idImagen", idImagenSeleccionada);
              fetch("../../servidor/habitacion.php", {
                method: "POST",
                body: argumentosPredeterminar,
              })
                .then((response) => response.text())
                .then((data) => {
                  alert("Imagen predeterminada actualizada.");
                  // Actualizar la imagen predeterminada
                  document.getElementById("imagen-prev").src =
                    document.querySelector(
                      ".swiper-slide.seleccionada img"
                    ).src;
                })
                .catch((error) => {});
            } else {
              alert(
                "Por favor, selecciona una imagen para establecer como predeterminada."
              );
            }
          });
        }
      });
  }

  if (modo === "añadir") {
    const id_habitacion = document.getElementById("id_habitacion");
    id_habitacion.style.display = "none";
    console.log("Añadir");
    //actualizar carrusel
    let imagenes = document.getElementById("btn_cambiarimg");
    imagenes.addEventListener("change", function () {
      const archivos = imagenes.files;
      if (archivos.length > 0) {
        for (let i = 0; i < archivos.length; i++) {
          agregarImagenAlCarrusel(archivos[i]);
        }
      }
    });
  }

  const btnGuardar = document.getElementById("btn_guardar");
  const btnCancelar = document.getElementById("btn_cancelar");

  if (btnGuardar) {
    console.log("Guardar");
    btnGuardar.addEventListener("click", async () => {
      event.preventDefault(); // Prevenir el comportamiento de recarga
      const Habs = parseInt(document.getElementById("txt_habitaciones").value);
      const HabsDisp = parseInt(
        document.getElementById("txt_habdisponibles").value
      );
      const Categoria = document.getElementById("txt_categoria").value;
      const Costo = document.getElementById("txt_costo").value;
      if (Habs < HabsDisp) {
        alert(
          "La cantidad de habitaciones disponibles es mayor a la cantidad de habitaciones totales"
        );
      } else if (!Habs || !HabsDisp || !Categoria || !Costo) {
        alert("Todos los campos deben tener valor");
      } else {
        console.log("Creando cambio");

        //Consulta para verificar si se cambio la imagen
        let archivos = document.getElementById("btn_cambiarimg").files;

        let idHabitacion = 0;
        if (modo === "añadir") {
          idHabitacion = await crearHabitacionVacia();
        } else {
          idHabitacion = HabInfo.codigo;
        }

        //let urlImagenes = [];
        if (archivos.length === 0 && modo === "añadir") {
          alert("Debes seleccionar al menos una imagen");
        } else {
          const formData = new FormData();
          for (let i = 0; i < archivos.length; i++) {
            formData.append("imagen" + i, archivos[i]);
          }

          actualizarImagenes(formData, idHabitacion);
        }

        const HabInformacionAEnviar = {
          nombre: document.getElementById("txt_nombre").value,
          categoria: document.getElementById("txt_categoria").value,
          descripcion: document.getElementById("t_area_descripcion").value,
          numHabitaciones: document.getElementById("txt_habitaciones").value,
          disponibles: document.getElementById("txt_habdisponibles").value,
          capacidadDePersonas: document.getElementById("txt_capacidad").value,
          costoPorNoche: document.getElementById("txt_costo").value,
        };
        /*
                    if(modo === 'añadir'){
                        console.log("Creando habitacion");
                        console.log(HabInformacionAEnviar);
                        crearHabitacionEnServidor(HabInformacionAEnviar);
                    }else{
                        actualizarHabitacionEnServidor(HabInformacionAEnviar, HabInfo.codigo);
                    }
                    */
        actualizarHabitacionEnServidor(HabInformacionAEnviar, idHabitacion);
      }
      alert("Cambios guardados");
      aAdmin();
    });
  }
  if (btnCancelar) {
    btnCancelar.addEventListener("click", () => {
      event.preventDefault(); // Prevenir el comportamiento de recarga
      console.log("Cancelar");
      localStorage.removeItem("ModoEdit");
      localStorage.removeItem("HabElegida");
      console.log("Btn cancelar");
      aAdmin();
    });
  }
});

function agregarImagenAlCarrusel(archivo) {
  const reader = new FileReader();

  reader.onload = function (e) {
    // Crear el elemento de imagen
    const img = document.createElement("img");
    img.src = e.target.result;
    img.alt = "Imagen de Habitación";
    img.style.width = "100%"; // Ajusta según tus necesidades

    // Crear el contenedor de la slide
    const divSlide = document.createElement("div");
    divSlide.classList.add("swiper-slide");
    divSlide.appendChild(img);

    const swiper = new Swiper(".swiper", {
      loop: true,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      allowTouchMove: true, // Permite el deslizamiento manual
    });

    const carrusel = document.querySelector(".swiper-wrapper");
    // Añadir la nueva slide a Swiper
    carrusel.appendChild(divSlide);
  };

  reader.readAsDataURL(archivo);
}

document.getElementById("cerrarSesion").addEventListener("click", function () {
  window.location.href = "../../../index.php";
});

async function crearHabitacionVacia() {
  try {
    const response = await fetch("../../servidor/habitacion.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded", // Ajusta según tu servidor
      },
      body: new URLSearchParams({
        action: "crearVacia",
      }),
    });

    if (!response.ok) {
      throw new Error("Error en la solicitud: " + response.statusText);
    }

    const codigo = await response.text(); // Obtener la respuesta como texto
    return codigo.trim(); // Eliminar posibles espacios en blanco
  } catch (error) {
    console.error("Error en crearHabitacionVacia:", error);
    throw error;
  }
}

function actualizarImagenes(formData, id) {
  formData.append("action", "actualizarImagenes");
  formData.append("id", id);
  fetch("../../servidor/habitacion.php", {
    method: "POST",
    body: formData,
  })
    .then((respuesta) => respuesta.text())
    .then((resultado) => {
      console.log(resultado);
    });
}

function actualizarHabitacionEnServidor(HabInformacionAEnviar, id) {
  // Idenfiticar los campos que sufrieron cambios
  console.log("Entrando a metodo para actualizar habitacion");
  const datosIniciales = JSON.parse(localStorage.getItem("HabElegida"));
  for (const key in HabInformacionAEnviar) {
    ``;
    console.log("Actualizando habitacion en servidor");
    const argumentos = new FormData();
    argumentos.append("action", "actualizar");
    argumentos.append("id", id);
    argumentos.append("campo", key);
    argumentos.append("valor", HabInformacionAEnviar[key]);
    fetch("../../servidor/habitacion.php", {
      method: "POST",
      body: argumentos,
    })
      .then((respuesta) => respuesta.text())
      .then((resultado) => {
        console.log(resultado);
      });
    console.log(argumentos);
  }
}

function crearHabitacionEnServidor(HabInformacionAEnviar) {
  const argumentos = new URLSearchParams();
  argumentos.append("action", "crear");
  Object.keys(HabInformacionAEnviar).forEach((key) =>
    argumentos.append(key, HabInformacionAEnviar[key])
  );
  fetch("../../servidor/habitacion.php", {
    method: "POST",
    body: argumentos,
  })
    .then((respuesta) => respuesta.text())
    .then((resultado) => {});
  console.log(argumentos);
}

function obtenerDescripcion(id, HabInfo) {
  const argumentos = new URLSearchParams();
  argumentos.append("action", "obtenerDescripcion");
  argumentos.append("id", id);
  fetch("../../servidor/habitacion.php", {
    method: "POST",
    body: argumentos,
  })
    .then((respuesta) => {
      if (!respuesta.ok) {
        throw new Error("La respuesta no fue bien recibida");
      }
      return respuesta.text();
    })
    .then((descripcion) => {
      // Guardar la descripción en HabInfo
      HabInfo.descripcion = descripcion;

      // Actualizar el localStorage con la nueva información
      localStorage.setItem("HabElegida", JSON.stringify(HabInfo));

      // Mostrar la descripción en el elemento correspondiente

      document.getElementById("t_area_descripcion").value = descripcion;
      document.getElementById("info_descripcion").value = descripcion;
    })
    .catch((error) => console.error("Error al enviar la petición:", error));
}

function mostrar(valor, id) {
  switch (id) {
    case "nombre_habitacion":
      document.getElementById(id).textContent = valor;
      break;
    case "categoria_habitacion":
      document.getElementById(id).textContent = valor;
      break;
    case "info_habitaciones":
      document.getElementById(id).innerHTML = "<b>Habitaciones: </b>" + valor;
      break;
    case "info_disponibles":
      document.getElementById(id).innerHTML =
        "<b>Habitaciones disponibles: </b>" + valor;
      break;
    case "info_descripcion":
      document.getElementById(id).textContent = valor;
      break;
    case "info_cap_personas":
      document.getElementById(id).innerHTML = "<b>Cap. personas: </b>" + valor;
      break;
    case "info_Costo":
      document.getElementById(id).innerHTML = "<b>Costo: </b>" + "$" + valor;
      break;
  }
}

function soloNumeros(input) {
  input.value = input.value.replace(/[^0-9]/g, ""); // Reemplaza cualquier cosa que no sea número
}

function aAdmin() {
  window.location.href = "adminpanel.php";
}

function PrevisualizarImagen(event) {
  const input = event.target;
  const file = input.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const imagen = document.getElementById("imagen-prev");
      //imagen.src = e.target.result;
      //imagen.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}
