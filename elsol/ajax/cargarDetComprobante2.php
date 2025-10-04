<?php
	include('../util/database.php');
	session_start();
	
    if(isset($_POST['idDetComprobante']))
	{
		$idDetComprobante = $_POST['idDetComprobante'];
		
		$selectDetComprobante = 
			"SELECT dcom.*, pro.descripcion, pro.unidad_medida, par.descripcion AS des_um 
				FROM det_comprobante dcom 
				JOIN productos pro ON pro.id = dcom.id_producto 
				JOIN parametros par ON par.codigo = pro.unidad_medida AND par.padre = 29 
					WHERE dcom.id = $idDetComprobante";
		
		if (!$resultSelectDetComprobante = mysqli_query($con, $selectDetComprobante)) 
		{
			exit(mysqli_error($con));
		}
		
		if(mysqli_num_rows($resultSelectDetComprobante) > 0) 
		{
			while ($rowSelectDetComprobante = mysqli_fetch_assoc($resultSelectDetComprobante)) 
			{
?>
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Actualizar</h4>
						</div>
						<div class="modal-body">
							<div class="panel-body">
								<div class="col-lg-12 col-mb-12 col-sm-12">
									<label for="act-producto">Producto</label>
									<input class="form-control" type="text" id="act-producto" placeholder="Producto" value="<?php echo utf8_encode($rowSelectDetComprobante['descripcion']); ?>" readonly />
								</div>
								<div class="col-lg-6 col-mb-12 col-sm-12">
									<label for="un_medida">Unidad de medida</label>
									<input class="form-control" type="text" id="un_medida" placeholder="Unidad de medida" value="<?php echo utf8_encode($rowSelectDetComprobante['des_um']); ?>" readonly />
								</div>
								<div class="col-lg-6 col-mb-12 col-sm-12">
									<label id="can" for="act-cantidad">Cantidad</label>
									<input class="form-control" type="text" id="act-cantidad" placeholder="Cantidad" value="<?php echo $rowSelectDetComprobante['cantidad']; ?>" />
								</div>
								<?php 
									if ($rowSelectDetComprobante['unidad_medida'] == 3) 
									{
								?>
								<div class="col-lg-2 col-mb-12 col-sm-12">
									<label id="esp" for="act-espesor">Esp.</label>
									<input class="form-control" type="text" id="act-espesor" placeholder="Espesor" value="<?php echo $rowSelectDetComprobante['espesor']; ?>" />
								</div>
								<div class="col-lg-2 col-mb-12 col-sm-12">
									<label id="anc" for="act-ancho">Anc.</label>
									<input class="form-control" type="text" id="act-ancho" placeholder="Ancho" value="<?php echo $rowSelectDetComprobante['ancho']; ?>" />
								</div>
								<div class="col-lg-2 col-mb-12 col-sm-12">
									<label id="lar" for="act-largo">Lar.</label>
									<input class="form-control" type="text" id="act-largo" placeholder="Largo" value="<?php echo $rowSelectDetComprobante['largo']; ?>" />
								</div>
								<?php
									}
								?>
								<div class="col-lg-6 col-mb-12 col-sm-12">
									<label for="act-precio">Precio</label>
									<input class="form-control" type="text" id="act-precio" placeholder="Precio" value="<?php echo $rowSelectDetComprobante['precio']; ?>" />
								</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-primary" onclick="actualizarDetComprobante2()">Actualizar</button>
							<input type="hidden" id="id-det-comprobante" value="<?php echo $idDetComprobante; ?>">
							<input type="hidden" id="act-unidad-medida" value="<?php echo $rowSelectDetComprobante['unidad_medida']; ?>">
						</div>
					</div>
				</div>
<?php
			}
		}
	}
?>