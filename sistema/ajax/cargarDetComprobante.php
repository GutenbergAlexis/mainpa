<?php
	include('../x/detComprobante.php');
	session_start();
	
    if(isset($_POST['idDetComprobante']))
	{
		$idDetComprobante = $_POST['idDetComprobante'];
		
		$detComprobante = $_SESSION['detComprobante'][$idDetComprobante];
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
							<input type="text" id="act-producto" placeholder="Producto" class="form-control" value="<?php echo $detComprobante->getProDescripcion(); ?>" readonly />
						</div>
						<div class="col-lg-6 col-mb-6 col-sm-12">
							<label for="act-unidad-medida">Uni. Med.</label>
							<input type="text" id="act-unidad-medida" placeholder="Unidad de medida" class="form-control" value="<?php echo $detComprobante->getProUnidadMedida(); ?>" readonly />
						</div>
<?php
					if ($detComprobante->getProCodigoUnidadMedida() == 3)
					{
?>
						<div class="col-lg-6 col-mb-6 col-sm-12">
							<label for="act-cantidad">Cantidad</label>
							<input type="text" id="act-cantidad" placeholder="Cantidad" class="form-control" value="<?php echo $detComprobante->getProCantidad(); ?>" />
						</div>
						<div class="col-lg-2 col-mb-2 col-sm-12">
							<label for="act-espesor">Espesor</label>
							<input type="text" id="act-espesor" placeholder="Espesor" class="form-control" value="<?php echo $detComprobante->getProEspesor(); ?>" />
						</div>
						<div class="col-lg-2 col-mb-2 col-sm-12">
							<label for="act-ancho">Ancho</label>
							<input type="text" id="act-ancho" placeholder="Ancho" class="form-control" value="<?php echo $detComprobante->getProAncho(); ?>" />
						</div>
						<div class="col-lg-2 col-mb-2 col-sm-12">
							<label for="act-largo">Largo</label>
							<input type="text" id="act-largo" placeholder="Largo" class="form-control" value="<?php echo $detComprobante->getProLargo(); ?>" />
						</div>
<?php						
					}
					else 
					{
?>
						<div class="col-lg-6 col-mb-6 col-sm-12">
							<label for="act-cantidad">Cantidad</label>
							<input type="text" id="act-cantidad" placeholder="Cantidad" class="form-control" value="<?php echo $detComprobante->getProCantidadFinal(); ?>" />
						</div>
<?php
					}
?>
						<div class="col-lg-6 col-mb-6 col-sm-12">
							<label for="act-precio">Precio</label>
							<input type="text" id="act-precio" placeholder="Precio" class="form-control" value="<?php echo number_format($detComprobante->getProPrecioUnitario(), 2, '.', ''); ?>" />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" onclick="actualizarDetComprobante()">Actualizar</button>
					<input type="hidden" id="id-det-comprobante" value="<?php echo $idDetComprobante; ?>">
					<input type="hidden" id="cod-unidad-medida" value="<?php echo $detComprobante->getProCodigoUnidadMedida(); ?>">
				</div>
			</div>
		</div>
<?php
	}
?>