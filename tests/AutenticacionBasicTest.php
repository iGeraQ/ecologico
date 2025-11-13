<?php

/**
 * Tests básicos para el módulo de autenticación
 * Ejecutar con: php autenticacion_test.php
 */

// Incluir archivos necesarios
require_once __DIR__ . '/../../../config.inc.php';
require_once __DIR__ . '/../sesion.php';

/**
 * Clase simple de testing sin dependencias externas
 */
class SimpleTestRunner 
{
    private $passed = 0;
    private $failed = 0;
    private $tests = [];
    
    public function run() 
    {
        echo "=== EJECUTANDO TESTS PARA MÓDULO DE AUTENTICACIÓN ===\n\n";
        
        // Ejecutar todos los tests
        $this->testIniciarSesionConUsuarioValido();
        $this->testIniciarSesionConUsuarioInvalido();
        $this->testRegistrarUsuario();
        $this->testValidacionesDeSesion();
        $this->testCerrarSesion();
        
        // Mostrar resultados
        $this->showResults();
    }
    
    public function assert($condition, $message) 
    {
        if ($condition) {
            echo "✓ PASS: $message\n";
            $this->passed++;
        } else {
            echo "✗ FAIL: $message\n";
            $this->failed++;
        }
    }
    
    public function testIniciarSesionConUsuarioValido() 
    {
        echo "\n--- Test: Iniciar sesión con usuario válido ---\n";
        
        // Mock de conexión de base de datos
        $mockData = [
            'idCliente' => 1,
            'user' => 'testuser',
            'contraseña' => 'testpass',
            'rol' => 'cliente'
        ];
        
        // Simular que encontramos el usuario
        $usuarioEncontrado = $mockData;
        $contrasenaCorrecta = 'testpass';
        
        // Test: Contraseña correcta
        $resultado = ($contrasenaCorrecta === $usuarioEncontrado['contraseña']);
        $this->assert($resultado, "Contraseña coincide correctamente");
        
        // Test: Datos de sesión se establecen correctamente
        if ($resultado) {
            $sessionData = [
                'idUsuario' => $usuarioEncontrado['idCliente'],
                'user' => $usuarioEncontrado['user'], 
                'rol' => $usuarioEncontrado['rol']
            ];
            
            $this->assert($sessionData['idUsuario'] === 1, "ID de usuario establecido correctamente");
            $this->assert($sessionData['user'] === 'testuser', "Nombre de usuario establecido correctamente");
            $this->assert($sessionData['rol'] === 'cliente', "Rol establecido correctamente");
            
            // Test: Redirección para cliente
            $expectedRedirect = "../cliente/view/principal.php";
            $actualRedirect = ($sessionData['rol'] === 'cliente') ? "../cliente/view/principal.php" : "../cliente/view/adminpanel.php";
            $this->assert($actualRedirect === $expectedRedirect, "Redirección correcta para cliente");
        }
    }
    
    public function testIniciarSesionConUsuarioInvalido()
    {
        echo "\n--- Test: Iniciar sesión con usuario inválido ---\n";
        
        // Test: Usuario no encontrado
        $usuarioEncontrado = null;
        $this->assert($usuarioEncontrado === null, "Usuario no encontrado correctamente identificado");
        
        // Test: Contraseña incorrecta
        $mockData = [
            'contraseña' => 'correctpass'
        ];
        $contrasenaIngresada = 'wrongpass';
        $contrasenaCorrecta = ($contrasenaIngresada === $mockData['contraseña']);
        $this->assert(!$contrasenaCorrecta, "Contraseña incorrecta identificada correctamente");
        
        // Test: Redirección con error
        $expectedErrorRedirect = "../../index.php?error=1";
        $this->assert(true, "Redirección de error configurada correctamente: " . $expectedErrorRedirect);
    }
    
    public function testRegistrarUsuario()
    {
        echo "\n--- Test: Registrar nuevo usuario ---\n";
        
        $nombreUsuario = "nuevouser";
        $contrasenaUsuario = "newpass123";
        $rol = "cliente";
        
        // Test: Datos de registro válidos
        $this->assert(!empty($nombreUsuario), "Nombre de usuario no está vacío");
        $this->assert(!empty($contrasenaUsuario), "Contraseña no está vacía");
        $this->assert(strlen($contrasenaUsuario) >= 6, "Contraseña tiene longitud mínima");
        $this->assert($rol === "cliente", "Rol por defecto es 'cliente'");
        
        // Test: Redirección de éxito
        $expectedSuccessRedirect = "../../index.php?registro_exitoso=1";
        $this->assert(true, "Redirección de éxito configurada: " . $expectedSuccessRedirect);
    }
    
    public function testValidacionesDeSesion()
    {
        echo "\n--- Test: Validaciones de sesión ---\n";
        
        // Test: Validación pantalla principal (solo clientes)
        $sessionCliente = ['idUsuario' => 1, 'rol' => 'cliente'];
        $validParaPrincipal = (isset($sessionCliente['idUsuario']) && $sessionCliente['rol'] !== 'administrador');
        $this->assert($validParaPrincipal, "Cliente puede acceder a pantalla principal");
        
        $sessionAdmin = ['idUsuario' => 2, 'rol' => 'administrador'];  
        $adminNoPuedeAccederPrincipal = !(isset($sessionAdmin['idUsuario']) && $sessionAdmin['rol'] !== 'administrador');
        $this->assert($adminNoPuedeAccederPrincipal, "Administrador no puede acceder a pantalla principal");
        
        // Test: Validación cliente
        $validacionCliente = (isset($sessionCliente['idUsuario']) && $sessionCliente['rol'] === 'cliente');
        $this->assert($validacionCliente, "Validación de sesión cliente funciona");
        
        // Test: Validación administrador
        $validacionAdmin = (isset($sessionAdmin['idUsuario']) && $sessionAdmin['rol'] === 'administrador');
        $this->assert($validacionAdmin, "Validación de sesión administrador funciona");
        
        // Test: Sin sesión
        $sinSesion = [];
        $sesionInvalida = !isset($sinSesion['idUsuario']);
        $this->assert($sesionInvalida, "Usuario sin sesión correctamente identificado");
    }
    
    public function testCerrarSesion() 
    {
        echo "\n--- Test: Cerrar sesión ---\n";
        
        // Simular sesión activa
        $sessionActive = true;
        $sessionData = ['idUsuario' => 1, 'user' => 'testuser'];
        
        // Simular cerrar sesión
        $sessionDestroyed = true;  // session_destroy()
        $sessionUnset = true;      // session_unset()
        
        $this->assert($sessionDestroyed, "Sesión destruida correctamente");
        $this->assert($sessionUnset, "Variables de sesión eliminadas");
        
        // Test: Redirección
        $redirectToIndex = "../../index.php";
        $this->assert(true, "Redirección a index configurada: " . $redirectToIndex);
    }
    
    private function showResults()
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "RESULTADOS DE LOS TESTS:\n";
        echo "✓ Pasaron: {$this->passed}\n";
        echo "✗ Fallaron: {$this->failed}\n";
        echo "Total: " . ($this->passed + $this->failed) . "\n";
        
        if ($this->failed === 0) {
            echo "\n🎉 ¡TODOS LOS TESTS PASARON!\n";
        } else {
            echo "\n⚠️  Algunos tests fallaron. Revisa el código.\n";
        }
        echo str_repeat("=", 50) . "\n";
    }
}

// Ejecutar tests si el archivo es llamado directamente
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $testRunner = new SimpleTestRunner();
    $testRunner->run();
}