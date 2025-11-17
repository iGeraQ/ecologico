<?php

use PHPUnit\Framework\TestCase;

/**
 * Tests para el módulo de autenticación usando mock de base de datos
 */
class AutenticacionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $_POST = [];
        $_SESSION = [];
        // Reset del mock para cada test
        MockDatabase::reset();
    }

    /**
     * @test  
     * Test 1: Contraseña incorrecta
     */
    public function testLoginContrasenaInvalida()
    {
        // Arrange
        $usuario = MockDatabase::buscarCliente('admin');
        $contrasenaIngresada = 'contrasena_incorrecta_123';
        
        // Act - Verificar contraseña como en el servidor
        $contrasenaCorrecta = ($contrasenaIngresada === $usuario['contraseña']);
        
        // Assert
        $this->assertFalse($contrasenaCorrecta, 'Contraseña incorrecta debe ser rechazada');
        
        echo "\n✅ Test 2 PASADO: Contraseña incorrecta rechazada correctamente";
    }

    /**
     * @test
     * Test 2: Accesos correctos
     */
    public function testLoginAccesoExitoso()
    {
        // Arrange
        $usuario = MockDatabase::buscarCliente('admin');
        $contrasenaIngresada = 'admin';
        
        // Act - Verificar contraseña como en el servidor
        $contrasenaCorrecta = ($contrasenaIngresada === $usuario['contraseña']);
        
        if ($contrasenaCorrecta) {
            // Simular inicio de sesión exitoso
            $_SESSION['idUsuario'] = $usuario['idCliente'];
            $_SESSION['user'] = $usuario['user'];
            $_SESSION['rol'] = $usuario['rol'];
        }
        
        // Assert
        $this->assertTrue($contrasenaCorrecta, 'Contraseña correcta debe ser aceptada');
        $this->assertEquals(1, $_SESSION['idUsuario'], 'ID de usuario debe establecerse');
        $this->assertEquals('admin', $_SESSION['user'], 'Nombre de usuario debe establecerse');
        $this->assertEquals('administrador', $_SESSION['rol'], 'Rol debe establecerse correctamente');
        
        echo "\n✅ Test 3 PASADO: Login exitoso con admin/admin - Rol: " . $_SESSION['rol'];
    }

    /**
     * @test
     * Test adicional: Verificar que el mock tiene los datos correctos
     */
    public function testMockBaseDatos()
    {
        // Test admin
        $admin = MockDatabase::buscarCliente('admin');
        $this->assertNotNull($admin, 'Usuario admin debe existir');
        $this->assertEquals('administrador', $admin['rol'], 'Admin debe tener rol administrador');
        
        // Test cliente
        $cliente = MockDatabase::buscarCliente('gera');
        $this->assertNotNull($cliente, 'Usuario gera debe existir');
        $this->assertEquals('cliente', $cliente['rol'], 'Gera debe tener rol cliente');
        
        // Test usuario inexistente
        $inexistente = MockDatabase::buscarCliente('usuario_que_no_existe');
        $this->assertNull($inexistente, 'Usuario inexistente debe retornar null');
        
        echo "\n✅ Test Mock DB PASADO: Base de datos mock funcionando correctamente";
    }
}