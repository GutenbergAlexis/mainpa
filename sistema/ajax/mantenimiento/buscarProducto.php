<?php
include('../../util/database.php');
session_start();

$producto = $_POST['producto'];

if (strlen($producto) < 3) {
    echo '<div class="alert alert-warning">Ingrese al menos 3 caracteres para buscar</div>';
    exit;
}

$productosPorPagina = 50;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $productosPorPagina;

// Consulta para el total de productos
$cantidadTotal = "SELECT COUNT(*) as total 
              FROM productos pro 
              JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29";

$selectProducto = "SELECT pro.id, pro.codigo, pro.descripcion, par.abreviatura AS unidad_medida, pro.precio, pro.stock 
     FROM productos pro 
     JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29";

if (!is_null($producto) && !empty($producto)) {
    $selectProducto = $selectProducto." WHERE lower(pro.descripcion) LIKE lower('%$producto%')";
    $cantidadTotal = $cantidadTotal." WHERE lower(pro.descripcion) LIKE lower('%$producto%')";
}

$resultCantidadTotal = mysqli_query($con, $cantidadTotal);
$rowCantidadTotal = mysqli_fetch_assoc($resultCantidadTotal)['total'];
$totalPaginas = ceil($rowCantidadTotal / $productosPorPagina);

if (!$resultProducto = mysqli_query($con, $selectProducto)) {
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
if(mysqli_num_rows($resultProducto) > 0) {
    $number = $offset + 1;
    while ($rowProducto = mysqli_fetch_assoc($resultProducto)) {
        $onclickCargar   = 'onclick="cargarProducto('.$rowProducto['id'].')"'; 
        $onclickEliminar = 'onclick="eliminarProducto('.$rowProducto['id'].')"';
        $disabled        = '';
        
        if ($_SESSION['perfil'] == 1 || $_SESSION['perfil'] == 2) {
            $onclickCargar   = '';
            $onclickEliminar = '';
            $disabled        = 'disabled';
        }
?>
        <tr>
            <td class="centrar tabla-detalle"><?php echo $number; ?></td>
            <td class="centrar tabla-detalle"><?php echo $rowProducto['codigo']; ?></td>
            <td class="izquierda tabla-detalle"><?php echo mb_convert_encoding($rowProducto['descripcion'], 'UTF-8', 'ISO-8859-1'); ?></td>
            <td class="centrar tabla-detalle"><?php echo mb_convert_encoding($rowProducto['unidad_medida'], 'UTF-8', 'ISO-8859-1'); ?></td>
            <td class="derecha tabla-detalle"><?php echo $rowProducto['stock']; ?></td>
            <td class="derecha tabla-detalle"><?php echo number_format($rowProducto['precio'], 2, '.', ''); ?></td>
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
<?php if ($totalPaginas > 1): ?>
<nav aria-label="Paginación de productos">
    <ul class="pagination">
        <!-- Botón Anterior -->
        <?php if ($paginaActual > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- Números de página -->
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
                <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Botón Siguiente -->
        <?php if ($paginaActual < $totalPaginas): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?>" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<div class="text-center text-muted">
    Mostrando <?php echo min($productosPorPagina, mysqli_num_rows($resultProducto)); ?> de <?php echo $rowCantidadTotal; ?> productos
    (Página <?php echo $paginaActual; ?> de <?php echo $totalPaginas; ?>)
</div>
<?php endif; ?>