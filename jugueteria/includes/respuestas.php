<?php

function respuestaJSON($exito, $mensaje, $datos = null) {
    header('Content-Type: application/json');
    $respuesta = [
        'exito' => $exito,
        'mensaje' => $mensaje
    ];
    if ($datos !== null) {
        $respuesta['datos'] = $datos;
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}
?>
