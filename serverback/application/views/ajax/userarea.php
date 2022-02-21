<!-- .row -->
<div class="row">
	<div class="col-md-12">					
		<div class="card" >                            
			<div class="card-body">
				<?
				if (empty($datos)) {
					$datos['id'] = 0;
					$datos['user'] = '';
					$datos['area'] = '';
					$datos['proceso'] = '';
					
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
					<label for="user" class="col-md-3 col-xs-12 col-form-label">Usuario *:
					<h6>Por favor escoja el usuario</h6></label>
					<div class="col-12">						
						<?= form_dropdown('user', $users, $datos['user'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="area" class="col-md-3 col-xs-12 col-form-label">Area *:
					<h6>Por favor escoja el area</h6></label>
					<div class="col-12">						
						<?= form_dropdown('area', $areas, $datos['area'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="area" class="col-md-3 col-xs-12 col-form-label">Proceso *:
					<h6>Por favor escoja el proceso</h6></label>
					<div class="col-12" id='process'>
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
			</div>			
		</div>		
	</div>
</div>