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

    private static $reservaciones = [];
    private static $detallesReservacion = [];

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
     * Insertar nueva habitación
     */
    public static function insertarHabitacion($nombre, $categoria, $descripcion, $numHab, $disponibles, $capacidad, $costo, $urlImg)
    {
        $nuevoId = count(self::$habitaciones) + 1;
        $nuevaHabitacion = [
            'idhabitacion' => $nuevoId,
            'nombre' => $nombre,
            'categoria' => $categoria,
            'descripcion' => $descripcion,
            'numHabitaciones' => $numHab,
            'disponibles' => $disponibles,
            'capacidadDePersonas' => $capacidad,
            'costoPorNoche' => $costo,
            'urlImagen' => $urlImg
        ];
        
        self::$habitaciones[] = $nuevaHabitacion;
        return $nuevoId;
    }

    /**
     * Actualizar habitación
     */
    public static function actualizarHabitacion($id, $campo, $valor)
    {
        foreach (self::$habitaciones as &$habitacion) {
            if ($habitacion['idhabitacion'] == $id) {
                if ($valor === -1 && $campo === 'disponibles') {
                    // Caso especial: decrementar disponibilidad
                    $habitacion['disponibles']--;
                } else {
                    $habitacion[$campo] = $valor;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Eliminar habitación
     */
    public static function eliminarHabitacion($id)
    {
        foreach (self::$habitaciones as $key => $habitacion) {
            if ($habitacion['idhabitacion'] == $id) {
                unset(self::$habitaciones[$key]);
                // Reindexar el array
                self::$habitaciones = array_values(self::$habitaciones);
                return true;
            }
        }
        return false;
    }

    /**
     * Insertar nueva reservación
     */
    public static function insertarReservacion($idHabitacion, $idCliente, $fechaReservacion, $inicioEstadia, $finEstadia, $subtotal)
    {
        $nuevoId = count(self::$reservaciones) + 1;
        $nuevaReservacion = [
            'idReserva' => $nuevoId,
            'idHabitacion' => $idHabitacion,
            'idCliente' => $idCliente,
            'fechaReservacion' => $fechaReservacion,
            'inicioEstadia' => $inicioEstadia,
            'finEstadia' => $finEstadia,
            'subtotal' => $subtotal
        ];
        
        self::$reservaciones[] = $nuevaReservacion;
        return $nuevoId;
    }

    /**
     * Insertar detalles de reservación
     */
    public static function insertarDetalleReservacion($idReserva, $formaDePago, $total)
    {
        $nuevoDetalle = [
            'idDetalle' => count(self::$detallesReservacion) + 1,
            'idReserva' => $idReserva,
            'formaDePago' => $formaDePago,
            'total' => $total
        ];
        
        self::$detallesReservacion[] = $nuevoDetalle;
        return $nuevoDetalle['idDetalle'];
    }

    /**
     * Obtener todas las reservaciones
     */
    public static function obtenerReservaciones()
    {
        return self::$reservaciones;
    }

    /**
     * Obtener detalles de reservaciones
     */
    public static function obtenerDetallesReservacion()
    {
        return self::$detallesReservacion;
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

        // Restaurar habitaciones originales
        self::$habitaciones = [
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

        // Limpiar reservaciones
        self::$reservaciones = [];
        self::$detallesReservacion = [];
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
    public $insert_id = 0;
    public $error = '';
    
    public function prepare($query)
    {
        return new MockMysqliStmt($query);
    }
    
    public function query($query)
    {
        $stmt = new MockMysqliStmt($query);
        $result = $stmt->execute();
        $this->insert_id = $stmt->insert_id;
        $this->error = $stmt->error;
        return $result;
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
    public $insert_id = 0;
    public $error = '';
    
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
        // Simular INSERT de habitaciones
        else if (strpos($this->query, 'INSERT') !== false && strpos($this->query, 'habitaciones') !== false) {
            // Los parámetros vienen en el orden del bind_param
            $nombre = $this->boundParams[0] ?? '';
            $categoria = $this->boundParams[1] ?? '';
            $descripcion = $this->boundParams[2] ?? '';
            $numHabitaciones = $this->boundParams[3] ?? 0;
            $disponibles = $this->boundParams[4] ?? 0;
            $capacidad = $this->boundParams[5] ?? 0;
            $costo = $this->boundParams[6] ?? 0;
            $urlImagen = $this->boundParams[7] ?? '';
            
            $id = MockDatabase::insertarHabitacion($nombre, $categoria, $descripcion, $numHabitaciones, $disponibles, $capacidad, $costo, $urlImagen);
            $this->insert_id = $id;
            $this->result = new MockMysqliResult([]);
        }
        // Simular UPDATE de habitaciones
        else if (strpos($this->query, 'UPDATE') !== false && strpos($this->query, 'habitaciones') !== false) {
            $valor = $this->boundParams[0] ?? '';
            $id = $this->boundParams[1] ?? 0;
            
            // Extraer el campo del query
            preg_match('/SET\s+(\w+)\s*=/', $this->query, $matches);
            $campo = $matches[1] ?? '';
            
            MockDatabase::actualizarHabitacion($id, $campo, $valor);
            $this->result = new MockMysqliResult([]);
        }
        // Simular DELETE de habitaciones
        else if (strpos($this->query, 'DELETE') !== false && strpos($this->query, 'habitaciones') !== false) {
            $id = $this->boundParams[0] ?? 0;
            $eliminado = MockDatabase::eliminarHabitacion($id);
            $this->result = new MockMysqliResult([]);
            return $eliminado; // Retorna true si se eliminó correctamente
        }
        // Simular INSERT de reservaciones
        else if (strpos($this->query, 'INSERT') !== false && strpos($this->query, 'reservaciones') !== false) {
            // Para pagar.php que usa query() directamente, parseamos los valores del SQL
            if (empty($this->boundParams)) {
                // Extraer valores del query SQL directo
                preg_match("/VALUES \('([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)', '([^']*)'\)/", $this->query, $matches);
                if (count($matches) >= 7) {
                    $idHabitacion = $matches[1];
                    $idCliente = $matches[2];
                    $fechaReservacion = $matches[3];
                    $inicioEstadia = $matches[4];
                    $finEstadia = $matches[5];
                    $subtotal = $matches[6];
                    
                    $id = MockDatabase::insertarReservacion($idHabitacion, $idCliente, $fechaReservacion, $inicioEstadia, $finEstadia, $subtotal);
                    $this->insert_id = $id;
                }
            }
            $this->result = new MockMysqliResult([]);
        }
        // Simular INSERT de detalles de reservación
        else if (strpos($this->query, 'INSERT') !== false && strpos($this->query, 'detallesreservacion') !== false) {
            preg_match("/VALUES \('([^']*)', '([^']*)', '([^']*)'\)/", $this->query, $matches);
            if (count($matches) >= 4) {
                $idReserva = $matches[1];
                $formaDePago = $matches[2];
                $total = $matches[3];
                
                MockDatabase::insertarDetalleReservacion($idReserva, $formaDePago, $total);
            }
            $this->result = new MockMysqliResult([]);
        }
        // Simular UPDATE de habitaciones (disponibilidad)
        else if (strpos($this->query, 'UPDATE') !== false && strpos($this->query, 'disponibles') !== false) {
            // Extraer el ID de la habitación del query
            preg_match('/WHERE idhabitacion = (\d+)/', $this->query, $matches);
            if (isset($matches[1])) {
                $idHabitacion = $matches[1];
                // Verificar si es una operación de decremento
                if (strpos($this->query, 'disponibles = disponibles - 1') !== false) {
                    MockDatabase::actualizarHabitacion($idHabitacion, 'disponibles', -1);
                }
            }
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