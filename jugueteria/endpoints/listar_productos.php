<?php
// endpoints/listar_productos.php - Lista productos con paginación y búsqueda

require_once '../db.php';
require_once '../includes/respuestas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responderError('Método no permitido. Use GET.', 405);
}

// Parámetros de paginación
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$porPagina = 20;
$offset = ($pagina - 1) * $porPagina;

// Parámetro de búsqueda
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    // Armar la consulta base
    $where = "WHERE activo = 1";
    $params = [];

    // Si hay búsqueda, agregar filtro
    if (!empty($busqueda)) {
        $where .= " AND (descripcion LIKE ? OR codigo_barra LIKE ?)";
        $params[] = "%$busqueda%";
        $params[] = "%$busqueda%";
    }

    // Contar total de productos (para saber cuántas páginas hay)
    $sqlCount = "SELECT COUNT(*) as total FROM productos $where";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $total = $stmtCount->fetch()['total'];
    $totalPaginas = ceil($total / $porPagina);

    // Obtener los productos de la página actual
    $sql = "SELECT id, codigo_barra, descripcion, precio_costo, precio_final, 
                   stock, stock_minimo, familia, departamento, seccion, activo
            FROM productos 
            $where
            ORDER BY id DESC
            LIMIT $porPagina OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();

    responderExito('Productos obtenidos correctamente.', [
        'productos' => $productos,
        'paginacion' => [
            'pagina_actual' => $pagina,
            'total_paginas' => $totalPaginas,
            'total_productos' => $total,
            'por_pagina' => $porPagina
        ]
    ]);

} catch (PDOException $e) {
    responderError('Error al obtener productos: ' . $e->getMessage());
}
?>