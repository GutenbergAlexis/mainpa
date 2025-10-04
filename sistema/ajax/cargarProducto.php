<?php
	include('../util/database.php');
	session_start();
	
    if(isset($_POST['idProducto']))
	{
		$idProducto = $_POST['idProducto'];
		
		$query = 
			"SELECT * FROM productos WHERE id = '$idProducto'";
		
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
						<h4 class="modal-title" id="myModalLabel">Actualizar producto</h4>
					</div>
					<div class="modal-body">
						<div class="panel-body">
							<h4>Datos del producto</h4>
							<div class="col-lg-12 col-mb-12 col-sm-12">
								<label for="act-descripcion">Descripción</label>
								<input class="form-control" type="text" id="act-descripcion" name="act-descripcion" placeholder="Descripción" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['descripcion']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-codigo">Código</label>
								<input class="form-control" type="text" id="act-codigo" name="act-codigo" placeholder="Código" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row['codigo']; ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-precio">Precio</label>
								<input class="form-control" type="text" id="act-precio" name="act-precio" placeholder="Precio" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo number_format($row['precio'], 2, '.', ''); ?>" />
							</div>
							<div class="col-lg-6 col-mb-12 col-sm-12">
								<label for="act-stock">Stock</label>
								<input class="form-control" type="text" id="act-stock" name="act-stock" placeholder="Stock" value="<?php echo $row['stock']; ?>" />
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" onclick="actualizarProducto();" title="actualizar">Actualizar</button>
						<input type="hidden" id="id-producto" value="<?php echo $idProducto; ?>">
					</div>
				</form>
			</div>
		</div>
<?php
		}
	}
?>