<div class="row bg-title">
	<!-- .page title 
	<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
		<h4 class="page-title"><?=$titulo?></h4>
	</div>
	-->
	<!-- /.page title -->
	<!-- .breadcrumb 
	<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">                        
		<ol class="breadcrumb">
			<li class="active"><?=$titulo?></li>
		</ol>
	</div>-->
	<!-- /.breadcrumb -->
</div>
<!-- .row -->
<div class="row">
	<div class="col-md-12">					
		<div class="card" >                            
			<div class="card-body">
				<?php
				if (empty($datos)) {
					$datos['id'] = 0;
					$datos['employee'] = '';
					$datos['employeen'] = '';
					$datos['destinyd'] = '';

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
					<label for="employeess" class="form-label">Empleado:</label> <a href="#" onclick="Escanear(0);return false;" class="btn btn-primary">QR</a>
					<input type="hidden" name="employeess" id="employeess" value="<?=$datos['employee']?>" required>
					<input onkeyup='buscaEmpleado();return false;' tabindex="1" type="text" value="<?=$datos['employeen']?>" class="form-control" id="employees" name="employees">
					<div id="suggesstion-box"></div>
				</div>

				<div class="form-group">
					<label for="destinyd" class="col-md-3 col-xs-12 col-form-label">Destino *:
					<h6>Por favor escoja el destino</h6></label>
					<div class="col-12">						
						<?= form_dropdown('destinyd', $destinysd, $datos['destinyd'],"class = form-control");?>
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