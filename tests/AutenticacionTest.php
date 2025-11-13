<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests para el módulo de autenticación
 * 
 * @covers sesion.php
 * @covers autenticacion.php
 */
class AutenticacionTest extends TestCase
{
    private $mockConexion;
    private $mockStmt;
    private $mockResult;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset global state
        $GLOBALS['test_headers'] = [];
        MockSession::reset();
        
        // Mock de la conexión a base de datos
        $this->mockConexion = $this->createMock(mysqli::class);
        $this->mockStmt = $this->createMock(mysqli_stmt::class);
        $this->mockResult = $this->createMock(mysqli_result::class);
        
        // Mock de la clase Conexiondb
        $mockConexionDb = $this->createMock(Conexiondb::class);
        $mockConexionDb->method('getConnection')->willReturn($this->mockConexion);
        
        // Override de la clase Conexiondb para testing
        if (!class_exists('TestableConexiondb')) {
            eval('class TestableConexiondb extends Conexiondb {
                private static $mockInstance;
                
                public static function setMockInstance($mock) {
                    self::$mockInstance = $mock;
                }
                
                public function getConnection() {
                    if (self::$mockInstance) {
                        return self::$mockInstance;
                    }
                    return parent::getConnection();
                }
            }');
        }
        
        TestableConexiondb::setMockInstance($this->mockConexion);
        
