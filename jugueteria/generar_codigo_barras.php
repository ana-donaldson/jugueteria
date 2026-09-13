<?php
// endpoints/generar_codigo_barras.php - Genera un código de barras automático

require_once '../includes/funciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respuestaJSON(false, 'Método no permitido. Use GET.');
}

$id = $_GET['id'] ?? null;

if (!$id) {
    respuestaJSON(false, 'Debe enviar el ID del producto.');
}

$codigo = generarCodigoBarras($id);

respuestaJSON(true, 'Código generado correctamente.', [
    'codigo_barras' => $codigo
]);
?>