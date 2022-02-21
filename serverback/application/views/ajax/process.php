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
					$datos['name'] = '';
					$datos['incentive'] = '';
					$datos['extra'] = '';
					$datos['destiny'] = '';

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
					<h6>Por favor Ingrese el Nombre</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="name" value="<?=$datos["name"]?>" id="name" required>
					</div>
				</div>
				<div class="form-group">
					<label for="destiny" class="col-md-3 col-xs-12 col-form-label">Area *:
					<h6>Por favor Ingrese el area del proceso</h6></label>
					<div class="col-12">
						<?= form_dropdown('destiny', $areas, $datos['destiny'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="incentive" class="col-md-3 col-xs-12 col-form-label">Incentivo *:
					<h6>Por favor Ingrese el valor del incentivo</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="incentive" value="<?=$datos["incentive"]?>" id="incentive" required>
					</div>
				</div>
				<div class="form-group">
					<label for="extra" class="col-md-3 col-xs-12 col-form-label">Extra *:
					<h6>Por favor Ingrese el valor de la Hora Extra</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="extra" value="<?=$datos["extra"]?>" id="extra" required>
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