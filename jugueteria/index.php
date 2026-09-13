<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de Productos - Juguetería</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedor">
        <h1>Sistema de Gestión - Juguetería</h1>

        <h2>Carga de Producto</h2>

        <div id="mensaje" class="mensaje"></div>

        <form id="formProducto">
            <div class="grupo">
                <label for="nombre">Nombre del producto *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="campo-codigo">
                <div class="grupo">
                    <label for="codigo_barra">Código de barras (opcional)</label>
                    <input type="text" id="codigo_barra" name="codigo_barra" 
                           placeholder="Dejar vacío para generar automático">
                </div>
                <button type="button" class="btn btn-generar" id="btnGenerar">
                    Generar
                </button>
            </div>

            <div class="fila" style="margin-top: 15px;">
                <div class="grupo">
                    <label for="precio_costo">Precio costo *</label>
                    <input type="number" id="precio_costo" name="precio_costo" 
                           step="0.01" min="0" required>
                </div>
                <div class="grupo">
                    <label for="ganancia">Ganancia (%)</label>
                    <input type="number" id="ganancia" name="ganancia" 
                           value="10" min="0">
                </div>
                <div class="grupo">
                    <label for="iva">IVA (%)</label>
                    <input type="number" id="iva" name="iva" 
                           value="21" min="0">
                </div>
            </div>

            <div class="fila">
                <div class="grupo">
                    <label for="stock">Stock inicial</label>
                    <input type="number" id="stock" name="stock" value="0" min="0">
                </div>
                <div class="grupo">
                    <label for="stock_minimo">Stock mínimo</label>
                    <input type="number" id="stock_minimo" name="stock_minimo" value="0" min="0">
                </div>
            </div>

            <div class="fila">
                <div class="grupo">
                    <label for="familia">Familia</label>
                    <input type="text" id="familia" name="familia">
                </div>
                <div class="grupo">
                    <label for="departamento">Departamento</label>
                    <input type="text" id="departamento" name="departamento">
                </div>
                <div class="grupo">
                    <label for="seccion">Sección</label>
                    <input type="text" id="seccion" name="seccion">
                </div>
            </div>

            <div class="acciones">
                <button type="submit" class="btn btn-primario">Guardar Producto</button>
                <button type="reset" class="btn btn-secundario">Limpiar</button>
            </div>
        </form>

        <div id="infoGenerada" class="info-generada"></div>

        <div style="margin-top: 40px;">
            <h2>Productos Cargados</h2>

            <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                <input type="text" id="busqueda" placeholder="Buscar por nombre o código de barras..." 
                       style="flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="button" class="btn btn-primario" id="btnBuscar">Buscar</button>
                <button type="button" class="btn btn-secundario" id="btnLimpiar">Limpiar</button>
                <button type="button" class="btn btn-secundario" id="btnRecargar">Recargar</button>
            </div>

            <table id="tablaProductos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código de Barras</th>
                        <th>Nombre</th>
                        <th>Precio Costo</th>
                        <th>Precio Final</th>
                        <th>Stock</th>
                        <th>Familia</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="cuerpoTabla">
                    <tr>
                        <td colspan="8" style="text-align: center; color: #999;">
                            Cargando productos...
                        </td>
                    </tr>
                </tbody>
            </table>

            <div id="paginacion" style="margin-top: 15px; display: flex; justify-content: center; align-items: center; gap: 10px;">
                <button type="button" class="btn btn-secundario" id="btnAnterior">← Anterior</button>
                <span id="infoPagina" style="color: #555;">Página 1</span>
                <button type="button" class="btn btn-secundario" id="btnSiguiente">Siguiente →</button>
            </div>
        </div>

    </div>

    <script>
        let paginaActual = 1;
        let totalPaginas = 1;

        function cargarProductos(pagina = 1) {
            paginaActual = pagina;
            const cuerpo = document.getElementById('cuerpoTabla');
            const busqueda = document.getElementById('busqueda').value.trim();

            cuerpo.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #999;">Cargando productos...</td></tr>`;

            let url = `endpoints/listar_productos.php?pagina=${pagina}`;
            if (busqueda) {
                url += `&busqueda=${encodeURIComponent(busqueda)}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data.exito) {
                        cuerpo.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #721c24;">Error: ${data.mensaje}</td></tr>`;
                        return;
                    }

                    const productos = data.datos.productos;
                    const pag = data.datos.paginacion;

                    // Convertir a número para que las comparaciones funcionen
                    const paginaActualNum = parseInt(pag.pagina_actual) || 1;
                    const totalPaginasNum = parseInt(pag.total_paginas) || 1;
                    totalPaginas = totalPaginasNum;

                    if (productos.length === 0) {
                        cuerpo.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #999;">No se encontraron productos.</td></tr>`;
                        document.getElementById('infoPagina').textContent = 'Sin resultados';
                        document.getElementById('btnAnterior').disabled = true;
                        document.getElementById('btnSiguiente').disabled = true;
                        return;
                    }

                    let filas = '';
                    productos.forEach(p => {
                        filas += `
                            <tr>
                                <td>${p.id}</td>
                                <td><strong>${p.codigo_barra || 'Sin código'}</strong></td>
                                <td>${p.descripcion}</td>
                                <td>$${parseFloat(p.precio_costo).toFixed(2)}</td>
                                <td>$${parseFloat(p.precio_final).toFixed(2)}</td>
                                <td>${p.stock}</td>
                                <td>${p.familia || '-'}</td>
                                <td>${p.activo == 1 ? 'Activo' : 'Inactivo'}</td>
                            </tr>
                        `;
                    });
                    cuerpo.innerHTML = filas;

                    document.getElementById('infoPagina').textContent = 
                        `Página ${paginaActualNum} de ${totalPaginasNum} (${pag.total_productos} productos)`;

                    document.getElementById('btnAnterior').disabled = (paginaActualNum <= 1);
                    document.getElementById('btnSiguiente').disabled = (paginaActualNum >= totalPaginasNum);
                })
                .catch(err => {
                    cuerpo.innerHTML = `<tr><td colspan="8" style="text-align: center; color: #721c24;">Error de conexión: ${err.message}</td></tr>`;
                });
        }

        document.addEventListener('DOMContentLoaded', () => cargarProductos(1));

        document.getElementById('btnRecargar').addEventListener('click', () => cargarProductos(1));
        document.getElementById('btnBuscar').addEventListener('click', () => cargarProductos(1));

        document.getElementById('busqueda').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') cargarProductos(1);
        });

        document.getElementById('btnLimpiar').addEventListener('click', () => {
            document.getElementById('busqueda').value = '';
            cargarProductos(1);
        });

        document.getElementById('btnAnterior').addEventListener('click', () => {
            if (paginaActual > 1) cargarProductos(paginaActual - 1);
        });

        document.getElementById('btnSiguiente').addEventListener('click', () => {
            if (paginaActual < totalPaginas) cargarProductos(paginaActual + 1);
        });

        document.getElementById('btnGenerar').addEventListener('click', function() {
            fetch('endpoints/generar_codigo_barras.php?id=' + Math.floor(Math.random() * 9000 + 1000))
                .then(res => res.json())
                .then(data => {
                    if (data.exito) {
                        document.getElementById('codigo_barra').value = data.datos.codigo_barra;
                        
                        const info = document.getElementById('infoGenerada');
                        info.innerHTML = `
                            <p><strong>Código generado:</strong> ${data.datos.codigo_barra}</p>
                            <p>Este código se guardará cuando hagas clic en "Guardar Producto".</p>
                        `;
                        info.classList.add('visible');
                    }
                })
                .catch(err => console.error('Error:', err));
        });

        document.getElementById('formProducto').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const mensaje = document.getElementById('mensaje');

            fetch('endpoints/cargar_producto.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                mensaje.className = 'mensaje';
                if (data.exito) {
                    mensaje.classList.add('exito');
                    mensaje.textContent = ' ' + data.mensaje;
                    
                    if (data.datos) {
                        const info = document.getElementById('infoGenerada');
                        info.innerHTML = `
                            <p><strong>ID:</strong> ${data.datos.id}</p>
                            <p><strong>Código de barras:</strong> ${data.datos.codigo_barra}</p>
                            <p><strong>Nombre:</strong> ${data.datos.nombre}</p>
                            <p><strong>Precio final:</strong> $${data.datos.precio_final}</p>
                        `;
                        info.classList.add('visible');
                    }
                    
                    this.reset();
                    cargarProductos(1);
                } else {
                    mensaje.classList.add('error');
                    mensaje.textContent = '❌ ' + data.mensaje;
                }
            })
            .catch(err => {
                mensaje.className = 'mensaje error';
                mensaje.textContent = '❌ Error de conexión: ' + err.message;
            });
        });
    </script>
</body>
</html>