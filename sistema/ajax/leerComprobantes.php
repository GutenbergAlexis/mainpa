<?php
include('../../util/database.php');
session_start();

// Configuración de paginación
$comprobantes_por_pagina = 50;
$pagina_actual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$offset = ($pagina_actual - 1) * $comprobantes_por_pagina;

$codVenta       = $_POST['codVenta'];
$numDocumento   = $_POST['numDocumento'];
$fechaDesde     = $_POST['fechaDesde'];
$fechaHasta     = $_POST['fechaHasta'];
$numComprobante = $_POST['numComprobante'];

// Consulta para el total de comprobantes
$sql_total = 
    "SELECT COUNT(*) as total 
     FROM comprobantes com 
     JOIN clientes cli ON cli.id = com.id_cliente 
     WHERE 1=1";

if (!is_null($codVenta) && !empty($codVenta)) {
    $sql_total .= " AND com.id = '$codVenta'";
}

if (!is_null($numDocumento) && !empty($numDocumento)) {
    $sql_total .= " AND cli.num_documento = '$numDocumento'";
}

if (!is_null($fechaDesde) && !empty($fechaDesde)) {
    $sql_total .= " AND com.fec_emision >= str_to_date('$fechaDesde', '%d/%m/%Y')";
} else {
    $sql_total .= " AND com.fec_emision >= date_sub(NOW(), INTERVAL 1 DAY)";
}

if (!is_null($fechaHasta) && !empty($fechaHasta)) {
    $sql_total .= " AND com.fec_emision <= str_to_date('$fechaHasta', '%d/%m/%Y')";
}

if (!is_null($numComprobante) && !empty($numComprobante)) {
    $expNumComprobante = explode("-", $numComprobante.'-');
    $serieComprobante  = $expNumComprobante[0];
    $numeroComprobante = $expNumComprobante[1];
    
    $sql_total .= " AND com.ser_comprobante = '$serieComprobante' AND com.num_comprobante = '$numeroComprobante'";
}

$result_total = mysqli_query($con, $sql_total);
$total_comprobantes = mysqli_fetch_assoc($result_total)['total'];
$total_paginas = ceil($total_comprobantes / $comprobantes_por_pagina);

// Consulta principal con LIMIT
$selectVentas = 
    "SELECT com.*, cli.num_documento, 
        IF(com.num_comprobante > 0, CONCAT(com.ser_comprobante, '-', LPAD(com.num_comprobante, 6, '0')), '') AS num_comprobante, 
        CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido) AS nombre_razon_social 
        FROM comprobantes com 
        JOIN clientes cli ON cli.id = com.id_cliente 
        WHERE 1=1";

// Agregar las mismas condiciones del total
if (!is_null($codVenta) && !empty($codVenta)) {
    $selectVentas .= " AND com.id = '$codVenta'";
}

if (!is_null($numDocumento) && !empty($numDocumento)) {
    $selectVentas .= " AND cli.num_documento = '$numDocumento'";
}

if (!is_null($fechaDesde) && !empty($fechaDesde)) {
    $selectVentas .= " AND com.fec_emision >= str_to_date('$fechaDesde', '%d/%m/%Y')";
} else {
    $selectVentas .= " AND com.fec_emision >= date_sub(NOW(), INTERVAL 1 DAY)";
}

if (!is_null($fechaHasta) && !empty($fechaHasta)) {
    $selectVentas .= " AND com.fec_emision <= str_to_date('$fechaHasta', '%d/%m/%Y')";
}

if (!is_null($numComprobante) && !empty($numComprobante)) {
    $expNumComprobante = explode("-", $numComprobante.'-');
    $serieComprobante  = $expNumComprobante[0];
    $numeroComprobante = $expNumComprobante[1];
    
    $selectVentas .= " AND com.ser_comprobante = '$serieComprobante' AND com.num_comprobante = '$numeroComprobante'";
}

$selectVentas .= " ORDER BY com.id DESC LIMIT $offset, $comprobantes_por_pagina";

if (!$resultSelectVentas = mysqli_query($con, $selectVentas)) {
    exit(mysqli_error($con));
}
?>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th class="tabla-cabecera" width="3%">N°</th>
            <th class="tabla-cabecera" width="8%">Comprobante</th>
            <th class="tabla-cabecera" width="8%">Fecha de emisión</th>
            <th class="tabla-cabecera" width="32%">Nombre/Razón Social</th>
            <th class="tabla-cabecera" width="8%">Vendedor</th>
            <th class="tabla-cabecera" width="8%">Monto neto</th>
            <th class="tabla-cabecera" width="8%">Monto IGV</th>
            <th class="tabla-cabecera" width="8%">Monto total</th>
            <th class="tabla-cabecera" width="17%">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
