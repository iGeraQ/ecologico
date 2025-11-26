// javascript
// tests/js/setupTests.test.js
// Ejecuta el archivo que define el mock y expone global.localStorage
require("./setupTests.js");

describe("LocalStorageMock", () => {
  beforeEach(() => {
    // Asegurar estado limpio antes de cada prueba
    if (
      global.localStorage &&
      typeof global.localStorage.clear === "function"
    ) {
      global.localStorage.clear();
    }
  });

  test("getItem devuelve null para clave inexistente", () => {
    expect(global.localStorage.getItem("no-existe")).toBeNull();
  });

  test("setItem almacena valores y getItem devuelve el valor como string", () => {
    global.localStorage.setItem("numero", 42);
    expect(global.localStorage.getItem("numero")).toBe("42");

    global.localStorage.setItem("texto", "hola");
    expect(global.localStorage.getItem("texto")).toBe("hola");
  });

  test("removeItem elimina la clave y getItem devuelve null", () => {
    global.localStorage.setItem("clave", "valor");
    expect(global.localStorage.getItem("clave")).toBe("valor");

    global.localStorage.removeItem("clave");
    expect(global.localStorage.getItem("clave")).toBeNull();
  });

  test("clear elimina todas las entradas", () => {
    global.localStorage.setItem("a", "1");
    global.localStorage.setItem("b", "2");
    expect(global.localStorage.getItem("a")).toBe("1");
    expect(global.localStorage.getItem("b")).toBe("2");

    global.localStorage.clear();
    expect(global.localStorage.getItem("a")).toBeNull();
    expect(global.localStorage.getItem("b")).toBeNull();
  });

  test("setItem convierte objetos a string mediante String(value)", () => {
    global.localStorage.setItem("obj", { a: 1 });
    // String({a:1}) => "[object Object]"
    expect(global.localStorage.getItem("obj")).toBe("[object Object]");
  });

  test("API pública existe y son funciones", () => {
    expect(typeof global.localStorage.getItem).toBe("function");
    expect(typeof global.localStorage.setItem).toBe("function");
    expect(typeof global.localStorage.removeItem).toBe("function");
    expect(typeof global.localStorage.clear).toBe("function");
  });

  test("removeItem en clave inexistente no lanza excepción y mantiene otras claves", () => {
    global.localStorage.setItem("preserva", "sí");
    expect(() => global.localStorage.removeItem("no-existe")).not.toThrow();
    expect(global.localStorage.getItem("preserva")).toBe("sí");
  });
});
