<div class="row bg-title">
</div>
<!-- .row -->
<div class="row">
	<div class="col-md-12">					
		<div class="card" >                            
			<div class="card-body">
				<?
				if (empty($datos)) {
					$datos['id'] = 0;
					$datos['name'] = '';
					$accion="accs";
				}else{
					$accion="acce";
				}
				echo form_open_multipart('#', array('class' => "col-md-12 $accion", 'id' => 'forminterno3'));
				?>				
				<div class="form-group">
					<input name="id" value="<?=$datos["id"]?>" id="id" type="hidden">
					<input name="idp" value="<?=$idp?>" id="idp" type="hidden">
					<input name="ide" value=<?=$ide?> id='ide' type='hidden'>
				</div>
				<div class="form-group">
					<label for="name" class="col-md-3 col-xs-12 col-form-label">Nombre *:
					<h6>Por favor Ingrese el nombre del producto</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="name" value="<?=$datos["name"]?>" id="name" required>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6 col-xs-12">
						<center>
							<button onclick='guardaM();return false;' class="btn btn-success waves-effect waves-light w-100 m-r-10 m-t-10">Guardar</button>
						</center>
					</div>
					<div class="col-md-6 col-xs-12">
						<center>
							<button onclick="measures(<?=$idp?>,<?=$ide?>);return false;"class="btn btn-success waves-effect waves-light w-100 m-r-10 m-t-10">Volver</button>
						</center>
					</div>
				</div>                
				<?= form_close(); ?>
			</div>			
		</div>
	</div>
</div>