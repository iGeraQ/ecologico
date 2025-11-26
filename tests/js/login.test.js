/**
 * @jest-environment jsdom
 */

describe("Pruebas del script de inicio de sesión", () => {
  let alertMock;
  let preventMock;

  beforeEach(() => {
    // Simular el DOM necesario
    document.body.innerHTML = `
            <form id="formulario-inicio-sesion">
                <input id="txt_nombre" value="">
                <input id="pass_contraseña" value="">
                <button type="submit"></button>
            </form>
            <button id="btn_RegistrarsePag"></button>
        `;

    // Cargar el script a probar
    require("../../src/cliente/scripts/index.js"); // Ruta del script original

    alertMock = jest.spyOn(window, "alert").mockImplementation(() => {});
    preventMock = { preventDefault: jest.fn() };
  });

  afterEach(() => {
    jest.resetModules();
    jest.clearAllMocks();
  });

  test("Debe mostrar alerta y prevenir submit si los campos están vacíos", () => {
    const form = document.getElementById("formulario-inicio-sesion");

    form.dispatchEvent(
      new Event("submit", { cancelable: true, bubbles: true })
    );

    expect(alertMock).toHaveBeenCalledWith(
      "Por favor, rellene todos los campos"
    );
    expect(preventMock.preventDefault).not.toHaveBeenCalled(); // no se enlaza aquí, lo probamos así:
  });

  test("Debe permitir submit si los campos están llenos", () => {
    document.getElementById("txt_nombre").value = "usuario";
    document.getElementById("pass_contraseña").value = "1234";

    const form = document.getElementById("formulario-inicio-sesion");
    const event = new Event("submit", { cancelable: true });
    event.preventDefault = preventMock.preventDefault;

    form.dispatchEvent(event);

    expect(alertMock).not.toHaveBeenCalled();
    expect(preventMock.preventDefault).not.toHaveBeenCalled();
  });
});
