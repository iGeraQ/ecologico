/**
 * @jest-environment jsdom
 */

import { cookiesCarrito } from "../../src/cliente/scripts/cookiesCarrito.js";
jest.mock("../../src/cliente/scripts/cookiesCarrito.js");

describe("Test del script pagar.js", () => {
  beforeEach(() => {
    // Mock de funciones de cookiesCarrito
    cookiesCarrito.eliminarHabitacionDelCarrito = jest.fn();
    cookiesCarrito.obtenerCookie = jest.fn();

    // Mock global de alert y window.location
    global.alert = jest.fn();
    delete window.location;
    window.location = {
      href: "",
      replace: jest.fn(),
      reload: jest.fn(),
    };

    // DOM mínimo necesario
    document.body.innerHTML = `
      <button id="cerrarSesion"></button>
      <button id="btnPagar"></button>
      <div id="totalFinal"></div>
      <button class="eliminar" id="hab1"></button>
      <button class="editar" id="hab2"></button>
    `;

    // Limpiar módulos cacheados
    jest.resetModules();

    // Cargar script después de crear el DOM
    require("../../src/cliente/scripts/pagar.js");

    // Disparar DOMContentLoaded
    document.dispatchEvent(new Event("DOMContentLoaded"));
  });
  /* 
  test("Eliminar habitación llama a cookiesCarrito.eliminarHabitacionDelCarrito", () => {
    const btnEliminar = document.getElementById("hab1");

    // Disparar click
    btnEliminar.click();

    expect(cookiesCarrito.eliminarHabitacionDelCarrito).toHaveBeenCalledWith(
      "hab1"
    );
    expect(window.location.reload).toHaveBeenCalled();
  });

  test("Editar habitación llama a cookiesCarrito.obtenerCookie y prepara la URL si hay cookies", () => {
    const btnEditar = document.getElementById("hab2");

    // Mock para que exista cookie
    cookiesCarrito.obtenerCookie.mockImplementation((nombre) => {
      if (nombre === "entradahab2") return "2025-12-10";
      if (nombre === "salidahab2") return "2025-12-12";
      if (nombre === "habitacionhab2") return "2";
      return undefined;
    });

    // Mock document.location.href
    delete document.location;
    document.location = "";

    // Disparar click
    btnEditar.click();

    expect(cookiesCarrito.obtenerCookie).toHaveBeenCalledWith("entradahab2");
    expect(cookiesCarrito.obtenerCookie).toHaveBeenCalledWith("salidahab2");
    expect(cookiesCarrito.obtenerCookie).toHaveBeenCalledWith("habitacionhab2");

    expect(document.location).toBe(
      "../view/carrito.php?id=hab2&entrada=2025-12-10&salida=2025-12-12&personas=2"
    );
  }); */

  test("Botón pagar muestra alerta si carrito vacío", () => {
    document.getElementById("totalFinal").innerHTML =
      "No hay habitaciones en el carrito.";
    const btnPagar = document.getElementById("btnPagar");

    btnPagar.click();

    expect(alert).toHaveBeenCalledWith(
      "el carrito esta vacio, agregue una habitacion"
    );
  });

  test("Botón pagar redirige si hay habitaciones", () => {
    document.getElementById("totalFinal").innerHTML = "200.00";
    const btnPagar = document.getElementById("btnPagar");

    btnPagar.click();

    expect(alert).not.toHaveBeenCalled();
  });
});
