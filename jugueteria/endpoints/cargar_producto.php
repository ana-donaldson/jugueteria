<?php
// endpoints/cargar_producto.php - Guarda un nuevo producto en la BD

// 1. Incluir configuración y funciones
require_once '../db.php';
require_once '../includes/funciones.php';

// 2. Verificar que los datos vengan por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respuestaJSON(false, 'Método no permitido. Use POST.');
}

// 3. Obtener los datos del formulario o de la app (JSON o formulario)
$input = json_decode(file_get_contents('php://input'), true);
if ($input) {
    // Si vienen en JSON (desde la app móvil)
    $nombre = $input['nombre'] ?? '';
    $codigoBarras = $input['codigo_barra'] ?? '';
    $precioCosto = $input['precio_costo'] ?? 0;
    $ganancia = $input['ganancia'] ?? 10;
    $iva = $input['iva'] ?? 21;
    $stock = $input['stock'] ?? 0;
    $stockMinimo = $input['stock_minimo'] ?? 0;
    $familia = $input['familia'] ?? '';
    $departamento = $input['departamento'] ?? '';
    $seccion = $input['seccion'] ?? '';
} else {
    // Si vienen de un formulario web (POST tradicional)
    $nombre = $_POST['nombre'] ?? '';
    $codigoBarras = $_POST['codigo_barra'] ?? '';
    $precioCosto = $_POST['precio_costo'] ?? 0;
    $ganancia = $_POST['ganancia'] ?? 10;
    $iva = $_POST['iva'] ?? 21;
    $stock = $_POST['stock'] ?? 0;
    $stockMinimo = $_POST['stock_minimo'] ?? 0;
    $familia = $_POST['familia'] ?? '';
    $departamento = $_POST['departamento'] ?? '';
    $seccion = $_POST['seccion'] ?? '';
}

// 4. Validar campos obligatorios
$errores = [];
$errores[] = validarRequerido($nombre, 'nombre');
$errores[] = validarRequerido($precioCosto, 'precio_costo');

// Filtrar errores vacíos
$errores = array_filter($errores);
if (!empty($errores)) {
    respuestaJSON(false, implode(' ', $errores));
}

// 5. Calcular el precio final
$precioFinal = calcularPrecioFinal($precioCosto, $ganancia, $iva);

// 6. Verificar que el código de barras no esté duplicado
if (!empty($codigoBarras)) {
    $stmt = $pdo->prepare("SELECT id FROM productos WHERE codigo_barra = ?");
    $stmt->execute([$codigoBarras]);
    if ($stmt->fetch()) {
        respuestaJSON(false, 'El código de barras ya está registrado.');
    }
}

// 7. Guardar el producto en la base de datos
try {
    $sql = "INSERT INTO productos (
        codigo_barra, descripcion, precio_costo, ganancia_porcentual, 
        iva, precio_final, stock, stock_minimo, 
        familia, departamento, seccion, activo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $activo = 1;
    $stmt->execute([
        $codigoBarras, $nombre, $precioCosto, $ganancia,
        $iva, $precioFinal, $stock, $stockMinimo,
        $familia, $departamento, $seccion, $activo
    ]);

    $idProducto = $pdo->lastInsertId();

    // 8. Si el producto no tiene código de barras, generarlo automáticamente
    if (empty($codigoBarras)) {
        $codigoBarrasGenerado = generarCodigoBarras($idProducto);
        $stmtUpdate = $pdo->prepare("UPDATE productos SET codigo_barra = ? WHERE id = ?");
        $stmtUpdate->execute([$codigoBarrasGenerado, $idProducto]);
        $codigoBarras = $codigoBarrasGenerado;
    }

    // 9. Respuesta de éxito
    respuestaJSON(true, 'Producto cargado correctamente.', [
        'id' => $idProducto,
        'codigo_barra' => $codigoBarras,
        'nombre' => $nombre,
        'precio_final' => $precioFinal
    ]);

} catch (PDOException $e) {
    respuestaJSON(false, 'Error al guardar el producto: ' . $e->getMessage());
}
?>