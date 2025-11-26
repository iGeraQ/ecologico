/**
 * @jest-environment jsdom
 */

const fs = require("fs");
const path = require("path");

// 1️⃣ Mock manual de cookiesCarrito
const mockCookiesCarrito = {
  agregarHabitacionAlCarrito: jest.fn(),
  fechaExpiracionDefault: jest.fn(() => "Thu, 31 Dec 2099 23:59:59 GMT"),
};

// Hacerlo global para que el script lo use
global.cookiesCarrito = mockCookiesCarrito;

describe("Test del archivo carrito.js sin módulos", () => {
  beforeEach(() => {
    // DOM mínimo
    document.body.innerHTML = `
      <input id="idhabitacion" value="101">
      <input id="entrada" value="2025-12-10">
      <input id="salida" value="2025-12-12">
      <input id="numPersonas" value="2">
      <input id="capacidad" value="4">
      <span id="costoPorNoche">100</span>
      <div id="total"></div>
      <div id="mensaje"></div>
      <button id="btn_anadir"></button>
      <button id="cerrarSesion"></button>
    `;

    // Evitar errores de window
    window.alert = jest.fn();
    window.confirm = jest.fn(() => true);
    delete window.location;
    window.location = { href: "", replace: jest.fn() };

    // Leer script original
    const filePath = path.resolve(
      __dirname,
      "../../src/cliente/scripts/carrito.js"
    );
    let scriptCode = fs.readFileSync(filePath, "utf8");

    // Limpiar imports y exports
    scriptCode = scriptCode
      .replace(/import\s+.*?from\s+["'][^"']+["'];?/g, "")
      .replace(/export\s+{([^}]+)};/g, (_, fns) =>
        fns
          .split(",")
          .map((fn) => `window.${fn.trim()} = ${fn.trim()};`)
          .join("\n")
      )
      .replace(/export\s+default\s+/g, "")
      .replace(/export\s+function\s+/g, "function ")
      .replace(/export\s+const\s+/g, "const ");

    // Ejecutar script
    new Function(scriptCode)();

    // Disparar DOMContentLoaded
    document.dispatchEvent(new Event("DOMContentLoaded"));
  });

  test("verificar() devuelve true cuando todo es válido", () => {
    document.getElementById("entrada").value = "2025-12-10";
    document.getElementById("salida").value = "2025-12-12";
    document.getElementById("numPersonas").value = "2";

    const resultado = window.verificar();

    expect(resultado).toBe(true);
    expect(document.getElementById("mensaje").textContent).toBe(
      "Formulario validado correctamente."
    );
  });

  test("verificar() detecta error cuando la salida < entrada", () => {
    document.getElementById("entrada").value = "2025-12-10";
    document.getElementById("salida").value = "2025-12-01";

    const resultado = window.verificar();

    expect(resultado).toBe(false);
    expect(document.getElementById("mensaje").innerHTML).toContain(
      "La fecha de salida no puede ser anterior a la fecha de entrada"
    );
  });

  test("anadirReservaAlCarrito() escribe cookies y llama a cookiesCarrito", () => {
    window.anadirReservaAlCarrito();

    expect(mockCookiesCarrito.agregarHabitacionAlCarrito).toHaveBeenCalledWith(
      "101"
    );

    expect(document.cookie).toContain("entrada101=");
    expect(document.cookie).toContain("salida101=");
    expect(document.cookie).toContain("habitacion101=");
    expect(document.cookie).toContain("total101=");
  });
});
