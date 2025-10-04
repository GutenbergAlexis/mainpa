<?php
	include('../util/database.php');
	session_start();
	
	$idCuota = $_POST['idCuota'];
	
	$selectCuota = "
	    SELECT cuo.id, cuo.id_comprobante, DATE_FORMAT(cuo.fecha, '%Y-%m-%d') AS fecha, cuo.monto 
        FROM cuotas cuo 
        WHERE cuo.id = '$idCuota'";
    
    if (!$resultSelectCuota = mysqli_query($con, $selectCuota)) {
		exit(mysqli_error($con));
	}
	
	if(mysqli_num_rows($resultSelectCuota) > 0) {
		$row = mysqli_fetch_assoc($resultSelectCuota);
?>
<div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Actualizar Cuota</h4>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12">
					<div class="form-group">
						<label for="fecha-cuota">Fecha</label>
						<input type="date" class="form-control" id="act-fecha-cuota" value="<?php echo $row['fecha']; ?>" />
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12">
					<div class="form-group">
						<label for="monto-cuota">Monto</label>
						<input type="number" step="0.01" min="0" class="form-control" id="act-monto-cuota" value="<?php echo $row['monto']; ?>" style="text-align:right;" />
					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
			<button type="button" onclick="actualizarCuotaComprobante()" class="btn btn-success">Actualizar</button>
			<input type="hidden" id="id-cuota" value="<?php echo $row['id']; ?>" />
			<input type="hidden" id="id-comprobante-cuota" value="<?php echo $row['id_comprobante']; ?>" />
		</div>
	</div>
</div>
<?php
	}
?> 