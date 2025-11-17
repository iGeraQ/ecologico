<?php

/**
 * Mock simple de la base de datos para testing
 */
class MockDatabase
{
    private static $clientes = [
        [
            'idCliente' => 1,
            'user' => 'admin',
            'contraseña' => 'admin',
            'rol' => 'administrador'
        ],
        [
            'idCliente' => 2,
            'user' => 'cliente1',
            'contraseña' => 'password123',
            'rol' => 'cliente'
        ],
        [
            'idCliente' => 3,
            'user' => 'gera',
            'contraseña' => 'qwerty',
            'rol' => 'cliente'
        ],
        [
            'idCliente' => 4,
            'user' => 'juan',
            'contraseña' => '123',
            'rol' => 'administrador'
        ]
    ];

    private static $habitaciones = [
        [
            'idhabitacion' => 1,
            'nombre' => 'Cascada Secreta',
            'categoria' => 'Deluxe',
            'disponibles' => 10,
            'numHabitaciones' => 20,
            'descripcion' => 'Habitación con ambiente de cascada',
            'costoPorNoche' => 2150,
            'capacidadDePersonas' => 3,
            'urlImagen' => 'cascada-secreta.jpg'
        ],
        [
            'idhabitacion' => 2,
            'nombre' => 'Ártico Blanco',
            'categoria' => 'Suite',
            'disponibles' => 1,
            'numHabitaciones' => 8,
            'descripcion' => 'Suite ambientada en el Ártico',
            'costoPorNoche' => 3900,
            'capacidadDePersonas' => 4,
            'urlImagen' => 'artico-blanco.jpg'
        ]
    ];

    /**
     * Buscar cliente por nombre de usuario
     */
    public static function buscarCliente($username)
    {
        foreach (self::$clientes as $cliente) {
            if ($cliente['user'] === $username) {
                return $cliente;
            }
        }
        return null;
    }

    /**
     * Insertar nuevo cliente
     */
    public static function insertarCliente($username, $password, $rol = 'cliente')
    {
        $nuevoId = count(self::$clientes) + 1;
        $nuevoCliente = [
            'idCliente' => $nuevoId,
            'user' => $username,
            'contraseña' => $password,
            'rol' => $rol
        ];
        
        self::$clientes[] = $nuevoCliente;
        return $nuevoId;
    }

    /**
     * Obtener todas las habitaciones
     */
    public static function obtenerHabitaciones()
    {
        return self::$habitaciones;
    }

    /**
     * Buscar habitación por ID
     */
    public static function buscarHabitacion($id)
    {
        foreach (self::$habitaciones as $habitacion) {
            if ($habitacion['idhabitacion'] == $id) {
                return $habitacion;
            }
        }
        return null;
    }

    /**
     * Reset de datos para testing
     */
    public static function reset()
    {
        // Restaurar datos originales
        self::$clientes = [
            [
                'idCliente' => 1,
                'user' => 'admin',
                'contraseña' => 'admin',
                'rol' => 'administrador'
            ],
            [
                'idCliente' => 2,
                'user' => 'cliente1',
                'contraseña' => 'password123',
                'rol' => 'cliente'
            ],
            [
                'idCliente' => 3,
                'user' => 'gera',
                'contraseña' => 'qwerty',
                'rol' => 'cliente'
            ],
            [
                'idCliente' => 4,
                'user' => 'juan',
                'contraseña' => '123',
                'rol' => 'administrador'
            ]
        ];
    }
}

/**
 * Mock de la clase conexiondb para usar en testing
 */
class MockConexiondb
{
    private $mockResult;
    
    public function __construct()
    {
        // Constructor mock
    }
    
    public function getConnection()
    {
        return new MockMysqli();
    }
}

/**
 * Mock de mysqli
 */
class MockMysqli
{
    public function prepare($query)
    {
        return new MockMysqliStmt($query);
    }
    
    public function close()
    {
        return true;
    }
}

/**
 * Mock de mysqli_stmt
 */
class MockMysqliStmt
{
    private $query;
    private $boundParams = [];
    private $result;
    
    public function __construct($query)
    {
        $this->query = $query;
    }
    
    public function bind_param($types, ...$params)
    {
        $this->boundParams = $params;
        return true;
    }
    
    public function execute()
    {
        // Simular consulta SELECT de clientes
        if (strpos($this->query, 'SELECT') !== false && strpos($this->query, 'clientes') !== false) {
            $username = $this->boundParams[0] ?? '';
            $cliente = MockDatabase::buscarCliente($username);
            
            if ($cliente) {
                $this->result = new MockMysqliResult([$cliente]);
            } else {
                $this->result = new MockMysqliResult([]);
            }
        }
        // Simular INSERT de clientes
        else if (strpos($this->query, 'INSERT') !== false && strpos($this->query, 'clientes') !== false) {
            $username = $this->boundParams[0] ?? '';
            $password = $this->boundParams[1] ?? '';
            MockDatabase::insertarCliente($username, $password);
            $this->result = new MockMysqliResult([]);
        }
        
        return true;
    }
    
    public function get_result()
    {
        return $this->result;
    }
    
    public function close()
    {
        return true;
    }
}

/**
 * Mock de mysqli_result
 */
class MockMysqliResult
{
    private $data;
    public $num_rows;
    
    public function __construct($data = [])
    {
        $this->data = $data;
        $this->num_rows = count($data);
    }
    
    public function fetch_assoc()
    {
        if (empty($this->data)) {
            return null;
        }
        return array_shift($this->data);
    }
    
    public function close()
    {
        return true;
    }
}