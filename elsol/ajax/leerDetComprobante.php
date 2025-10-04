<?php
	include('../util/numerosALetras.php');
	include('../x/detComprobante.php');
	session_start();
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="3%">N°</th>
						<th width="45%">Producto</th>
						<th width="10%">Un. medida</th>
						<th width="10%">Cantidad</th>
						<th width="10%">Precio unit.</th>
						<th width="10%">Precio total</th>
						<th width="8%">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
<?php
    if(count($_SESSION['detComprobante']) > 0)
	{
		$number     = 1;
		$montototal = 0;
		$montoneto  = 0;
		$montoigv   = 0;
		
		foreach ($_SESSION['detComprobante'] as $item) 
		{
?>
					<tr>
						<td><?php echo $number; ?></td>
<?php
			if ($item->getProCodigoUnidadMedida() == 3)
			{
?>
						<td><?php echo $item->getProCodigo().' - '.$item->getProDescripcion().' - '.$item->getProCantidad().' - '.$item->getProEspesor().'X'.$item->getProAncho().'X'.$item->getProLargo(); ?></td>
<?php
			}
			else 
			{
?>
						<td><?php echo $item->getProCodigo().' - '.$item->getProDescripcion(); ?></td>
<?php 
			}
?>	
						<td><?php echo $item->getProUnidadMedida(); ?></td>
						<td style="text-align:center;"><?php echo $item->getProCantidadFinal(); ?></td>
						<td style="text-align:right;"><?php echo number_format($item->getProPrecioUnitario(), 2, '.', ''); ?></td>
						<td style="text-align:right;"><?php echo number_format($item->getProPrecioTotal(), 2, '.', ''); ?></td>
						<td>
							<label onclick="cargarDetComprobante('<?php echo $item->getId(); ?>')" class="btn btn-default btn-circle" 
									data-toggle="tooltip" title="editar">
								<i class="fa fa-pencil"></i>
							</label>
							<label onclick="eliminarDetComprobante('<?php echo $item->getId(); ?>')" class="btn btn-danger btn-circle" 
									data-toggle="tooltip" title="eliminar">
								<i class="fa fa-times"></i>
							</label>
						</td>
					</tr>
				</tbody>
<?php 
			$montototal += $item->getProPrecioTotal();
			$number++;
		}
		
		$montototal = round($montototal, 2);
		$montoneto  = round($montototal/1.18, 2);
		$montoigv   = $montototal - $montoneto;
		
		$metodoReflexionado = new ReflectionMethod('numerosALetras', 'to_word');
		$montoEnLetras = $metodoReflexionado->invoke(new numerosALetras(), number_format($montototal, 2, '.', ''), 'PEN');
?>
				<tfoot>
					<tr>
						<td colspan="4">Son: <?php echo $montoEnLetras; ?></td>
						<td style="text-align:right;">Monto neto</td>
						<td style="text-align:right;"><?php echo number_format($montoneto, 2, '.', ''); ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
						<td style="text-align:right;">IGV</td>
						<td style="text-align:right;"><?php echo number_format($montoigv, 2, '.', ''); ?></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
						<td style="text-align:right;font-weight:bold;">Monto total</td>
						<td style="text-align:right;font-weight:bold;"><?php echo number_format($montototal, 2, '.', ''); ?></td>
						<td>&nbsp;</td>
					</tr>
					<input type="hidden" id="monto-neto" value="<?php echo $montoneto; ?>" />
					<input type="hidden" id="monto-igv" value="<?php echo $montoigv; ?>" />
					<input type="hidden" id="monto-total" value="<?php echo $montototal; ?>" />
				</tfoot>
<?php 
	}
	else
	{
?>
					<tr><td colspan="7">Aún no se han agregado productos.</td></tr>
				</tbody>
<?php 
	}
?> 
			</table>