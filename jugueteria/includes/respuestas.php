<?php
// includes/respuestas.php - Funciones estandarizadas para respuestas JSON

/**
 * Devuelve una respuesta JSON de éxito
 */
function responderExito($mensaje, $datos = null) {
    header('Content-Type: application/json');
    $respuesta = [
        'exito' => true,
        'mensaje' => $mensaje
    ];
    if ($datos !== null) {
        $respuesta['datos'] = $datos;
    }
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Devuelve una respuesta JSON de error
 */
function responderError($mensaje, $codigo = 400) {
    http_response_code($codigo);
    header('Content-Type: application/json');
    echo json_encode([
        'exito' => false,
        'mensaje' => $mensaje
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Devuelve una respuesta JSON con datos (genérica)
 */
function responderJSON($exito, $mensaje, $datos = null) {
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