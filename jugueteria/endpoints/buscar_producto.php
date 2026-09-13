<?php
// endpoints/buscar_producto.php - Busca un producto por código de barras o nombre

require_once '../db.php';
require_once '../includes/funciones.php';
require_once '../includes/respuestas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respuestaJSON(false, 'Método no permitido. Use GET.');
}

$busqueda = $_GET['codigo'] ?? $_GET['nombre'] ?? '';

if (empty($busqueda)) {
    respuestaJSON(false, 'Debe enviar un código de barras o nombre.');
}

try {
    // Buscar por código de barras
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE codigo_barra = ? ");
    $stmt->execute([$busqueda]);
    $producto = $stmt->fetch();

if (!$producto) {
    responderError('Producto no encontrado.');
}

if ($producto['activo'] == 0) {
    responderError('El producto existe pero está inactivo.');
}

responderExito('Producto encontrado.', $producto);

    // Si no se encuentra por código, buscar por nombre (coincidencia parcial)
    if (!$producto) {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE descripcion LIKE ? LIMIT 10");
        $stmt->execute(['%' . $busqueda . '%']);
        $productos = $stmt->fetchAll();
        if (count($productos) === 1) {
            $producto = $productos[0];
        } else if (count($productos) > 1) {
            respuestaJSON(false, 'Se encontraron varios productos con ese nombre. Sea más específico.', $productos);
            return;
        }
    }

    if (!$producto) {
        respuestaJSON(false, 'Producto no encontrado.');
    }

    respuestaJSON(true, 'Producto encontrado.', $producto);

} catch (PDOException $e) {
    respuestaJSON(false, 'Error al buscar producto: ' . $e->getMessage());
}
?>