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
					$datos['dategoal'] = '';
					$datos['proccess'] = '';
					$datos['goal'] = '';

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
					<label for="dategoal" class="col-md-3 col-xs-12 col-form-label">Fecha *:
					<h6>Por favor Ingrese la fecha</h6></label>
					<div class="col-12">
						<input tabindex="1" type="date" class="form-control" name="dategoal" value="<?=$datos["dategoal"]?>" id="dategoal" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required>
						
					</div>
				</div>
				<div class="form-group">
					<label for="proccess" class="form-label">Proceso:</label>
					<input type="hidden" name="proccess" id="proccess" value="<?=$datos["proccess"]?>" required>
					<input onkeyup="getProccess();" tabindex="2" type="text" value="<?=$datos["proccessn"]?>" class="form-control" id="proccessn" name="proccessn">
					<div id="suggesstion-box"></div>
				</div>
				<div class="form-group">
					<label for="goal" class="col-md-3 col-xs-12 col-form-label">Meta *:
					<h6>Por favor Ingrese la meta del día</h6></label>
					<div class="col-12">
						<input tabindex="3" type="text" class="form-control" name="goal" value="<?=$datos["goal"]?>" id="goal" required>
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