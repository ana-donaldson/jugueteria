<?php
// includes/funciones.php - Funciones reutilizables

/**
 * Calcula el precio final de un producto
 * Fórmula: precio_costo + (precio_costo * ganancia%) + (precio_costo * IVA%)
 */
function calcularPrecioFinal($precioCosto, $ganancia = 10, $iva = 21) {
    $conGanancia = $precioCosto + ($precioCosto * $ganancia / 100);
    $precioFinal = $conGanancia + ($conGanancia * $iva / 100);
    return round($precioFinal, 2);
}

/**
 * Genera un código de barras automático
 * Formato: 999 + ceros hasta completar 10 dígitos + ID
 */
function generarCodigoBarras($idProducto) {
    $codigo = '999';
    $idStr = strval($idProducto);
    $ceros = 10 - strlen($idStr);
    $codigo .= str_repeat('0', $ceros) . $idStr;
    return $codigo;
}

/**
 * Valida que un campo no esté vacío
 */
function validarRequerido($campo, $nombre) {
    if (empty(trim($campo))) {
        return "El campo '$nombre' es obligatorio.";
    }
    return null;
}

/**
 * Devuelve una respuesta JSON estandarizada
 */
function respuestaJSON($exito, $mensaje, $datos = null) {
    $respuesta = [
        'exito' => $exito,
        'mensaje' => $mensaje
    ];
    if ($datos !== null) {
        $respuesta['datos'] = $datos;
    }
    header('Content-Type: application/json');
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}
?>