<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests para las validaciones de sesión
 * 
 * @covers validarSesionPantallaPrincipal
 * @covers validarSesionCliente  
 * @covers validarSesionAdministrador
 */
class ValidacionSesionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset global state
        $GLOBALS['test_headers'] = [];
        MockSession::reset();
        
        // Define testing mode
        if (!defined('TESTING_HEADERS_SENT')) {
            define('TESTING_HEADERS_SENT', true);
        }
    }

    protected function tearDown(): void
    {
        if (isset($GLOBALS['test_headers'])) {
            unset($GLOBALS['test_headers']);
        }
        MockSession::reset();
        
        parent::tearDown();
    }

    /**
     * @test
     * @covers validarSesionPantallaPrincipal
     */
    public function testValidarSesionPantallaPrincipalConClienteValido()
    {
        // Arrange - Sesión válida de cliente
        MockSession::start();
        MockSession::set('idUsuario', 1);
        MockSession::set('rol', 'cliente');
        
        // Act
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        // No debería lanzar excepción ni redireccionar
        validarSesionPantallaPrincipal();
        
        // Assert - No hay redirecciones
        $this->assertEmpty($GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionPantallaPrincipal
     */
    public function testValidarSesionPantallaPrincipalSinSesion()
    {
        // Arrange - No hay sesión iniciada
        MockSession::start();
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionPantallaPrincipal();
        
        // Verificar redirección
        $this->assertContains('Location: ../../../index.php', $GLOBALS['test_headers']);
    }

    /**
     * @test  
     * @covers validarSesionPantallaPrincipal
     */
    public function testValidarSesionPantallaPrincipalConAdministrador()
    {
        // Arrange - Sesión de administrador (no permitida en pantalla principal)
        MockSession::start();
        MockSession::set('idUsuario', 2);
        MockSession::set('rol', 'administrador');
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionPantallaPrincipal();
        
        // Verificar redirección
        $this->assertContains('Location: ../../../index.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionCliente
     */
    public function testValidarSesionClienteConClienteValido()
    {
        // Arrange
        MockSession::start();
        MockSession::set('idUsuario', 1);
        MockSession::set('rol', 'cliente');
        
        // Act
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionCliente();
        
        // Assert - No hay redirecciones
        $this->assertEmpty($GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionCliente
     */
    public function testValidarSesionClienteSinSesion()
    {
        // Arrange - No hay sesión
        MockSession::start();
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionCliente();
        
        // Verificar redirección
        $this->assertContains('Location: ../../../index.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionCliente
     */
    public function testValidarSesionClienteConAdministrador()
    {
        // Arrange - Rol incorrecto
        MockSession::start();
        MockSession::set('idUsuario', 2);
        MockSession::set('rol', 'administrador');
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionCliente();
        
        // Verificar redirección
        $this->assertContains('Location: ../../../index.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionAdministrador
     */
    public function testValidarSesionAdministradorConAdminValido()
    {
        // Arrange
        MockSession::start();
        MockSession::set('idUsuario', 2);
        MockSession::set('rol', 'administrador');
        
        // Act
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionAdministrador();
        
        // Assert - No hay redirecciones
        $this->assertEmpty($GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionAdministrador
     */
    public function testValidarSesionAdministradorSinSesion()
    {
        // Arrange - No hay sesión
        MockSession::start();
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionAdministrador();
        
        // Verificar redirección
        $this->assertContains('Location: ../../../index.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers validarSesionAdministrador
     */
    public function testValidarSesionAdministradorConCliente()
    {
        // Arrange - Rol incorrecto
        MockSession::start();
        MockSession::set('idUsuario', 1);
        MockSession::set('rol', 'cliente');
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        validarSesionAdministrador();
        
        // Verificar redirección
        $this->assertContains('Location: ../../../index.php', $GLOBALS['test_headers']);
    }
}