<?php 
if(mysqli_num_rows($resultSelectVentas) > 0) {
    $number = $offset + 1;
    while ($rowSelectVentas = mysqli_fetch_assoc($resultSelectVentas)) {
?>
        <tr>
            <td class="centrar tabla-detalle"><?php echo $number; ?></td>
            <td class="centrar tabla-detalle"><?php echo $rowSelectVentas['num_comprobante']; ?></td>
            <td class="izquierda tabla-detalle"><?php echo date("d.m.Y", strtotime($rowSelectVentas['fec_emision'])); ?></td>
            <td class="izquierda tabla-detalle"><?php echo mb_convert_encoding($rowSelectVentas['num_documento'].' - '.$rowSelectVentas['nombre_razon_social'], 'UTF-8', 'ISO-8859-1'); ?></td>
            <td class="izquierda tabla-detalle"><?php echo $rowSelectVentas['usu_creacion']; ?></td>
            <td class="derecha tabla-detalle"><?php echo number_format($rowSelectVentas['mon_neto'] , 2, '.', ''); ?></td>
            <td class="derecha tabla-detalle"><?php echo number_format($rowSelectVentas['mon_igv']  , 2, '.', ''); ?></td>
            <td class="derecha tabla-detalle"><?php echo number_format($rowSelectVentas['mon_total'], 2, '.', ''); ?></td>
            <td class="centrar tabla-detalle">
                <form method="post" action="../../ajax/comprobantes/verTicketPDF.php" target="_blank" >
<?php 
        if ($rowSelectVentas['anulado'] == 0) {
            if ($rowSelectVentas['entregado'] == 0) {
?>
                    <i class="fa fa-user" style="color:red;" data-toggle="tooltip" title="no entregado"></i>
<?php 
            } else {
?>
                    <i class="fa fa-user" style="color:green;" data-toggle="tooltip" title="entregado"></i>
<?php 
            }
        } else {
?>
                    <i class="fa fa-user" data-toggle="tooltip" title="anulado"></i>
<?php 
        }
?>
                    <button type="button" class="btn btn-default btn-circle" onclick="cargarComprobante('<?php echo $rowSelectVentas['id']; ?>')" data-toggle="tooltip" title="ver">
                        <i class="fa fa-eye"></i>
                    </button>
<?php 
        if ($rowSelectVentas['emitido'] == 0) {
?>
                    <button type="button" class="btn btn-default btn-circle" style="color:red;" data-toggle="tooltip" title="no emitido" disabled>
                        <i class="fa fa-file"></i>
                    </button>
<?php 
        } else {
            if ($rowSelectVentas['tip_comprobante'] == 5) {
?>
                    <button type="submit" class="btn btn-default btn-circle" style="color:green;" data-toggle="tooltip" title="ver PDF">
                        <i class="fa fa-file"></i>
                    </button>
                    <input type="hidden" id="id-comprobante" name="id-comprobante" value="<?php echo $rowSelectVentas['id']; ?>">
<?php 
            } else {
?>
                    <button type="button" class="btn btn-default btn-circle" style="color:green;" onclick="window.open('<?php echo $rowSelectVentas['url']; ?>');" data-toggle="tooltip" title="ver PDF">
                        <i class="fa fa-file"></i>
                    </button>
<?php 
            }
        }
        if ($rowSelectVentas['anulado'] == 0) {
            if ($rowSelectVentas['pagado'] == 0) {
?>
                    <button type="button" class="btn btn-default btn-circle" style="color:red;" onclick="pagarComprobante('<?php echo $rowSelectVentas['id']; ?>')" data-toggle="tooltip" title="pagar">
                        <i class="fa fa-dollar"></i>
                    </button>
<?php 
            } else {
?>
                    <button type="button" class="btn btn-default btn-circle" style="color:green;" data-toggle="tooltip" title="pagado" disabled>
                        <i class="fa fa-dollar"></i>
                    </button>
<?php 
            }
        } else {
?>
                    <button type="button" class="btn btn-default btn-circle" data-toggle="tooltip" title="anulado" disabled>
                        <i class="fa fa-dollar"></i>
                    </button>
<?php 
        }
        if ($rowSelectVentas['emitido'] == 1) {
            if ($rowSelectVentas['anulado'] == 0) {
?>
                    <button type="button" class="btn btn-default btn-circle" style="color:red;" onclick="anularComprobante('<?php echo $rowSelectVentas['id']; ?>')" data-toggle="tooltip" title="anular">
                        <i class="fa fa-arrow-down"></i>
                    </button>
<?php 
            } else {
?>
                    <button type="button" class="btn btn-default btn-circle" data-toggle="tooltip" title="anulado" disabled>
                        <i class="fa fa-arrow-down"></i>
                    </button>
<?php 
            }
        } else {
?>
                    <button type="button" class="btn btn-default btn-circle" disabled>
                        <i class="fa fa-arrow-down"></i>
                    </button>
<?php
        }
?>
                </form>
            </td>
        </tr>
<?php 
        $number++;
    }
} else { 
?>
        <tr><td colspan="9">No existen comprobantes guardados.</td></tr>
<?php 
}
?>
    </tbody>
</table>

<!-- Controles de Paginación -->
<?php if ($total_paginas > 1): ?>
<nav aria-label="Paginación de comprobantes">
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
    Mostrando <?php echo min($comprobantes_por_pagina, mysqli_num_rows($resultSelectVentas)); ?> de <?php echo $total_comprobantes; ?> comprobantes
    (Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>)
</div>
<?php endif; ?>