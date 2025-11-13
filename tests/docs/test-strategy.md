# Estrategia de Testing - Módulo de Autenticación

## Objetivos

El objetivo principal es garantizar que todas las funcionalidades del módulo de autenticación funcionen correctamente y de manera segura.

## Alcance

### Funciones Cubiertas
- `iniciarSesion()` - Autenticación de usuarios
- `registrarUsuario()` - Registro de nuevos usuarios  
- `cerrarSesion()` - Cierre de sesión
- `validarSesionPantallaPrincipal()` - Validación para pantalla principal
- `validarSesionCliente()` - Validación de clientes
- `validarSesionAdministrador()` - Validación de administradores
- Controlador principal (`autenticacion.php`)

### Casos de Uso Cubiertos

#### Inicio de Sesión
- ✅ Usuario válido con credenciales correctas
- ✅ Usuario administrador
- ✅ Usuario inexistente
- ✅ Contraseña incorrecta
- ✅ Redirección según rol

#### Registro de Usuario
- ✅ Registro exitoso
- ✅ Asignación de rol por defecto
- ✅ Redirección post-registro

#### Validaciones de Sesión
- ✅ Sesión válida para cada tipo de usuario
- ✅ Sesión inválida o inexistente
- ✅ Usuario con rol incorrecto para la función
- ✅ Redirecciones de seguridad

#### Controlador
- ✅ Diferentes acciones POST
- ✅ Datos faltantes
- ✅ Acciones inválidas

## Metodología

### 1. Test-Driven Development (TDD)
- Escribir tests antes del código
- Red-Green-Refactor cycle
- Tests como especificación

### 2. Tipos de Tests

#### Unit Tests
- Funciones individuales aisladas
- Mocks para dependencias externas
- Enfoque en lógica de negocio

#### Integration Tests  
- Interacción entre componentes
- Base de datos real (opcional)
- Flujos completos

#### Functional Tests
- Escenarios de usuario final
- Requests HTTP completos
- Validación de respuestas

### 3. Estrategias de Mocking

#### Base de Datos
```php
// Mock de mysqli y prepared statements
$mockConexion = $this->createMock(mysqli::class);
$mockStmt = $this->createMock(mysqli_stmt::class);
$mockResult = $this->createMock(mysqli_result::class);
```

#### Sesiones PHP
```php
// Mock de session functions
class MockSession {
    private static $data = [];
    public static function start() { /* ... */ }
    public static function set($key, $value) { /* ... */ }
}
```

#### Headers y Redirects
```php
// Override de header() para testing
function header($string) {
    $GLOBALS['test_headers'][] = $string;
}
```

## Estructura de Tests

### Organización por Responsabilidad

1. **AutenticacionTest.php**
   - Tests de funciones core
   - Mocks completos de BD
   - Casos edge

2. **ValidacionSesionTest.php**
   - Tests de validaciones
   - Estados de sesión
   - Autorizaciones

3. **AutenticacionControllerTest.php**
   - Tests de controlador
   - Routing de peticiones
   - Integración de componentes

4. **AutenticacionBasicTest.php**
   - Tests sin dependencias
   - Validación de lógica básica
   - Ambiente sin PHPUnit

### Convenciones de Naming

```php
// Patrón: test[FunctionName][Scenario][ExpectedResult]
public function testIniciarSesionConUsuarioValidoRetornaExito()
public function testIniciarSesionConCredencialesInvalidasRetornaError()
public function testValidarSesionClienteConRolIncorrectoRedirecciona()
```

## Cobertura de Código

### Objetivo: >95% de cobertura

#### Métricas Clave
- **Line Coverage**: % de líneas ejecutadas
- **Function Coverage**: % de funciones llamadas  
- **Branch Coverage**: % de ramas condicionales
- **Path Coverage**: % de caminos de ejecución

#### Herramientas
```bash
# Generar reporte de cobertura
phpunit --coverage-html coverage/
phpunit --coverage-clover coverage.xml
```

## Automatización

### CI/CD Pipeline

```yaml
# Ejemplo para GitHub Actions
tests:
  runs-on: ubuntu-latest
  steps:
    - uses: actions/checkout@v2
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: 8.0
    - name: Install dependencies
      run: composer install
    - name: Run tests
      run: phpunit --coverage-clover coverage.xml
```

### Pre-commit Hooks
- Ejecutar tests antes de commit
- Validar cobertura mínima
- Formatear código

## Datos de Test

### Usuarios de Prueba
```php
$testUsers = [
    'cliente_valido' => [
        'id' => 1,
        'user' => 'cliente1',
        'password' => 'pass123',
        'rol' => 'cliente'
    ],
    'admin_valido' => [
        'id' => 2, 
        'user' => 'admin1',
        'password' => 'adminpass',
        'rol' => 'administrador'
    ]
];
```

### Escenarios de Error
- Usuario inexistente
- Contraseña incorrecta
- Sesión expirada
- Rol insuficiente
- Datos malformados

## Métricas y Reporting

### KPIs de Calidad
- Time to execute all tests < 30s
- Cobertura de código > 95%
- 0 tests fallidos en master
- Tiempo de feedback < 2min

### Reports Automáticos
- Cobertura de código (HTML + XML)
- Resultados por email/Slack
- Métricas de tendencia
- Alertas de regresión

## Mantenimiento

### Revisión Periódica
- Revisar tests obsoletos mensualmente
- Actualizar datos de prueba
- Optimizar performance de tests
- Refactoring de test helpers

### Evolución de Tests
- Añadir tests para nuevas features
- Mejorar assertions existentes
- Documentar casos edge nuevos
- Mantener DRY en test code