<?php

// Bootstrap file para PHPUnit
require_once __DIR__ . '/../config.inc.php';

// Mock de headers para evitar errores en testing
if (!function_exists('mockHeaders')) {
    function mockHeaders() {
        if (!defined('TESTING_HEADERS_SENT')) {
            define('TESTING_HEADERS_SENT', true);
        }
    }
}

// Override de header function para testing
if (!function_exists('header')) {
    function header($string, $replace = true, $http_response_code = null) {
        if (defined('TESTING_HEADERS_SENT')) {
            // En testing, almacenamos headers en una variable global
            if (!isset($GLOBALS['test_headers'])) {
                $GLOBALS['test_headers'] = [];
            }
            $GLOBALS['test_headers'][] = $string;
            return;
        }
        
        // En producción, usa la función original
        return \header($string, $replace, $http_response_code);
    }
}

// Override de exit function para testing
if (!function_exists('exitMock')) {
    function exitMock($status = null) {
        if (defined('TESTING_HEADERS_SENT')) {
            throw new Exception('Script terminated with exit()');
        }
        exit($status);
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
    }
}

// Limpiar estado entre tests
register_shutdown_function(function() {
    if (isset($GLOBALS['test_headers'])) {
        unset($GLOBALS['test_headers']);
    }
    MockSession::reset();
});