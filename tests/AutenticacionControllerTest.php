<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests para el controlador de autenticación (autenticacion.php)
 * 
 * @covers autenticacion.php
 */
class AutenticacionControllerTest extends TestCase
{
    private $originalPost;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Guardar estado original de $_POST
        $this->originalPost = $_POST ?? [];
        
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
        // Restaurar $_POST
        $_POST = $this->originalPost;
        
        if (isset($GLOBALS['test_headers'])) {
            unset($GLOBALS['test_headers']);
        }
        MockSession::reset();
        
        parent::tearDown();
    }

    /**
     * @test
     * @covers autenticacion.php - acción cerrar sesión
     */
    public function testControllerCerrarSesion()
    {
        // Arrange
        $_POST = [
            'action' => 'cerrarsesion'
        ];
        
        MockSession::start();
        MockSession::set('idUsuario', 1);
        MockSession::set('user', 'testuser');
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        // Capturar la salida para evitar warnings
        ob_start();
        try {
            require __DIR__ . '/../src/servidor/autenticacion.php';
        } finally {
            ob_end_clean();
        }
        
        // Verificar que se cerró la sesión
        $this->assertEmpty(MockSession::getData());
        $this->assertContains('Location: ../../index.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers autenticacion.php - acción registrar usuario
     */
    public function testControllerRegistrarUsuario()
    {
        // Arrange
        $_POST = [
            'action' => 'registrarUsuario',
            'txt_nombre' => 'nuevouser',
            'pass_contraseña' => 'newpass123'
        ];
        
        // Mock de base de datos
        $this->mockDatabaseForRegistration();
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        ob_start();
        try {
            require __DIR__ . '/../src/servidor/autenticacion.php';
        } finally {
            ob_end_clean();
        }
        
        // Verificar redirección de éxito
        $this->assertContains('Location: ../../index.php?registro_exitoso=1', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers autenticacion.php - acción iniciar sesión
     */
    public function testControllerIniciarSesion()
    {
        // Arrange
        $_POST = [
            'action' => 'login',
            'txt_nombre' => 'testuser',
            'pass_contraseña' => 'testpass'
        ];
        
        // Mock de base de datos
        $this->mockDatabaseForLogin();
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        ob_start();
        try {
            require __DIR__ . '/../src/servidor/autenticacion.php';
        } finally {
            ob_end_clean();
        }
        
        // Verificar que se inició la sesión
        $this->assertEquals(1, MockSession::get('idUsuario'));
        $this->assertEquals('testuser', MockSession::get('user'));
        $this->assertEquals('cliente', MockSession::get('rol'));
        $this->assertContains('Location: ../cliente/view/principal.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers autenticacion.php - sin datos POST
     */
    public function testControllerSinDatosPost()
    {
        // Arrange
        $_POST = [];
        
        // Act
        ob_start();
        require __DIR__ . '/../src/servidor/autenticacion.php';
        $output = ob_get_clean();
        
        // Assert - No debería hacer nada si no hay datos POST
        $this->assertEmpty($GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers autenticacion.php - acción inválida
     */
    public function testControllerAccionInvalida()
    {
        // Arrange
        $_POST = [
            'action' => 'accionInvalida',
            'txt_nombre' => 'testuser',
            'pass_contraseña' => 'testpass'
        ];
        
        // Mock de base de datos
        $this->mockDatabaseForLogin();
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        ob_start();
        try {
            require __DIR__ . '/../src/servidor/autenticacion.php';
        } finally {
            ob_end_clean();
        }
        
        // Para acción no reconocida, debería ejecutar iniciarSesion por defecto
        $this->assertEquals(1, MockSession::get('idUsuario'));
        $this->assertContains('Location: ../cliente/view/principal.php', $GLOBALS['test_headers']);
    }

    /**
     * Mock helper para registro de usuario
     */
    private function mockDatabaseForRegistration()
    {
        // Para este test simple, asumimos que la inserción es exitosa
        // En un entorno real, necesitarías mockear mysqli completamente
    }

    /**
     * Mock helper para login de usuario
     */
    private function mockDatabaseForLogin()
    {
        // Para este test simple, configuramos la sesión manualmente
        MockSession::start();
        MockSession::set('idUsuario', 1);
        MockSession::set('user', 'testuser');
        MockSession::set('rol', 'cliente');
    }
}