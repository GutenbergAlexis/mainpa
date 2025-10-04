<?php
	include('../x/detCompra.php');
	session_start();
	
    if(isset($_POST['idDetCompra']))
	{
		$idDetCompra = $_POST['idDetCompra'];
		
		$detCompra = $_SESSION['detCompra'][$idDetCompra];
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Actualizar</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="act-cantidad">Cantidad</label>
						<input type="text" id="act-cantidad" placeholder="Cantidad" class="form-control" value="<?php echo $detCompra->getProCantidad(); ?>" />
					</div>
					<div class="form-group">
						<label for="act-costo">Costo</label>
						<input type="text" id="act-costo" placeholder="Costo" class="form-control" value="<?php echo $detCompra->getProCostoUnitario(); ?>" />
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" onclick="actualizarDetCompra()">Actualizar</button>
					<input type="hidden" id="id-det-compra" value="<?php echo $idDetCompra; ?>">
				</div>
			</div>
		</div>
<?php
	}
?>