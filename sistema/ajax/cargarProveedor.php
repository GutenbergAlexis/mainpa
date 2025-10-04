<?php
	include('../util/database.php');
	session_start();
	
    if(isset($_POST['idProveedor']))
	{
		$idProveedor = $_POST['idProveedor'];
		
		$query = 
				"SELECT * FROM proveedores WHERE id = '$idProveedor'";
		
		if (!$result = mysqli_query($con, $query)) 
		{
			exit(mysqli_error($con));
		}
	
		if(mysqli_num_rows($result) > 0) 
		{
			$row = mysqli_fetch_assoc($result);
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Actualizar proveedor</h4>
					</div>
					<div class="modal-body">
						<div class="panel-body">
							<h4>Datos del proveedor</h4>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-ruc">RUC</label>
								<input class="form-control" type="text" id="act-ruc" name="act-ruc" placeholder="RUC" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['ruc']; ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-razon-social">Razon social</label>
								<input class="form-control" type="text" id="act-razon-social" name="act-razon-social" placeholder="RAZÓN SOCIAL" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['razon_social']; ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-direccion">Direccion</label>
								<input class="form-control" type="text" id="act-direccion" name="act-direccion" placeholder="DIRECCIÓN" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['direccion']; ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-contacto">Contacto</label>
								<input class="form-control" type="text" id="act-contacto" name="act-contacto" placeholder="CONTACTO" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['contacto']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-telefono">Telefono</label>
								<input class="form-control" type="text" id="act-telefono" name="act-telefono" placeholder="TELÉFONO" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['telefono']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-correo">Correo</label>
								<input class="form-control" type="text" id="act-correo" name="act-correo" placeholder="CORREO" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['correo']; ?>" />
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="actualizarProveedor();" title="actualizar">Actualizar</button>
						<input type="hidden" id="id-proveedor" value="<?php echo $idProveedor; ?>">
					</div>
				</form>
			</div>
		</div>
<?php
		}
	}
?>