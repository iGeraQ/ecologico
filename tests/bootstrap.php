<?php

// Definir modo testing ANTES de incluir otros archivos
define('TESTING_MODE', true);

// Mock simple de base de datos directamente aquí
$GLOBALS['mock_usuarios'] = [
    'admin' => ['idCliente' => 1, 'user' => 'admin', 'contraseña' => 'admin', 'rol' => 'administrador'],
    'gera' => ['idCliente' => 3, 'user' => 'gera', 'contraseña' => 'qwerty', 'rol' => 'cliente'],
    'juan' => ['idCliente' => 4, 'user' => 'juan', 'contraseña' => '123', 'rol' => 'administrador']
];

// Incluir el mock
require_once __DIR__ . '/MockDatabase.php';

// Bootstrap file para PHPUnit
require_once __DIR__ . '/../src/servidor/config/config.inc.php';

// Mock de headers para evitar errores en testing
if (!function_exists('mockHeaders')) {
    function mockHeaders() {
        if (!defined('TESTING_HEADERS_SENT')) {
            define('TESTING_HEADERS_SENT', true);
        }
    }
}

// Override de header function para testing
if (!function_exists('header_original')) {
    if (function_exists('header')) {
        // Guardar función original
        function header_original($string, $replace = true, $http_response_code = null) {
            return \header($string, $replace, $http_response_code);
        }
    }
}

// Mock de session functions
class MockSession {
    private static $data = [];
    private static $started = false;
    
    public static function start() {
        self::$started = true;
    }
    
    public static function isStarted() {
        return self::$started;
    }
    
    public static function set($key, $value) {
        self::$data[$key] = $value;
    }
    
    public static function get($key) {
        return self::$data[$key] ?? null;
    }
    
    public static function unset($key = null) {
        if ($key === null) {
            self::$data = [];
        } else {
            unset(self::$data[$key]);
        }
    }
    
    public static function destroy() {
        self::$data = [];
        self::$started = false;
    }
    
    public static function getData() {
        return self::$data;
    }
    
    public static function reset() {
        self::$data = [];
        self::$started = false;
        MockDatabase::reset();
    }
}

// Limpiar estado entre tests
register_shutdown_function(function() {
    if (isset($GLOBALS['test_headers'])) {
        unset($GLOBALS['test_headers']);
    }
    MockSession::reset();
});