<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	include('../x/detCompra.php');
	session_start();
	
	$idCompra = $_POST['id'];

	$selectCompra = 
		"SELECT cmp.*, pro.ruc, pro.razon_social, pro.direccion, par.descripcion AS des_med_pago
			FROM compras cmp 
			JOIN proveedores pro ON pro.id = cmp.id_proveedor 
			JOIN parametros par ON par.codigo = cmp.par_medio_pago AND par.padre = 4 
			WHERE cmp.id = '$idCompra'";

	$selectDetCompra = 
		"SELECT dcmp.id_producto, pro.descripcion AS des_producto, dcmp.cantidad, 
			dcmp.costo AS costo_unitario, dcmp.costo*dcmp.cantidad costo_total 
			FROM det_compra dcmp 
			JOIN productos pro ON pro.id = dcmp.id_producto 
			WHERE dcmp.id_compra = '$idCompra'";
	
	if (!$resultSelectCompra = mysqli_query($con, $selectCompra)) 
	{
		exit(mysqli_error($con));
	}
	
	if (!$resultSelectDetCompra = mysqli_query($con, $selectDetCompra)) 
	{
		exit(mysqli_error($con));
	}
	
    if(isset($_POST['id']))
	{
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<?php
						if(mysqli_num_rows($resultSelectCompra) > 0) {
							while ($rowSelectCompra = mysqli_fetch_assoc($resultSelectCompra)) {
					?>
					<div class="panel-body">
						<h4>RUC del proveedor</h4>
						<div class="col-lg-4 col-mb-6 col-sm-12">
							<input class="form-control" type="text" id="tip-documento" value="<?php echo $rowSelectCompra['ruc']; ?>" readonly />
						</div>
					</div>
					<div class="panel-body">
						<h4>Datos del proveedor</h4>
						<div class="col-lg-8 col-mb-8 col-sm-12">
							<input type="hidden" id="id-proveedor" />
							<label for="razon-social">Razón Social</label>
							<input class="form-control" type="text" id="razon-social" value="<?php echo utf8_encode($rowSelectCompra['razon_social']); ?>" readonly />
							<label for="direccion">Dirección</label>
							<input class="form-control" type="text" id="direccion" value="<?php echo utf8_encode($rowSelectCompra['direccion']); ?>" readonly />
							<label for="observaciones">Observaciones</label>
							<textarea class="form-control" rows="3" id="observaciones" readonly><?php echo utf8_encode($rowSelectCompra['observaciones']); ?></textarea>
						</div>
						<div class="col-lg-4 col-mb-4 col-sm-12">
							<label for="fecha-compra">Fecha de compra</label>
							<input class="form-control" type="text" id="fecha-compra" value="<?php echo $rowSelectCompra['fec_compra']; ?>" readonly />
							<label for="medio-pago">Medio de pago</label>
							<input class="form-control" type="text" id="medio-pago" value="<?php echo utf8_encode($rowSelectCompra['des_med_pago']); ?>" readonly />
						</div>
					</div>
					<?php
						}
					}
					?>
					<!-- /.col-lg-12 -->
					<div class="panel-body">
						<div id="detalle-compra">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th width="5%">N°</th>
										<th width="50%">Producto</th>
										<th width="15%">Cantidad</th>
										<th width="15%">Costo unit.</th>
										<th width="15%">Costo total</th>
									</tr>
								</thead>
								<tbody>
								<?php 
									$number        = 1;
									$costototal    = 0;
									$costoneto     = 0;
									$costoigv      = 0;
									$costoEnLetras = '-';
									
									if(mysqli_num_rows($resultSelectDetCompra) > 0) 
									{
										while ($rowSelectDetCompra = mysqli_fetch_assoc($resultSelectDetCompra)) 
										{
								?>
									<tr>
										<td><?php echo $number; ?></td>
										<td><?php echo utf8_encode($rowSelectDetCompra['des_producto']); ?></td>
										<td style="text-align:center;"><?php echo $rowSelectDetCompra['cantidad']; ?></td>
										<td style="text-align:right;"><?php echo number_format($rowSelectDetCompra['costo_unitario'], 2, '.', ''); ?></td>
										<td style="text-align:right;"><?php echo number_format($rowSelectDetCompra['costo_total'], 2, '.', ''); ?></td>
									</tr>
								<?php
											$_SESSION['rows'][$number] = $rowSelectDetCompra;
											$costototal += $rowSelectDetCompra['costo_total'];
											$number++;
										}
										$_SESSION['num-registros'] = $number;
										$costoneto = round($costototal/1.18, 2);
										$costoigv  = $costototal - $costoneto;
										
										$metodoReflex  = new ReflectionMethod('numerosALetras', 'to_word');
										$costoEnLetras = $metodoReflex->invoke(new numerosALetras(), number_format($costototal, 2, '.', ''), 'PEN');
									}
								?>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="3">Son: <?php echo $costoEnLetras; ?></td>
										<td>Costo neto</td>
										<td style="text-align:right;"><?php echo number_format($costoneto, 2, '.', ''); ?></td>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
										<td>IGV</td>
										<td style="text-align:right;"><?php echo number_format($costoigv, 2, '.', ''); ?></td>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
										<td>Costo total</td>
										<td style="text-align:right;"><?php echo number_format($costototal, 2, '.', ''); ?></td>
									</tr>
								<tfoot>
							</table>
						</div>
					</div>
					<div class="panel-body">
						<center>
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						</center>
					</div>
				</div>
			</div>
		</div>
<?php
	}
?>