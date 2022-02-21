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
				echo form_open_multipart('#', array('class' => "col-md-12 $accion", 'id' => 'forminterno'));
				?>				
				<div class="form-group">
					<input name="id" value="<?=$datos["id"]?>" id="id" type="hidden">
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
							<button id="enviarform" class="btn btn-warning waves-effect waves-light w-100 m-r-10 m-t-10">Guardar</button>
						</center>
					</div>
					<div class="col-md-6 col-xs-12">
						<center>
							<button onclick="redraw();return false;"class="btn btn-danger waves-effect waves-light w-100 m-r-10 m-t-10">Volver</button>
						</center>
					</div>
				</div>                
				<?= form_close(); ?>
				<div class="row">
					<? if ($datos['id']>0){ ?>
						<div class="col-12">
							<center>
								<button onclick="formu2(0,<?=$datos["id"]?>);" class="btn btn-success w-100" data-toggle="modal" data-target="#MyModalParte">Agregar detalle</button>
							</center>
						</div>
					<? } ?>
				</div>
			</div>			
		</div>
		<?
			if ($datos['id']>0){
		?>
		<div class="white-box" >
			<div class="row">
				<div class="col-12">
					<h3>Producto detalle</h3>
					<?=$table?>
				</div>
			</div>
		</div>
		<? } ?>	
	</div>
</div>
<div id="MyModalDetalle" class="modal bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="detallenModalLabel">Crear Detalle</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<?
				if (empty($datos2)) {
					$datos2['id'] = 0;
					$datos2['name'] = '';
					$accion="accs";
				}else{
					$accion="acce";
				}
				echo form_open_multipart('#', array('class' => "col-md-12 $accion", 'id' => 'forminterno1'));
				?>				
				<div class="form-group row">
					<input name="id2" id="id2" value="0" type="hidden">
					<input name="productid" id="productid" type="hidden">
				</div>
				<div class="form-group row">
					<label for="qty2" class="col-md-3 col-xs-12 col-form-label">Nombre *:
					<h6>Por favor Ingresa el nombre</h6></label>
					<div class="col-9">
						<input class="form-control" value="<?=$datos2["name"]?>" name="name1" id="name1" type="text" required>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12 col-xs-12">
						<center>
							<button onclick="guardaFormInterno(); return false;" id="enviarform2" class="btn btn-warning waves-effect waves-light m-r-10" data-dismiss="modal"  aria-hidden="true">Guardar</button>
						</center>
					</div>
				</div>
				<?= form_close(); ?>
			</div>
			<div class="modal-footer">
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>