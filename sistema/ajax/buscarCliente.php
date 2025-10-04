<?php
	include('../util/database.php');
	session_start();

	if(isset($_POST['numDocumento']) && isset($_POST['numDocumento']) != "")
	{
		$numDocumento = $_POST['numDocumento'];
		
		$selectCliente = 
			"SELECT id, num_documento, direccion, 
				CONCAT_WS(' ', nombre_razon_social, seg_nombre, pri_apellido, seg_apellido) AS nombre_razon_social
				FROM clientes 
				WHERE num_documento = '$numDocumento'";
		
		if (!$resultSelectCliente = mysqli_query($con, $selectCliente)) 
		{
			exit(mysqli_error($con));
		}
		
		if(mysqli_num_rows($resultSelectCliente) > 0) 
		{
			while ($rowSelectCliente = mysqli_fetch_assoc($resultSelectCliente)) 
			{
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">Datos del cliente</h4>
				</div>
				<div class="modal-body">
					<div class="panel-body">
						<div class="col-lg-6 col-mb-6 col-sm-12">
							<label for="cli-numero-documento">Número de documento</label>
							<input class="form-control" type="text" id="cli-numero-documento" value="<?php echo $rowSelectCliente['num_documento']; ?>" readonly />
						</div>
						<div class="col-lg-12 col-mb-12 col-sm-12">
							<label for="cli-nombre-razon-social">Nombre/Razón Social</label>
							<input class="form-control" type="text" id="cli-nombre-razon-social" value="<?php echo $rowSelectCliente['nombre_razon_social']; ?>" readonly />
						</div>
						<div class="col-lg-12 col-mb-12 col-sm-12">
							<label for="cli-direccion">Dirección</label>
							<input class="form-control" type="text" id="cli-direccion" value="<?php echo $rowSelectCliente['direccion']; ?>" readonly />
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" onclick="agregarCliente()">Agregar</button>
					<input type="hidden" id="cli-id-cliente" value="<?php echo $rowSelectCliente['id']; ?>">
				</div>
			</div>
		</div>
<?php 
			}
		}
		else
		{
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">No se encontró el cliente</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>No se encontró el cliente</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
<?php 
		}
	}
	else
	{
?>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">No se encontró el cliente</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>No ingresó el número de documento</label>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
<?php 
	}
?>