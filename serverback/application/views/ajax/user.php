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
				<?
				if (empty($datos)) {
					$datos['id'] = 0;
					$datos['name'] = '';
					$datos['phone'] = '';
					$datos['email'] = '';
					$datos['rol'] = '';
					$datos['status'] = '';
					$datos['user'] = '';
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
					<label for="user" class="col-md-3 col-xs-12 col-form-label">Usuario *:
					<h6>Por favor Ingrese el usuario</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="user" value="<?=$datos["user"]?>" id="user" required>
					</div>
				</div>	
				<div class="form-group">
					<label for="name" class="col-md-3 col-xs-12 col-form-label">Nombre *:
					<h6>Por favor Ingrese el nombre del usuario</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="name" value="<?=$datos["name"]?>" id="name" required>
					</div>
				</div>	

				<div class="form-group">
					<label for="phone" class="col-md-3 col-xs-12 col-form-label">Tel   *:
					<h6> Por favor Ingrese el telefono del usuario</h6></label>
					<div class="col-12">
						<input class="form-control" name="phone" value="<?=$datos["phone"]?>" id="phone" type="text" required>
					</div>
				</div>
				<div class="form-group">
					<label for="email" class="col-md-3 col-xs-12 col-form-label">Email   *:
					<h6>Por favor Ingrese el email del usuario</h6></label>
					<div class="col-12">
						<input class="form-control" name="email" value="<?=$datos["email"]?>" id="email" type="text" required>
					</div>
				</div>
				<div class="form-group">
					<label for="pass" class="col-md-3 col-xs-12 col-form-label">Clave   *:
					<h6>Por favor Ingrese la clave del usuario</h6></label>
					<div class="col-12">
						<input autocomplete="false" class="form-control" name="pass" value="" id="pass" type="password">
					</div>
				</div>
				<div class="form-group">
					<label for="rol" class="col-md-3 col-xs-12 col-form-label">Rol *:
					<h6>Por favor escoja el rol de accesso</h6></label>
					<div class="col-12">						
						<?= form_dropdown('rol', $roles, $datos['rol'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="status" class="col-md-3 col-xs-12 col-form-label">Estado *:
					<h6>Por favor escoja el estado del usuario</h6></label>
					<div class="col-12">						
						<?= form_dropdown('status', $estados, $datos['status'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="destiny" class="col-md-3 col-xs-12 col-form-label">Area:
					<h6>Por favor escoja el area del usuario</h6></label>
					<div class="col-12">						
						<?= form_dropdown('destiny', $areas, $datos['destiny'],"class = form-control");?>
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