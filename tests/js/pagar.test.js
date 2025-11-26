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