        // Define testing mode
        if (!defined('TESTING_HEADERS_SENT')) {
            define('TESTING_HEADERS_SENT', true);
        }
    }

    protected function tearDown(): void
    {
        // Limpiar estado después de cada test
        if (isset($GLOBALS['test_headers'])) {
            unset($GLOBALS['test_headers']);
        }
        MockSession::reset();
        
        parent::tearDown();
    }

    /**
     * @test
     * @covers iniciarSesion
     */
    public function testIniciarSesionExitoso()
    {
        // Arrange
        $nombreUsuario = 'testuser';
        $contrasenaUsuario = 'testpass';
        
        // Mock de datos de usuario válido (cliente)
        $userData = [
            'idCliente' => 1,
            'user' => 'testuser',
            'contraseña' => 'testpass',
            'rol' => 'cliente'
        ];
        
        // Configurar mocks
        $this->mockConexion->expects($this->once())
            ->method('prepare')
            ->with('SELECT idCliente, user, contraseña, rol FROM clientes WHERE user = ?')
            ->willReturn($this->mockStmt);
            
        $this->mockStmt->expects($this->once())
            ->method('bind_param')
            ->with('s', $nombreUsuario);
            
        $this->mockStmt->expects($this->once())
            ->method('execute');
            
        $this->mockStmt->expects($this->once())
            ->method('get_result')
            ->willReturn($this->mockResult);
            
        $this->mockResult->expects($this->once())
            ->method('num_rows')
            ->willReturnReference($numRows = 1);
            
        $this->mockResult->expects($this->once())
            ->method('fetch_assoc')
            ->willReturn($userData);
            
        $this->mockStmt->expects($this->once())
            ->method('close');

        // Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Script terminated with exit()');
        
        // Incluir el archivo que contiene las funciones
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        iniciarSesion($nombreUsuario, $contrasenaUsuario);
        
        // Verificar que se estableció la sesión correctamente
        $this->assertEquals(1, MockSession::get('idUsuario'));
        $this->assertEquals('testuser', MockSession::get('user'));
        $this->assertEquals('cliente', MockSession::get('rol'));
        
        // Verificar redirección correcta
        $this->assertContains('Location: ../cliente/view/principal.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers iniciarSesion
     */
    public function testIniciarSesionAdministrador()
    {
        // Arrange
        $nombreUsuario = 'admin';
        $contrasenaUsuario = 'adminpass';
        
        // Mock de datos de usuario administrador
        $userData = [
            'idCliente' => 2,
            'user' => 'admin',
            'contraseña' => 'adminpass',
            'rol' => 'administrador'
        ];
        
        // Configurar mocks
        $this->mockConexion->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);
            
        $this->mockStmt->expects($this->once())
            ->method('bind_param')
            ->with('s', $nombreUsuario);
            
        $this->mockStmt->expects($this->once())
            ->method('execute');
            
        $this->mockStmt->expects($this->once())
            ->method('get_result')
            ->willReturn($this->mockResult);
            
        $this->mockResult->expects($this->once())
            ->method('num_rows')
            ->willReturnReference($numRows = 1);
            
        $this->mockResult->expects($this->once())
            ->method('fetch_assoc')
            ->willReturn($userData);
            
        $this->mockStmt->expects($this->once())
            ->method('close');

        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        iniciarSesion($nombreUsuario, $contrasenaUsuario);
        
        // Verificar sesión de administrador
        $this->assertEquals(2, MockSession::get('idUsuario'));
        $this->assertEquals('admin', MockSession::get('user'));
        $this->assertEquals('administrador', MockSession::get('rol'));
        
        // Verificar redirección a panel de admin
        $this->assertContains('Location: ../cliente/view/adminpanel.php', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers iniciarSesion
     */
    public function testIniciarSesionUsuarioInexistente()
    {
        // Arrange
        $nombreUsuario = 'noexiste';
        $contrasenaUsuario = 'wrongpass';
        
        // Configurar mocks para usuario no encontrado
        $this->mockConexion->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);
            
        $this->mockStmt->expects($this->once())
            ->method('bind_param');
            
        $this->mockStmt->expects($this->once())
            ->method('execute');
            
        $this->mockStmt->expects($this->once())
            ->method('get_result')
            ->willReturn($this->mockResult);
            
        $this->mockResult->expects($this->once())
            ->method('num_rows')
            ->willReturnReference($numRows = 0);
            
        $this->mockStmt->expects($this->once())
            ->method('close');

        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        iniciarSesion($nombreUsuario, $contrasenaUsuario);
        
        // Verificar redirección con error
        $this->assertContains('Location: ../../index.php?error=1', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers iniciarSesion
     */
    public function testIniciarSesionContrasenaIncorrecta()
    {
        // Arrange
        $nombreUsuario = 'testuser';
        $contrasenaUsuario = 'wrongpass';
        
        $userData = [
            'idCliente' => 1,
            'user' => 'testuser',
            'contraseña' => 'correctpass',
            'rol' => 'cliente'
        ];
        
        // Configurar mocks
        $this->mockConexion->expects($this->once())
            ->method('prepare')
            ->willReturn($this->mockStmt);
            
        $this->mockStmt->expects($this->once())
            ->method('bind_param');
            
        $this->mockStmt->expects($this->once())
            ->method('execute');
            
        $this->mockStmt->expects($this->once())
            ->method('get_result')
            ->willReturn($this->mockResult);
            
        $this->mockResult->expects($this->once())
            ->method('num_rows')
            ->willReturnReference($numRows = 1);
            
        $this->mockResult->expects($this->once())
            ->method('fetch_assoc')
            ->willReturn($userData);
            
        $this->mockStmt->expects($this->once())
            ->method('close');

        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        iniciarSesion($nombreUsuario, $contrasenaUsuario);
        
        // Verificar redirección con error
        $this->assertContains('Location: ../../index.php?error=1', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers registrarUsuario
     */
    public function testRegistrarUsuarioExitoso()
    {
        // Arrange
        $nombreUsuario = 'nuevouser';
        $contrasenaUsuario = 'newpass';
        $rol = 'cliente';
        
        // Configurar mocks
        $this->mockConexion->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO clientes(user, contraseña, rol) VALUES(?,?,?)')
            ->willReturn($this->mockStmt);
            
        $this->mockStmt->expects($this->once())
            ->method('bind_param')
            ->with('sss', $nombreUsuario, $contrasenaUsuario, $rol);
            
        $this->mockStmt->expects($this->once())
            ->method('execute');
            
        $this->mockStmt->expects($this->once())
            ->method('close');

        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        registrarUsuario($nombreUsuario, $contrasenaUsuario);
        
        // Verificar redirección con éxito
        $this->assertContains('Location: ../../index.php?registro_exitoso=1', $GLOBALS['test_headers']);
    }

    /**
     * @test
     * @covers cerrarSesion
     */
    public function testCerrarSesion()
    {
        // Arrange
        MockSession::start();
        MockSession::set('idUsuario', 1);
        MockSession::set('user', 'testuser');
        MockSession::set('rol', 'cliente');
        
        // Act & Assert
        $this->expectException(Exception::class);
        
        require_once __DIR__ . '/../src/servidor/sesion.php';
        
        cerrarSesion();
        
        // Verificar que la sesión se destruyó
        $this->assertEmpty(MockSession::getData());
        $this->assertFalse(MockSession::isStarted());
        
        // Verificar redirección
        $this->assertContains('Location: ../../index.php', $GLOBALS['test_headers']);
    }
}