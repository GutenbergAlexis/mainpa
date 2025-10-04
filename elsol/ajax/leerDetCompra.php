<?php
	include('../x/detCompra.php');
	session_start();
	
	$data = 
			'<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th width="3%">N°</th>
						<th width="45%">Producto</th>
						<th width="10%">Un. medida</th>
						<th width="10%">Cantidad</th>
						<th width="10%">Costo unit.</th>
						<th width="10%">Costo total</th>
						<th width="8%">&nbsp;</th>
					</tr>
				</thead>
				<tbody>';
		
    if(count($_SESSION['detCompra']) > 0)
	{
		$number     = 1;
		$montototal = 0;
		$montoneto  = 0;
		$montoigv   = 0;
		
		foreach ($_SESSION['detCompra'] as $item) 
		{
			$data .= 
					'<tr>
						<td>'.$number.'</td>
						<td>'.$item->getProCodigo().' - '.$item->getProDescripcion().'</td>
						<td>'.$item->getProUnidadMedida().'</td>
						<td style="text-align:center;">'.$item->getProCantidad().'</td>
						<td style="text-align:right;">'.number_format($item->getProCostoUnitario(), 2, '.', '').'</td>
						<td style="text-align:right;">'.number_format($item->getProCostoTotal(), 2, '.', '').'</td>
						<td>
							<label onclick="cargarDetCompra('.$item->getId().')" class="btn btn-default btn-circle" 
									data-toggle="tooltip" title="editar">
								<i class="fa fa-pencil"></i>
							</label>
							<label onclick="eliminarDetCompra('.$item->getId().')" class="btn btn-danger btn-circle" 
									data-toggle="tooltip" title="eliminar">
								<i class="fa fa-times"></i>
							</label>
						</td>
					</tr>
				</tbody>';
			$montototal += $item->getProCostoTotal();
			$number++;
		}
		$montoneto = round($montototal/1.18, 2);
		$montoigv  = $montototal - $montoneto;
		
		$data .= 
				'<tfoot>
					<tr>
						<td colspan="3">&nbsp;</td>
						<td>Monto neto</td>
						<td style="text-align:right;">' .number_format($montoneto, 2, '.', ''). '</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
						<td>IGV</td>
						<td style="text-align:right;">' .number_format($montoigv, 2, '.', ''). '</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
						<td>Monto total</td>
						<td style="text-align:right;">' .number_format($montototal, 2, '.', ''). '</td>
						<td>&nbsp;</td>
					</tr>
					<input type="hidden" id="monto-neto" value="'.$montoneto.'">
					<input type="hidden" id="monto-igv" value="'.$montoigv.'">
					<input type="hidden" id="monto-total" value="'.$montototal.'">
				<tfoot>';
	}
	else
	{
		$data .= 
					'<tr><td colspan="6">Aún no se han agregado productos.</td></tr>
				</tbody>';
	}
	$data .= 
			'</table>';

    echo $data;
?>