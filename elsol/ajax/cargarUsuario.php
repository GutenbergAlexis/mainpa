<?php
	include('../util/database.php');
	session_start();
	
    if(isset($_POST['idUsuario']))
	{
		$idUsuario = $_POST['idUsuario'];
		
		$query = 
				"SELECT * FROM usuarios WHERE id = '$idUsuario'";
		
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
						<h4 class="modal-title" id="myModalLabel">Actualizar usuario</h4>
					</div>
					<div class="modal-body">
						<div class="panel-body">
							<h4>Datos del usuario</h4>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-numero-documento">Número de documento</label>
								<input class="form-control" type="text" id="act-numero-documento" name="act-numero-documento" placeholder="Número de documento" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['num_documento']; ?>" />
							</div>
						</div>
						<div class="panel-body">
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-primer-nombre">Primer nombre</label>
								<input class="form-control" type="text" id="act-primer-nombre" name="act-primer-nombre" placeholder="Primer nombre" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['pri_nombre']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-segundo-nombre">Segundo nombre</label>
								<input class="form-control" type="text" id="act-segundo-nombre" name="act-segundo-nombre" placeholder="Segundo nombre" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['seg_nombre']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-primer-apellido">Primer apellido</label>
								<input class="form-control" type="text" id="act-primer-apellido" name="act-primer-apellido" placeholder="Primer apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['pri_apellido']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-segundo-apellido">Segundo apellido</label>
								<input class="form-control" type="text" id="act-segundo-apellido" name="act-segundo-apellido" placeholder="Segundo apellido" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['seg_apellido']; ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-direccion">Dirección</label>
								<input class="form-control" type="text" id="act-direccion" name="act-direccion" placeholder="Dirección" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['direccion']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-telefono">Teléfono</label>
								<input class="form-control" type="text" id="act-telefono" name="act-telefono" placeholder="Teléfono" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['telefono']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-celular">Celular</label>
								<input class="form-control" type="text" id="act-celular" name="act-celular" placeholder="Celular" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['celular']; ?>" />
							</div>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-correo">Correo electrónico</label>
								<input class="form-control" type="text" id="act-correo" name="act-correo" placeholder="Correo electrónico" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['correo']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-user">Usuario</label>
								<input class="form-control" type="text" id="act-user" name="act-user" placeholder="Usuario" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['user']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-pass">Contraseña</label>
								<input class="form-control" type="password" id="act-pass" name="act-pass" placeholder="Contraseña" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['pass']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-perfil">Perfil</label>
								<select class="form-control" id="act-perfil" name="act-perfil" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
									<?php 
										$query = 'SELECT * FROM parametros WHERE padre = 15';
										$result = mysqli_query($con, $query);
										while ($rows = mysqli_fetch_assoc($result)) {
											echo '<option value=' .$rows['codigo']. ' data-tokens=' .$rows['codigo']. '.' .$rows['descripcion']. '>' .$rows['codigo']. '. ' .$rows['descripcion']. '</option>';
										}
									?>
								</select>
								<script>document.ready=document.getElementById("act-perfil").value='<?php echo $row['perfil'] ?>';</script>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="actualizarUsuario();" title="actualizar">Actualizar</button>
						<input type="hidden" id="id-usuario" value="<?php echo $idUsuario; ?>">
					</div>
				</form>
			</div>
		</div>
<?php
		}
	}
?>