# Guía Rápida de Testing - Proyecto Ecológico

## 🚀 Comandos Esenciales

### Ejecución Local (Windows/XAMPP)
```bash
# Todos los tests
C:\xampp\php\php.exe vendor\bin\phpunit

# Script incluido (más fácil)
test.bat

# Test específico
C:\xampp\php\php.exe vendor\bin\phpunit tests\AutenticacionTest.php

# Método específico
C:\xampp\php\php.exe vendor\bin\phpunit --filter testLoginExitoso

# Con verbose
C:\xampp\php\php.exe vendor\bin\phpunit --testdox --verbose
```

### Ejecución con Composer
```bash
composer test
composer test-coverage
composer test-filter -- "nombreTest"
```

## 📋 Tests Disponibles

| Suite | Archivo | Descripción |
|-------|---------|-------------|
| **Autenticacion** | `AutenticacionTest.php` | Login, registro, validación de usuarios |
| **Habitaciones** | `HabitacionesTest.php` | CRUD de habitaciones, listado, filtros |
| **Pagos** | `PagoTest.php` | Procesamiento de pagos, reservaciones |

## 🔧 Debugging Rápido

### Agregar Debug en Tests
```php
// En cualquier test
echo "DEBUG: " . print_r($variable, true) . "\n";
```

### VS Code (Recomendado)
1. Instalar: `recca0120.vscode-phpunit`
2. Colocar breakpoint (clic en margen izquierdo)
3. F5 para debugear

### Comandos de Debug
```bash
# Solo failures
C:\xampp\php\php.exe vendor\bin\phpunit --stop-on-failure

# Con stack trace
C:\xampp\php\php.exe vendor\bin\phpunit --verbose

# Test específico con debug
C:\xampp\php\php.exe vendor\bin\phpunit --filter testNombre --debug
```

## 🌐 GitHub Actions

### ¿Cuándo se ejecutan?
- ✅ Push a `main`
- ✅ Pull Request a `main`

### ¿Dónde ver resultados?
1. GitHub → Pestaña "Actions"
2. Seleccionar "PHP Composer"
3. Ver logs detallados

### Estados
- 🟢 **Success**: Todos los tests OK
- 🔴 **Failure**: Tests fallaron
- 🟡 **Pending**: Ejecutándose

## ⚙️ Configuración

### Variables de Entorno (phpunit.xml)
```xml
<env name="APP_ENV" value="testing"/>
<env name="DB_HOST" value="localhost"/>
<env name="DB_NAME" value="ecologico_test"/>
<env name="DB_USER" value="root"/>
<env name="DB_PASS" value=""/>
```

### Estructura
```
tests/
├── bootstrap.php          # Setup inicial
├── MockDatabase.php      # Mock DB
├── AutenticacionTest.php # Tests login
├── HabitacionesTest.php  # Tests habitaciones  
└── PagoTest.php         # Tests pagos
```

## 🚨 Solución de Problemas

| Error | Solución |
|-------|----------|
| `php command not found` | Usar ruta completa: `C:\xampp\php\php.exe` |
| `Class not found` | `composer dump-autoload` |
| `Connection refused` | Verificar base de datos test |
| Tests lentos | Usar `--stop-on-failure` |

## 📊 Métricas

### Cobertura de Código
```bash
# Generar reporte HTML
C:\xampp\php\php.exe vendor\bin\phpunit --coverage-html coverage

# Ver reporte en: coverage/index.html
```

### Estadísticas por Suite
```bash
# Solo suite específica
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Autenticacion
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Habitaciones
C:\xampp\php\php.exe vendor\bin\phpunit --testsuite Pagos
```

## 🎯 Tips Pro

1. **Usar test.bat** para ejecución rápida
2. **VS Code + breakpoints** para debugging visual
3. **--filter** para tests específicos
4. **--stop-on-failure** para debugging eficiente
5. **Composer scripts** para comandos consistentes

---
*Documentación generada para el equipo de desarrollo del proyecto Ecológico*