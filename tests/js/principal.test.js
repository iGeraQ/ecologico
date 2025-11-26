/**
 * @jest-environment jsdom
 */

async function waitForElement(selector, timeout = 1000, interval = 20) {
  const start = Date.now();
  while (Date.now() - start < timeout) {
    const el = document.querySelector(selector);
    if (el) return el;
    // small delay
    await new Promise((r) => setTimeout(r, interval));
  }
  return null;
}

describe("Pruebas del carrusel de habitaciones (corregido)", () => {
  let cookiesCarritoMock;

  beforeEach(() => {
    jest.resetModules();

    // Mocks globales
    global.confirm = jest.fn(() => true);
    global.alert = jest.fn(() => {});

    // Mock del módulo cookiesCarrito
    jest.doMock("../../src/cliente/scripts/cookiesCarrito.js", () => ({
      cookiesCarrito: {
        eliminarTodasLasCookies: jest.fn(),
      },
    }));

    // DOM mínimo (sin tarjetas pre-creadas)
    document.body.innerHTML = `
      <button id="reserv"></button>
      <button id="prev"></button>
      <button id="next"></button>
      <div id="carrusel"></div>
      <button id="cerrarSesion"></button>
    `;

    // Mock fetch
    global.fetch = jest.fn();

    // Obtener mock del require del módulo simulado
    cookiesCarritoMock =
      require("../../src/cliente/scripts/cookiesCarrito.js").cookiesCarrito;

    // Forzar ejecución del listener DOMContentLoaded cuando el script lo registre
    jest.spyOn(document, "addEventListener").mockImplementation((event, cb) => {
      if (event === "DOMContentLoaded") {
        // ejecutar callback asincrónicamente para mantener orden similar al real
        setTimeout(cb, 0);
      }
    });
  });

  // ---------------------------------------------------------
  test("Click en tarjeta debe crear el contenedor modal", async () => {
    const fakeData = [
      {
        idhabitacion: 1,
        nombre: "Suite",
        descripcion: "Desc",
        costoPorNoche: 100,
        URLImagen: "a.jpg",
      },
    ];

    // fetch para cargar tarjetas
    fetch.mockResolvedValueOnce({
      json: () => Promise.resolve(fakeData),
    });

    // fetch para mostrarHabitacion cuando se haga click
    fetch.mockResolvedValueOnce({
      json: () => Promise.resolve(fakeData),
    });

    require("../../src/cliente/scripts/principal.js");

    // esperar que el script cree la tarjeta en el DOM
    const tarjeta = await waitForElement(".tarjeta-habitacion", 1500);
    expect(tarjeta).not.toBeNull(); // guard clause para errores más claros

    // ahora sí click
    tarjeta.click();

    // esperar que aparezca el modal creado por mostrarHabitacion
    const modal = await waitForElement(".contenedor", 1500);
    expect(modal).not.toBeNull();
  });

  // ---------------------------------------------------------
  test("Botón cerrar del modal debe remover el contenedor", async () => {
    const fakeData = [
      {
        idhabitacion: 1,
        nombre: "Suite",
        descripcion: "Desc",
        costoPorNoche: 100,
        URLImagen: "a.jpg",
      },
    ];

    fetch.mockResolvedValueOnce({ json: () => Promise.resolve(fakeData) });
    fetch.mockResolvedValueOnce({ json: () => Promise.resolve(fakeData) });

    require("../../src/cliente/scripts/principal.js");

    const tarjeta = await waitForElement(".tarjeta-habitacion", 1500);
    expect(tarjeta).not.toBeNull();

    tarjeta.click();

    const cerrarBtn = await waitForElement("#btnCerrarInfo", 1500);
    expect(cerrarBtn).not.toBeNull();

    // click cerrar
    cerrarBtn.click();

    // esperar un tick para que el handler remueva el contenedor
    await new Promise((r) => setTimeout(r, 0));
    expect(document.querySelector(".contenedor")).toBeNull();
  });

  // ---------------------------------------------------------
  test("Cerrar sesión debe llamar cookiesCarrito.eliminarTodasLasCookies()", async () => {
    fetch.mockResolvedValueOnce({ json: () => Promise.resolve([]) });

    require("../../src/cliente/scripts/principal.js");

    // esperar que el listener se haya registrado (listener ejecutado tras DOMContentLoaded mock)
    await new Promise((r) => setTimeout(r, 0));

    const btn = document.getElementById("cerrarSesion");
    expect(btn).not.toBeNull();

    btn.click();

    expect(cookiesCarritoMock.eliminarTodasLasCookies).toHaveBeenCalled();
  });

  // ---------------------------------------------------------
  test("Botones prev y next deben modificar transform del carrusel", async () => {
    const fakeData = [
      {
        idhabitacion: 1,
        nombre: "Suite",
        descripcion: "Desc",
        costoPorNoche: 100,
        URLImagen: "a.jpg",
      },
    ];

    fetch.mockResolvedValueOnce({
      json: () => Promise.resolve(fakeData),
    });

    require("../../src/cliente/scripts/principal.js");

    // esperar que la tarjeta exista y que el listener de inicializarCarrusel se haya añadido
    const tarjeta = await waitForElement(".tarjeta-habitacion", 1500);
    expect(tarjeta).not.toBeNull();

    // Mockear dimensiones necesarias
    Object.defineProperty(tarjeta, "offsetWidth", {
      value: 200,
      configurable: true,
    });
    const carrusel = document.getElementById("carrusel");
    expect(carrusel).not.toBeNull();

    Object.defineProperty(carrusel, "scrollWidth", {
      value: 1000,
      configurable: true,
    });
    // parentElement en jsdom será body; sobreescribimos parentElement con un objeto que tenga offsetWidth
    Object.defineProperty(carrusel, "parentElement", {
      value: { offsetWidth: 500 },
      configurable: true,
    });

    // Esperar un tick para que los listeners de prev/next estén registrados
    await new Promise((r) => setTimeout(r, 0));

    const next = document.getElementById("next");
    const prev = document.getElementById("prev");
    expect(next).not.toBeNull();
    expect(prev).not.toBeNull();

    // ejecutar clicks
    next.click();
    // permitir microtask que actualiza style
    await new Promise((r) => setTimeout(r, 0));
    expect(carrusel.style.transform).not.toBe("");

    prev.click();
    await new Promise((r) => setTimeout(r, 0));
    expect(carrusel.style.transform).not.toBe("");
  });
});
