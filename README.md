# Proyecto de Reservaciones de Habitaciones

Este proyecto web permite a los usuarios administrar reservaciones de habitaciones, tanto como huéspedes como administradores. La aplicación proporciona autenticación de usuarios, administración de habitaciones y un carrito de reservaciones, además de una experiencia de usuario intuitiva y agradable.

## Tabla de Contenidos

- [Características del Proyecto](#características-del-proyecto)
- [Testing](#testing)
  - [Ejecución Local](#ejecución-local)
  - [Ejecución Remota (GitHub Actions)](#ejecución-remota-github-actions)
  - [Tests Disponibles](#tests-disponibles)
  - [Debugging de Tests](#debugging-de-tests)
- [Recursos](#recursos)
- [Autores](#autores)

## Características del Proyecto
Las caracteristicas del proyecto pueden ser consultadas desde el [documento de especificación de requerimientos](https://alumnosuady.sharepoint.com/:w:/s/tilinesdeldesarrolloweb/ES-yvQRnj8NEq8dEWSMvXn8BkppVsIAa0cdLKn-leFRdmg?e=9888fh)

### Asignación de Roles
- **Menú central, Main**: Luis Gerardo Méndez Villanueva, Luis Carlos Pacheco Ramírez, Carlos Julián Chan Ek
- **Iniciar sesión**: José Alberto Murcia Cen
- **Registrar usuario**: José Alberto Murcia Cen
- **Buscar**: Juan Emmanuel Poot Escamilla
- **Reservar (tiempo de estadía, número de personas)**: Breindel Varguez González
- **Pagar**: Breindel Varguez González
- **Gestión de habitaciones (añadir/modificar imágenes, editar descripción y precio)**: Luis Carlos Pacheco Ramírez

## Testing

El proyecto utiliza PHPUnit 10.5 para pruebas automatizadas, con un enfoque en testing unitario de los módulos del servidor.

### Ejecución Local

#### Prerrequisitos
- PHP 8.1 o superior
- XAMPP instalado (o equivalente con PHP en PATH)
- Composer instalado

#### Comandos Disponibles

**Ejecutar todos los tests:**
```bash
# Windows (XAMPP)
C:\xampp\php\php.exe vendor\bin\phpunit

# O usando el script incluido
test.bat

# Linux/Mac
./vendor/bin/phpunit
```

**Ejecutar un test específico:**
```bash
# Por archivo
C:\xampp\php\php.exe vendor\bin\phpunit tests\AutenticacionTest.php

# Por método específico
C:\xampp\php\php.exe vendor\bin\phpunit --filter testLoginExitoso tests\AutenticacionTest.php
```

**Ejecutar tests con cobertura (requiere Xdebug):**
```bash
C:\xampp\php\php.exe vendor\bin\phpunit --coverage-html coverage
```

**Ejecutar tests por suite:**
```bash
# Solo tests de autenticación
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Autenticacion

# Solo tests de habitaciones
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Habitaciones

# Solo tests de pagos
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Pagos
```

#### Configuración Local

El archivo `phpunit.xml` está configurado con las siguientes variables de entorno para testing:
- `APP_ENV=testing`
- `DB_HOST=localhost`
- `DB_NAME=ecologico_test`
- `DB_USER=root`
- `DB_PASS=""` (vacío)

### Ejecución Remota (GitHub Actions)

Los tests se ejecutan automáticamente en GitHub Actions en las siguientes situaciones:

#### Triggers Automáticos
- **Push a main**: Cada vez que se hace push a la rama main
- **Pull Request a main**: Cada vez que se abre o actualiza un PR hacia main

#### Pipeline de CI/CD

El workflow está definido en `.github/workflows/php.yml` y ejecuta:

1. **Setup del ambiente** (Ubuntu latest + PHP 8.2)
2. **Validación** de composer.json
3. **Cache** de dependencias de Composer
4. **Instalación** de dependencias
5. **Ejecución** de tests con PHPUnit

#### Revisar Resultados

**En GitHub:**
1. Ve a la pestaña "Actions" en el repositorio
2. Selecciona el workflow "PHP Composer"
3. Haz clic en la ejecución específica
4. Revisa los logs de cada step

**Estados posibles:**
- ✅ **Success**: Todos los tests pasaron
- ❌ **Failure**: Uno o más tests fallaron
- 🟡 **Pending**: Tests ejecutándose actualmente

### Tests Disponibles

#### 1. AutenticacionTest.php
Prueba el sistema de login y registro de usuarios:
- `testLoginExitoso()`: Login con credenciales válidas
- `testLoginFallido()`: Login con credenciales inválidas
- `testRegistroExitoso()`: Registro de nuevo usuario
- `testRegistroFallido()`: Registro con datos inválidos

#### 2. HabitacionesTest.php
Prueba la gestión de habitaciones:
- `testHabitacionListado()`: Listado de habitaciones
- `testHabitacionAgregacionExitosa()`: Crear habitación válida
- `testHabitacionAgregacionDatosInvalidos()`: Crear con datos inválidos
- `testHabitacionEdicionExitosa()`: Editar habitación
- `testHabitacionEliminacionExitosa()`: Eliminar habitación

#### 3. PagoTest.php
Prueba el sistema de pagos y reservaciones:
- `testPagoExitoso()`: Procesar pago válido
- `testPagoDatosInvalidos()`: Pago con datos inválidos
- `testReservacionCreacion()`: Crear reservación
- `testCalculoCosto()`: Cálculo de costos

### Debugging de Tests

#### En VS Code (Recomendado)

1. **Instalar extensión PHPUnit Test Explorer:**
   ```
   recca0120.vscode-phpunit
   ```

2. **Configurar debugging:**
   - Coloca breakpoints en el código
   - Ve a "Run and Debug" (Ctrl+Shift+D)
   - Selecciona "Debug Specific Test Method"
   - Presiona F5

#### Debugging Manual

**Agregar output de debug:**
```php
// En cualquier test
echo "DEBUG: Variable = " . print_r($variable, true) . "\n";
```

**Ejecutar con verbose:**
```bash
C:\xampp\php\php.exe vendor\bin\phpunit --testdox --verbose
```

**Ver solo failures:**
```bash
C:\xampp\php\php.exe vendor\bin\phpunit --stop-on-failure
```

#### Comandos de Composer

Los siguientes comandos están disponibles en `composer.json`:

```bash
# Ejecutar tests
composer test

# Ejecutar tests con cobertura
composer test-coverage

# Filtrar tests específicos
composer test-filter -- "nombreDelTest"
```

### Estructura de Tests

```
tests/
├── bootstrap.php          # Configuración inicial de tests
├── MockDatabase.php      # Mock de base de datos para testing
├── AutenticacionTest.php # Tests de login/registro
├── HabitacionesTest.php  # Tests de gestión de habitaciones
└── PagoTest.php         # Tests de pagos y reservaciones
```

### Solución de Problemas Comunes

**Error: "php command not found"**
- Asegúrate de que PHP esté en tu PATH o usa la ruta completa a php.exe

**Error: "Class not found"**
- Ejecuta: `composer dump-autoload`

**Tests fallan localmente pero pasan en CI:**
- Verifica las variables de entorno en phpunit.xml
- Asegúrate de tener la base de datos de test configurada

## Recursos
- **Diagrama de Entidades**: Describe la estructura de la base de datos utilizada en MySQL [Click aquí](https://lucid.app/lucidchart/b1e77903-ae3d-4159-9200-4000f52d392a/edit?viewport_loc=-3353%2C-1025%2C3706%2C1996%2C0_0&invitationId=inv_871b0493-43b9-42b4-af3f-5bea94f36336).
- **Prototipo en Figma**: Referencia visual y estructura de la interfaz de usuario [Click aquí](https://www.figma.com/design/xL1Ln9MMFodOJwZpDn5eih/Hotel-Web-site?node-id=0-1&m=dev&t=LuFiYaXrjoB5973E-1).

## Autores
- Luis Gerardo Méndez Villanueva
- José Alberto Murcia Cen
- Juan Emmanuel Poot Escamilla
- Breindel Varguez González
- Carlos Julián Chan Ek
- Luis Carlos Pacheco Ramírez
