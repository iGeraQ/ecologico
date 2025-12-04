<?php
require_once (__DIR__."/../../config.inc.php");
include_once "config/config.inc.php";

function agregarReservacionDB($idHabitacion, $idCliente, $fechaReservacion, $inicioEstadia, $finEstadia, $subtotal) {
    $conexionSql = new Conexiondb();
    $conexionSql = $conexionSql->getConnection();

    $peticion = "INSERT INTO reservaciones (idHabitacion, idCliente, fechaReservacion, inicioEstadia, finEstadia, subtotal) 
                 VALUES ('$idHabitacion', '$idCliente', '$fechaReservacion', '$inicioEstadia', '$finEstadia', '$subtotal')";

    if ($conexionSql->query($peticion) === TRUE) {
        $idReserva = $conexionSql->insert_id;
        $respuesta = "Reservación agregada correctamente.";
    } else {
        $respuesta = "Error al agregar la reservación: " . $conexionSql->error;
        $conexionSql->close();
        return $respuesta;
    }

    $conexionSql->close();

    actualizarDisponibilidad($idHabitacion, $idReserva, $subtotal);

    return $respuesta;
}


function actualizarDisponibilidad($idHabitacion, $idReserva, $subtotal) {
    $conexionSql = new Conexiondb();
    $conexionSql = $conexionSql->getConnection();

    $peticion = "UPDATE habitaciones SET disponibles = disponibles - 1 WHERE idhabitacion = $idHabitacion";

    if ($conexionSql->query($peticion) === TRUE) {
        $respuesta = "Disponibilidad actualizada.";
    } else {
        $respuesta = "Error al actualizar disponibilidad: " . $conexionSql->error;
        $conexionSql->close();
        return $respuesta;
    }

    $peticion = "INSERT INTO detallesreservacion (idReserva, formaDePago, total) 
                 VALUES ('$idReserva', 'credito', '$subtotal')";

    if ($conexionSql->query($peticion) === TRUE) {
        $respuesta .= " Detalles de la reservación agregados correctamente.";
    } else {
        $respuesta .= " Error al agregar detalles: " . $conexionSql->error;
    }

    $conexionSql->close();

    return $respuesta;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idHabitacion = $_POST['idHabitacion'];
    $idCliente = $_POST['idCliente'];
    $fechaReservacion = $_POST['fechaReservacion'];
    $inicioEstadia = $_POST['inicioEstadia'];
    $finEstadia = $_POST['finEstadia'];
    $subtotal = $_POST['subtotal'];

    $resultado = agregarReservacionDB($idHabitacion, $idCliente, $fechaReservacion, $inicioEstadia, $finEstadia, $subtotal);

    echo json_encode(["mensaje" => $resultado]);
}
?>
