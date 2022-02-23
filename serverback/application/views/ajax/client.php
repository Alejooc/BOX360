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
					$datos['typeID'] = '';
					$datos['type'] = '';
					$datos["docId"]="";
					$datos['rh'] = '';
					$datos['status'] = '';
					$datos['emergency'] = '';
					$datos['gender'] = '';
					$datos['birth'] = '';
					$datos['address'] = '';
					$datos['city'] = '';
					$datos['age'] = '';
					$datos['phone'] = '';

					
					$accion="accs";
				}else{
					$accion="acce";
				}
				echo form_open_multipart('#', array('class' => "col-md-12 $accion", 'id' => 'forminterno'));
				?>				
				<div class="form-group">
					<input name="id" value="<?=$datos["id"]?>" id="id" type="hidden">
				</div>
				<div class="row">
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="name" class="col-md-12 col-xs-12 col-form-label">Nombre *:
							<h6>Por favor Ingrese el nombre del usuario</h6></label>
							<div class="col-12">
								<input type="text" class="form-control" name="name" value="<?=$datos["name"]?>" id="name" required autocomplete="off">
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="typeID" class="col-md-12 col-xs-12 col-form-label">Tipo de documento *:
							<h6>Por favor escoja el tipo de documento</h6></label>
							<div class="col-12">						
								<?= form_dropdown('typeID', $tipe_docu, $datos['typeID'],"class = form-control");?>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="docid" class="col-md-12 col-xs-12 col-form-label">Numero de documento *:
							<h6>Por favor Ingrese el numero de documento</h6></label>
							<div class="col-12">
								<input type="text" class="form-control" name="docid" value="<?=$datos["docId"]?>" id="docid" required>
							</div>
						</div>
					</div>
					
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="rh" class="col-md-12 col-xs-12 col-form-label">RH *:
							<h6>Por favor Ingrese el rh del usuario</h6></label>
							<div class="col-12">
								<input type="text" class="form-control" name="rh" value="<?=$datos["rh"]?>" id="rh" required>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="phone" class="col-md-12 col-xs-12 col-form-label">Telefono *:
							<h6>Por favor Ingrese el numero de telefono</h6></label>
							<div class="col-12">
								<input type="text" class="form-control" name="phone" value="<?=$datos["phone"]?>" id="phone" required>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-12">
						<div class="form-group">
							<label for="gender" class="col-md-12 col-xs-12 col-form-label">Genero *:
							<h6>Por favor escoja el genero</h6></label>
							<div class="col-12">						
								<?= form_dropdown('gender', $genders, $datos['gender'],"class = form-control");?>
							</div>
						</div>
					</div>
					

				</div>
				<hr/>
				<button class="btn btn-info" type="button" onclick="vermas();" >
					Agregar datos extra
				</button>
				<span id='vermas' style='display: none;'>
					<div class="form-group">
						<label for="emergency" class="col-md-3 col-xs-12 col-form-label">Contacto de emergencía:
						<h6>Por favor Ingrese el contacto de emergencía</h6></label>
						<div class="col-12">
							<input type="text" class="form-control" name="emergency" value="<?=$datos["emergency"]?>" id="emergency">
						</div>
					</div>
					<div class="form-group">
						<label for="birth" class="col-md-3 col-xs-12 col-form-label">Fecha de nacimiento:
						<h6>Por favor Ingrese fecha de nacimiento</h6></label>
						<div class="col-12">
							<input type="text" class="form-control" name="birth" value="<?=$datos["birth"]?>" id="birth">
						</div>
					</div>	
					<div class="form-group">
						<label for="age" class="col-md-3 col-xs-12 col-form-label">Edad:
						<h6>Por favor Ingrese edad</h6></label>
						<div class="col-12">
							<input type="text" class="form-control" name="age" value="<?=$datos["age"]?>" id="age">
						</div>
					</div>
					<div class="form-group">
						<label for="address" class="col-md-3 col-xs-12 col-form-label">Dirección:
						<h6>Por favor Ingrese dirección</h6></label>
						<div class="col-12">
							<input type="text" class="form-control" name="address" value="<?=$datos["address"]?>" id="address">
						</div>
					</div>
					<div class="form-group">
						<label for="city" class="col-md-3 col-xs-12 col-form-label">Ciudad:
						<h6>Por favor Ingrese ciudad</h6></label>
						<div class="col-12">
							<input type="text" class="form-control" name="city" value="<?=$datos["city"]?>" id="city">
						</div>
					</div>
				</span>
				<div class="form-group row">
					<div class="col-md-6 col-xs-12">
						<center>
							<button id="enviarform" class="btn btn-success waves-effect waves-light w-100 m-r-10 m-t-10">Guardar</button>
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
					<?php if ($datos['id']>0){ ?>
						<div class="col-12">
							<center>
								<button onclick="formu2(0,<?=$datos["id"]?>);" class="btn btn-success w-100" data-toggle="modal" data-target="#MyModalParte">Agregar subscripción</button>
							</center>
						</div>
					<?php } ?>
				</div>
			</div>			
		</div>
	</div>
	<?php
			if ($datos['id']>0){
		?>
	<div class="white-box" >
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h3>Subscripciónes</h3>
					<?=$table?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>	
</div>
<div id="MyModalDetalle" class="modal bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="detalleModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="detallenModalLabel">Agregar Subscripción</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<?php
				if (empty($datos2)) {
					$datos2['id'] = 0;
					$datos2['sub'] = '';
					$datos2['user'] = '';
					$datos2['start'] = '';
					$datos2['end'] = '';
					$accion="accs";
				}else{
					$accion="acce";
				}
				echo form_open_multipart('#', array('class' => "col-md-12 $accion", 'id' => 'forminterno1'));
				?>				
				<div class="form-group row">
					<input name="id2" id="id2" value="0" type="hidden">
					<input name="idp" id="idp" type="hidden">
				</div>
				<div class="form-group row">
					<label for="plan" class="col-md-12 col-xs-12 col-form-label">Plan *:
					<h6>Por favor selecciona el plan a asignar</h6></label>
					<div class="col-12">
						<?= form_dropdown('plan', $plans, $datos2['sub'],"name='plan' id='plan' class = form-control onchange='detailext(this)'");?>
					</div>
				</div>
				<div class="form-group row">
					<label class=" col-md-12 col-xs-12 col-form-label"><h6>Por favor selecciona el inicio del plan</h6></label>
					<div class="col-md-6">
						<input type="text" name="inicio" class="form-control" placeholder="2017-06-04" id="mdateini">
					</div>
				</div>
				<div class="form-group row">
					<label class=" col-md-12 col-xs-12 col-form-label"><h6>Por favor selecciona el Fin del plan</h6></label>
					<div class="col-md-6">
						<input type="text" name="fin" class="form-control" placeholder="2017-06-04" id="mdatefin">
					</div>
				</div>
				<hr>
				<div class="col-md-12 col-xs-12">
					<div id="pland">Total Accesos: 0</div>	
					<div id="total">Total: $0</div>		
				</div>
				
				
			</div>
			<div class="modal-footer">
				<div class="col-md-12 col-xs-12">
					<button onclick="guardaFormInterno(); return false;" id="enviarform2" class="btn btn-warning waves-effect waves-light btn-block" data-dismiss="modal"  aria-hidden="true">Guardar</button>
				</div>
			</div>
			<?= form_close(); ?>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<script>
$(document).ready(function(){
	$("#destinyn").keyup(function(){
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/"+modulo+"/autocomplete/destiny",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			data:'keyword='+$(this).val(),
			success: function(data){
				$("#suggesstion-box-d").show();
				$("#suggesstion-box-d").html(data);
				$("#destinyn").css("background","#FFF");
			}
		});
	});	
});
function selectDestiny(id,name){
	$("#destiny").val(id);
	$("#destinyn").val(name);
	$("#suggesstion-box-d").hide();
}

</script>
<script>
        // MAterial Date picker    
        $('#mdateini').bootstrapMaterialDatePicker({ weekStart: 0, time: false });
		$('#mdatefin').bootstrapMaterialDatePicker({ weekStart: 0, time: false });

        $('#timepicker').bootstrapMaterialDatePicker({ format: 'HH:mm', time: true, date: false });
        $('#date-format').bootstrapMaterialDatePicker({ format: 'dddd DD MMMM YYYY - HH:mm' });
    
        $('#min-date').bootstrapMaterialDatePicker({ format: 'DD/MM/YYYY HH:mm', minDate: new Date() });
        
</script>