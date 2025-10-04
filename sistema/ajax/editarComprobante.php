<?php
	include('../util/database.php');
	include('../util/numerosALetras.php');
	include('../x/detComprobante.php');
	session_start();

	$queryComprobante = 
			"SELECT com.id, com.id_cliente, cli.tip_documento, cli.num_documento, par2.descripcion AS des_tipo_documento, 
				CASE cli.tip_documento
				WHEN 6
				THEN CONCAT_WS(' ', cli.nombre_razon_social, cli.seg_nombre, cli.pri_apellido, cli.seg_apellido)
				WHEN 1
				THEN cli.nombre_razon_social 
				END AS nombre_razon_social, 
				cli.direccion,
				com.tip_comprobante, par1.descripcion AS des_comprobante, par3.descripcion AS des_med_pago, 
				com.guia_remision, DATE_FORMAT(com.fec_emision, '%d/%m/%Y') AS fec_emision, com.ord_compra, 
				com.observaciones 
				FROM comprobantes com
				JOIN clientes cli ON cli.id = com.id_cliente
				JOIN parametros par1 ON par1.codigo = com.tip_comprobante AND par1.padre = 8
				JOIN parametros par2 ON par2.codigo = cli.tip_documento AND par2.padre = 12
				LEFT JOIN parametros par3 ON par3.codigo = com.par_medio_pago AND par3.padre = 4
				WHERE com.id = " .$_POST['id'];

	$queryDetComprobante = 
			"SELECT dcom.id, dcom.id_comprobante, dcom.id_producto, pro.descripcion AS des_producto, dcom.cantidad, 
				dcom.precio AS precio_unitario, dcom.precio*dcom.cantidad precio_total
				FROM det_comprobante dcom
				JOIN productos pro ON pro.id = dcom.id_producto
				WHERE dcom.id_comprobante = " .$_POST['id'];
	
	if (!$resultComprobante = mysqli_query($con, $queryComprobante)) {
		exit(mysqli_error($con));
	}
	
	if (!$resultDetComprobante = mysqli_query($con, $queryDetComprobante)) {
		exit(mysqli_error($con));
	}
	
    if(isset($_POST['id'])) {
		$idComprobante = $_POST['id'];

		$data = '
				<div class="modal-dialog" style="width:60% !important; overflow-y: initial !important;" role="document" >
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
							
							if(mysqli_num_rows($resultComprobante) > 0) 
							{
								while ($row = mysqli_fetch_assoc($resultComprobante)) 
								{
							
		$data .=			'<h4 class="modal-title" id="myModalLabel">' .$row['des_comprobante']. '</h4>
							<div class="panel-body">
								<div class="col-lg-4 col-mb-6 col-sm-12">
									<label for="numero-documento">Núm. documento</label>
									<input class="form-control" type="text" id="numero-documento" value="' .$row['num_documento']. '" />
									<input type="hidden" id="id-comprobante" name="id-comprobante" />
									<input type="hidden" id="id-cliente" name="id-cliente" value="' .$row['id_cliente']. '" />
								</div>
								<div class="col-lg-2 col-mb-6 col-sm-12">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-default" data-toggle="modal" onclick="buscarCliente()" title="buscar">
									<i class="fa fa-search"></i> Buscar</button>
								</div>
							</div>
							<div class="panel-body">
								<div class="col-lg-8 col-mb-8 col-sm-12">
									<label for="nombre">Nombre/Razón Social</label>
									<input class="form-control" type="text" name="nombre" id="nombre" value="' .$row['nombre_razon_social']. '" readonly />
									<label for="direccion">Dirección</label>
									<input class="form-control" type="text" name="direccion" id="direccion" value="' .$row['direccion']. '" readonly />
									<label for="guia-remision">Guía de remisión</label>
									<input class="form-control" type="text" name="guia-remision" id="guia-remision" value="' .$row['guia_remision']. '" />
									<label for="observaciones">Observaciones</label>
									<textarea class="form-control" rows="3" name="observaciones" id="observaciones" >' .$row['observaciones']. '</textarea>
								</div>
								<div class="col-lg-4 col-mb-4 col-sm-12">
									<label for="fecha">Fecha emisión</label>
									<input class="form-control datepicker" id="fecha-emision" name="fecha-emision" value="' .$row['fec_emision']. '" data-provide="datepicker-inline" />
									<label for="medio-pago">Medio de pago</label>
									<input class="form-control" type="text" name="medio-pago" id="medio-pago" value="' .$row['des_med_pago']. '" />
									<label for="orden-compra">Orden de compra</label>
									<input class="form-control" type="text" name="orden-compra" id="orden-compra" value="' .$row['ord_compra']. '" />
								</div>
							</div>';
								}
							}
		$data .=			'<div class="panel-body">
								<button type="button" class="btn btn-success" data-toggle="modal" data-target="#mod-agregar-producto" title="agregar productos">Agregar productos</button>
							</div>
							<!-- /.col-lg-12 -->
							<div class="panel-body">
								<div id="detalle-comprobante">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>N°</th>
												<th>Producto</th>
												<th>Cantidad</th>
												<th>Precio unit.</th>
												<th>Precio total</th>
											</tr>
										</thead>
										<tbody>';
									
										if(mysqli_num_rows($resultDetComprobante) > 0) {
											$number     = 1;
											$montototal = 0;
											$montoneto  = 0;
											$montoigv   = 0;

											while ($row = mysqli_fetch_assoc($resultDetComprobante)) {
												$data .=	
													'<tr>
														<td>' .$number. '</td>
														<td>' .$row['des_producto']. '</td>
														<td style="text-align:center;">' .$row['cantidad']. '</td>
														<td style="text-align:right;">' .number_format($row['precio_unitario'], 2, '.', ''). '</td>
														<td style="text-align:right;">' .number_format($row['precio_total'], 2, '.', ''). '</td>
														<td>
															<label onclick="cargarDetComprobante2(' .$row['id']. ')" class="btn btn-default btn-circle" data-toggle="tooltip" title="editar">
																<i class="fa fa-pencil"></i>
															</label>
															<label onclick="eliminarDetComprobante2(' .$row['id']. ')" class="btn btn-danger btn-circle" data-toggle="tooltip" title="eliminar">
																<i class="fa fa-times"></i>
															</label>
														</td>
													</tr>';
												$_SESSION['rows'][$number] = $row;
												$montototal += $row['precio_total'];
												$number++;
											}
											$_SESSION['num-registros'] = $number;
											$montoneto = round($montototal/1.18, 2);
											$montoigv  = $montototal - $montoneto;
											
											$metodoReflexionado = new ReflectionMethod('numerosALetras', 'to_word');
											$montoEnLetras = $metodoReflexionado->invoke(new numerosALetras(), number_format($montototal, 2, '.', ''), 'PEN');
										}
										
										$data .= 
										'</tbody>
											<tfoot>
												<tr>
													<td colspan="3">Son: ' .$montoEnLetras. '</td>
													<td>Monto neto</td>
													<td style="text-align:right;">' .number_format($montoneto, 2, '.', ''). '</td>
													<input type="hidden" id="monto-neto" name="monto-neto" value="' .number_format($montoneto, 2, '.', ''). '" />
												</tr>
												<tr>
													<td colspan="3">&nbsp;</td>
													<td>IGV</td>
													<td style="text-align:right;">' .number_format($montoigv, 2, '.', ''). '</td>
													<input type="hidden" id="monto-igv" name="monto-igv" value="' .number_format($montoigv, 2, '.', ''). '" />
												</tr>
												<tr>
													<td colspan="3">
														<input class="form-check-input" type="checkbox" name="entregado" id="entregado" /> Entregado
													</td>
													<td>Monto total</td>
													<td style="text-align:right;">' .number_format($montototal, 2, '.', ''). '</td>
													<input type="hidden" id="monto-total" name="monto-total" value="' .number_format($montototal, 2, '.', ''). '" />
												</tr>
											</tfoot>';
		$data .=					
									'</table>
								</div>
							</div>
							<div class="panel-body">
								<center>
									<input type="submit" class="btn btn-info" title="actualizar" name="actualizar" onclick="actualizarComprobante()" value="Actualizar" />
								</center>
							</div>
						</div>
					</div>
				</div>';

		echo $data;
	}
?>