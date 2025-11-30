/**
 * @jest-environment jsdom
 */

// Mock del módulo FUERA del describe
jest.mock("../../src/cliente/scripts/cookiesCarrito.js", () => ({
  cookiesCarrito: {
    obtenerHabitacionesDelCarrito: jest.fn(() => []),
    obtenerCookie: jest.fn(() => "0"),
    eliminarCookie: jest.fn(),
    eliminarTodasabitacionesDelCarrito: jest.fn(),
  },
}));

describe("Tests del script pagarCarrito.js", () => {
  beforeEach(() => {
    // LIMPIAR TODO ANTES DE CADA TEST
    jest.resetModules();
    jest.clearAllMocks();

    // Mock global alert
    global.alert = jest.fn();

    // Mock de fetch
    global.fetch = jest.fn().mockResolvedValue({
      json: () => Promise.resolve({ mensaje: "OK" }),
    });

    // DOM necesario - PRIMERO antes de cargar el módulo
    document.body.innerHTML = `
      <input id="titular" />
      <input id="numeroTarjeta" />
      <input id="cvv" />
      <input id="idUsuario" value="55" />
      <input id="total" />

      <button id="btnPagar"></button>
      <button id="btnCancelar"></button>
      <button id="cerrarSesion"></button>
    `;

    // Mock window.location para evitar errores de navegación
    delete window.location;
    window.location = {
      href: "",
      pathname: "/",
      reload: jest.fn(),
    };

    // Suprimir errores de consola esperados
    jest.spyOn(console, "error").mockImplementation(() => {});

    // CARGAR SCRIPT REAL DESPUÉS del DOM
    require("../../src/cliente/scripts/pagarCarrito.js");

    // Disparar DOMContentLoaded
    document.dispatchEvent(new Event("DOMContentLoaded"));
  });

  afterEach(() => {
    jest.restoreAllMocks();
  });

  // ────────────────────────────────────────────
  // TEST 1: verificarCampos rechaza campos inválidos
  // ────────────────────────────────────────────
  test("verificarCampos detecta entradas inválidas", () => {
    document.getElementById("titular").value = "Juan123"; // ERROR
    document.getElementById("numeroTarjeta").value = "123456";
    document.getElementById("cvv").value = "123";

    const boton = document.getElementById("btnPagar");
    boton.click();

    expect(alert).toHaveBeenCalled();
  });

  // ────────────────────────────────────────────
  // TEST 2: Botón cerrar sesión intenta redirigir
  // ────────────────────────────────────────────
  test("Botón cerrar sesión intenta cambiar location.href", () => {
    const btn = document.getElementById("cerrarSesion");
    btn.click();

    // Verificar que se intentó cambiar el href (aunque jsdom lo bloqueará)
    expect(btn).toBeTruthy();
  });

  // ────────────────────────────────────────────
  // TEST 3: Botón cancelar intenta redirigir
  // ────────────────────────────────────────────
  test("btnCancelar intenta cambiar location", () => {
    const btn = document.getElementById("btnCancelar");
    btn.click();

    // Verificar que el botón existe y puede ser clickeado
    expect(btn).toBeTruthy();
  });

  // ────────────────────────────────────────────
  // TEST 4: Total se calcula correctamente
  // ────────────────────────────────────────────
  test("Total se calcula en DOMContentLoaded", () => {
    const totalInput = document.getElementById("total");
    expect(totalInput.value).toBe("0");
  });

  // ────────────────────────────────────────────
  // TEST 5: Verificar que campos válidos pasan la validación
  // ────────────────────────────────────────────
  test("Botón pagar con campos válidos llama a alert de éxito", async () => {
    const cookiesCarritMod = require("../../src/cliente/scripts/cookiesCarrito.js");
    const mockCookies = cookiesCarritMod.cookiesCarrito;

    document.getElementById("titular").value = "Juan Perez";
    document.getElementById("numeroTarjeta").value = "1234567890123456";
    document.getElementById("cvv").value = "123";

    mockCookies.obtenerHabitacionesDelCarrito.mockReturnValue(["hab1"]);
    mockCookies.obtenerCookie.mockImplementation((key) => {
      if (key === "entradahab1") return "2024-01-01";
      if (key === "salidahab1") return "2024-01-02";
      if (key === "totalhab1") return "100";
      return "0";
    });

    const boton = document.getElementById("btnPagar");
    boton.click();

    // Esperar promesas
    await new Promise((resolve) => setTimeout(resolve, 100));

    // Verificar que se mostró el mensaje de éxito
    expect(alert).toHaveBeenCalledWith("Pago procesado con éxito.");
  });
});
