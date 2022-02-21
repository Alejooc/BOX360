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
					$datos['supplies'] = '';
					$datos['suppliess'] = '';
					$datos['item'] = '';
					$datos['items'] = '';
					$datos['cons'] = '';

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
					<label for="supplies" class="form-label">Insumo:</label>
					<input type="hidden" name="supplies" id="supplies" value="<?=$datos['supplies']?>" required>
					<input autocomplete="off" onkeyup='buscaimple();return false;' tabindex="1" type="text" value="<?=$datos['suppliess']?>" class="form-control" id="suppliesss" name="suppliesss">
					<div id="suggesstion-box"></div>
				</div>
				<div class="form-group">
					<label for="item" class="form-label">Item:</label>
					<input type="hidden" name="item" id="item" value="<?=$datos['item']?>" required>
					<input  autocomplete="off" onkeyup='buscaitem();return false;' tabindex="1" type="text" value="<?=$datos['items']?>" class="form-control" id="itemss" name="itemss">
					<div id="suggesstion-box2"></div>
				</div>
				<div class="form-group">
					<label for="cons" class="col-md-3 col-xs-12 col-form-label">Consumo *:
					<h6>Por favor Ingrese el Consumo</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="cons" value="<?=$datos["cons"]?>" id="name" required>
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