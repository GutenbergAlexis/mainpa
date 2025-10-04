<?php
include('../../util/database.php');
session_start();

								   
					   
							 

							 
						
				
			

// Configuración de paginación
$ventas_por_pagina = 50;
$pagina_actual = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$offset = ($pagina_actual - 1) * $ventas_por_pagina;

$codVenta       = $_POST['codVenta'];
$numDocumento   = $_POST['numDocumento'];
$fechaDesde     = $_POST['fechaDesde'];
$fechaHasta     = $_POST['fechaHasta'];
$numComprobante = $_POST['numComprobante'];

																																						  

// Consulta para el total de ventas
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
$total_ventas = mysqli_fetch_assoc($result_total)['total'];
$total_paginas = ceil($total_ventas / $ventas_por_pagina);

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

$selectVentas .= " ORDER BY com.id DESC LIMIT $offset, $ventas_por_pagina";

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
            <th class="tabla-cabecera" width="39%">Nombre/Razón social</th>
            <th class="tabla-cabecera" width="8%">Vendedor</th>
            <th class="tabla-cabecera" width="8%">Monto neto</th>
            <th class="tabla-cabecera" width="8%">Monto IGV</th>
            <th class="tabla-cabecera" width="8%">Monto total</th>
            <th class="tabla-cabecera" width="10%">&nbsp;</th>
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
					<form method="post" action="actualizar-comprobante.php">
<?php
			if($rowSelectVentas['pagado'] == 0) 
			{
				$colorPagado = "color:red;";
				$titlePagado = "no pagado";
			}
			else
			{
				$colorPagado = "color:green;";
				$titlePagado = "pagado";
			}
			
			if($rowSelectVentas['emitido'] == 0) 
			{
				$colorEmitido = "color:red;";
				$titleEmitido = "no emitido";
			}
			else
			{
				$colorEmitido = "color:green;";
				$titleEmitido = "emitido";
			}
			
			if($rowSelectVentas['entregado'] == 0) 
			{
				$colorEntregado = "color:red;";
				$titleEntregado = "no entregado";
			}
			else
			{
				$colorEntregado = "color:green;";
				$titleEntregado = "entregado";
			}
			
			if($rowSelectVentas['anulado'] == 1) 
			{
				$colorPagado    = "";
				$colorEmitido   = "";
				$colorEntregado = "";
				$titlePagado    = "anulado";
				$titleEmitido   = "anulado";
				$titleEntregado = "anulado";
				$disabled       = "disabled";
			}
			else 
			{
				$disabled       = "";
			}
?>
						<i class="fa fa-dollar" style="<?php echo $colorPagado; ?>" data-toggle="tooltip" title="<?php echo $titlePagado; ?>"></i>
						<i class="fa fa-file" style="<?php echo $colorEmitido; ?>" data-toggle="tooltip" title="<?php echo $titleEmitido; ?>"></i>
						<i class="fa fa-user" style="<?php echo $colorEntregado; ?>" data-toggle="tooltip" title="<?php echo $titleEntregado; ?>"></i>
						<button type="submit" class="btn btn-default btn-circle" data-toggle="tooltip" title="editar" <?php echo $disabled; ?> >
							<i class="fa fa-pencil"></i>
						</button>
						<input type="hidden" name="idComprobante" value="<?php echo $rowSelectVentas['id']; ?>">
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
<nav aria-label="Paginación de ventas">
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
    Mostrando <?php echo min($ventas_por_pagina, mysqli_num_rows($resultSelectVentas)); ?> de <?php echo $total_ventas; ?> ventas
    (Página <?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?>)
</div>
<?php endif; ?>