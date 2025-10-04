<?php
	include('../x/cuotas.php');
	
	/** Condición de pago Crédito - inicio **/
	include('../x/cuotasCredito.php');
	/** Condición de pago Crédito - fin **/
	
	session_start();
?>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="10%">N°</th>
						<th width="35%">Fecha</th>
						<th width="30%">Monto</th>
						<th width="25%">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
<?php
    if(count($_SESSION['cuotas']) > 0)
	{
		$number           = 1;
		$montoTotalCuotas = 0;
		
		foreach ($_SESSION['cuotas'] as $item) 
		{
?>
					<tr>
						<td><?php echo $number; ?></td>
						<td style="text-align:right;"><?php echo $item->getComFechaCuota(); ?></td>
						<td style="text-align:right;"><?php echo number_format($item->getComMontoCuota(), 2, '.', ''); ?></td>
						<td>
							<label onclick="cargarCuota('<?php echo $item->getId(); ?>')" class="btn btn-default btn-circle" 
									data-toggle="tooltip" title="editar">
								<i class="fa fa-pencil"></i>
							</label>
							<label onclick="eliminarCuota('<?php echo $item->getId(); ?>')" class="btn btn-danger btn-circle" 
									data-toggle="tooltip" title="eliminar">
								<i class="fa fa-times"></i>
							</label>
						</td>
					</tr>
				</tbody>
<?php 
			$montoTotalCuotas += $item->getComMontoCuota();
			$number++;
		}
		
		$montoTotalCuotas = round($montoTotalCuotas, 2);
?>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td style="text-align:right;font-weight:bold;">Monto total</td>
						<td style="text-align:right;font-weight:bold;"><?php echo number_format($montoTotalCuotas, 2, '.', ''); ?></td>
						<td>&nbsp;</td>
					</tr>
					<input type="hidden" id="monto-total-cuotas" name="monto-total-cuotas" value="<?php echo $montoTotalCuotas; ?>">
				</tfoot>
<?php 
	}
	else
	{
?>
					<tr><td colspan="4">Aún no se han agregado cuotas.</td></tr>
				</tbody>
<?php 
	}
?> 
			</table>