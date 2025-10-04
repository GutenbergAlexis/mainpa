<?php
	include('../util/database.php');
	session_start();
	
    if(isset($_POST['idCliente']))
	{
		$idCliente = $_POST['idCliente'];
		
		$selectCliente = 
			"SELECT * FROM clientes WHERE id = '$idCliente'";
		
		if (!$resultSelectCliente = mysqli_query($con, $selectCliente)) 
		{
			exit(mysqli_error($con));
		}
	
		if(mysqli_num_rows($resultSelectCliente) > 0) 
		{
			$rowSelectCliente = mysqli_fetch_assoc($resultSelectCliente);
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Actualizar cliente</h4>
					</div>
					<div class="modal-body">
						<div class="panel-body">
							<h4>Datos del cliente</h4>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="tipo-cliente">Tipo Cliente&nbsp;&nbsp;</label>
								<?php
									if ($rowSelectCliente['tip_cliente'] == 1) {
										echo 
										'<label class="radio-inline"><input type="radio" name="tipo-cliente" id="act-rb-juridica" value="juridica"> Persona jurídica&nbsp;&nbsp;</label>
										<label class="radio-inline"><input type="radio" checked="checked" name="tipo-cliente" id="act-rb-natural" value="natural"> Persona natural</label>';
									} else {
										echo
										'<label class="radio-inline"><input type="radio" checked="checked" name="tipo-cliente" id="act-rb-juridica" value="juridica"> Persona jurídica&nbsp;&nbsp;</label>
										<label class="radio-inline"><input type="radio" name="tipo-cliente" id="act-rb-natural" value="natural"> Persona natural</label>';
									}
								?>
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-tipo-documento">Tipo de documento</label>
								<select class="form-control" id="act-tipo-documento" name="act-tipo-documento" data-live-search="true" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
									<?php 
										$query = 'SELECT * FROM parametros WHERE padre = 12';
										$result = mysqli_query($con, $query);
										while ($rows = mysqli_fetch_assoc($result)) {
									?>
											<option value='<?php echo $rows['codigo']; ?>' data-tokens='<?php echo $rows['codigo'].$rows['descripcion']; ?>'><?php echo $rows['codigo'].'. '.$rows['descripcion']; ?></option>
									<?php 
										}
									?>
								</select>
								<script>document.ready=document.getElementById("act-tipo-documento").value='<?php echo $rowSelectCliente['tip_documento'] ?>';</script>
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-numero-documento">Número de documento</label>
								<input class="form-control" type="text" id="act-numero-documento" name="act-numero-documento" placeholder="Número de documento" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $rowSelectCliente['num_documento']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-primer-nombre">Primer nombre/Razón social</label>
								<input class="form-control" type="text" id="act-primer-nombre" name="act-primer-nombre" placeholder="Primer nombre/Razón social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo utf8_encode($rowSelectCliente['nombre_razon_social']); ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-segundo-nombre">Segundo nombre</label>
								<input class="form-control" type="text" id="act-segundo-nombre" name="act-segundo-nombre" placeholder="Segundo nombre" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo utf8_encode($rowSelectCliente['seg_nombre']); ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-primer-apellido">Primer apellido</label>
								<input class="form-control" type="text" id="act-primer-apellido" name="act-primer-apellido" placeholder="Primer apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo utf8_encode($rowSelectCliente['pri_apellido']); ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-segundo-apellido">Segundo apellido</label>
								<input class="form-control" type="text" id="act-segundo-apellido" name="act-segundo-apellido" placeholder="Segundo apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo utf8_encode($rowSelectCliente['seg_apellido']); ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-direccion">Dirección</label>
								<input class="form-control" type="text" id="act-direccion" name="act-direccion" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo utf8_encode($rowSelectCliente['direccion']); ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-contacto">Contacto</label>
								<input class="form-control" type="text" id="act-contacto" name="act-contacto" placeholder="Contacto" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo utf8_encode($rowSelectCliente['contacto']); ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-telefono">Teléfono</label>
								<input class="form-control" type="text" id="act-telefono" name="act-telefono" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $rowSelectCliente['telefono']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-celular">Celular</label>
								<input class="form-control" type="text" id="act-celular" name="act-celular" placeholder="Celular" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $rowSelectCliente['celular']; ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-correo">Correo electrónico</label>
								<input class="form-control" type="text" id="act-correo" name="act-correo" placeholder="Correo electrónico" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $rowSelectCliente['correo']; ?>" />
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="actualizarCliente();" title="actualizar">Actualizar</button>
						<input type="hidden" id="id-cliente" value="<?php echo $idCliente; ?>">
					</div>
				</form>
			</div>
		</div>
<?php
		}
	}
?>