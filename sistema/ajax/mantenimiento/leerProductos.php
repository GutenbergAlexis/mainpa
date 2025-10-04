<?php
include('../../util/database.php');
session_start();

// Configuración de paginación
$productos_por_pagina = 50;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $productos_por_pagina;

// Consulta para el total de productos
$sql_total = "SELECT COUNT(*) as total 
              FROM productos pro 
              JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29";
$result_total = mysqli_query($con, $sql_total);
$total_productos = mysqli_fetch_assoc($result_total)['total'];
$total_paginas = ceil($total_productos / $productos_por_pagina);

// Consulta principal con LIMIT
$selectProductos = 
    "SELECT pro.id, pro.codigo, pro.descripcion, par.abreviatura AS unidad_medida, pro.precio, pro.stock 
     FROM productos pro 
     JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29 
     ORDER BY pro.id ASC 
     LIMIT $offset, $productos_por_pagina";

if (!$resultSelectProductos = mysqli_query($con, $selectProductos)) {
    exit(mysqli_error($con));
}
?>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th class="tabla-cabecera" width="3%">N°</th>
            <th class="tabla-cabecera" width="12%">Código</th>
            <th class="tabla-cabecera" width="40%">Descripción</th>
            <th class="tabla-cabecera" width="12%">Unidad de medida</th>
            <th class="tabla-cabecera" width="12%">Stock</th>
            <th class="tabla-cabecera" width="12%">Precio unitario</th>
            <th class="tabla-cabecera" width="9%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
<?php 
if(mysqli_num_rows($resultSelectProductos) > 0) {
    $number = $offset + 1;
    while ($rowSelectProductos = mysqli_fetch_assoc($resultSelectProductos)) {
        $onclickCargar   = 'onclick="cargarProducto('.$rowSelectProductos['id'].')"'; 
        $onclickEliminar = 'onclick="eliminarProducto('.$rowSelectProductos['id'].')"';
        $disabled        = '';
        
        if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 2) {
            $onclickCargar   = '';
            $onclickEliminar = '';
            $disabled        = 'disabled';
        }
?>
        <tr>
            <td class="centrar tabla-detalle"><?php echo $number; ?></td>
            <td class="centrar tabla-detalle"><?php echo $rowSelectProductos['codigo']; ?></td>
            <td class="izquierda tabla-detalle"><?php echo mb_convert_encoding($rowSelectProductos['descripcion'], 'UTF-8', 'ISO-8859-1'); ?></td>
            <td class="centrar tabla-detalle"><?php echo mb_convert_encoding($rowSelectProductos['unidad_medida'], 'UTF-8', 'ISO-8859-1'); ?></td>
            <td class="derecha tabla-detalle"><?php echo $rowSelectProductos['stock']; ?></td>
            <td class="derecha tabla-detalle"><?php echo number_format($rowSelectProductos['precio'], 2, '.', ''); ?></td>
            <td class="centrar tabla-detalle">
                <label <?php echo $onclickCargar; ?> class="btn btn-default btn-circle" data-toggle="tooltip" title="editar" <?php echo $disabled; ?> >
                    <i class="fa fa-pencil"></i>
                </label>
                <label <?php echo $onclickEliminar; ?> class="btn btn-danger btn-circle" data-toggle="tooltip" title="eliminar" <?php echo $disabled; ?> >
                    <i class="fa fa-times"></i>
                </label>
            </td>
        </tr>
<?php 
        $number++;
    }
} else {
?>
        <tr><td colspan="7">No existen productos.</td></tr>
<?php 
}
?>
    </tbody>
</table>

<!-- Controles de Paginación -->
<?php if ($total_paginas > 1): ?>
<nav aria-label="Paginación de productos">
    <ul class="pagination">
        <!-- Botón Anterior -->
        <?php if ($pagina_actual > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Números de página -->
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Botón Siguiente -->
        <?php if ($pagina_actual < $total_paginas): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<div class="text-center text-muted">
    Mostrando <?php echo min($productos_por_pagina, mysqli_num_rows($resultSelectProductos)); ?> de <?php echo $total_productos; ?> productos
    (Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>)
</div>
<?php endif; ?>