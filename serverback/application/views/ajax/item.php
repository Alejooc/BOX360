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
					$datos['item'] = '';
					$datos['state'] = '';
					$datos['name'] = '';
					$datos['measure'] = '';
					$datos['prom'] = '';
					$datos['cloth'] = '';
					$datos['cloth'] = '';
					$datos['productd'] = '';

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
					<label for="item" class="col-md-3 col-xs-12 col-form-label">Item *:
					<h6>Por favor Ingrese el item</h6></label>
					<div class="col-12">
						<input type="number" class="form-control" name="item" value="<?=$datos["item"]?>" id="item" required>
					</div>
				</div>
				<div class="form-group">
					<label for="name" class="col-md-3 col-xs-12 col-form-label">Nombre *:
					<h6>Por favor Ingrese el Nombre</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="name" value="<?=$datos["name"]?>" id="name" required>
					</div>
				</div>
				<div class="form-group">
					<label for="measure" class="col-md-3 col-xs-12 col-form-label">Medida *:
					<h6>Por favor Ingrese la Medida</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="measure" value="<?=$datos["measure"]?>" id="measure" >
					</div>
				</div>
				<div class="form-group">
					<label for="prom" class="col-md-3 col-xs-12 col-form-label">Promedio *:
					<h6>Por favor Ingrese el Promedio</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="prom" value="<?=$datos["prom"]?>" id="prom" >
					</div>
				</div>
				<div class="form-group">
					<label for="cloth" class="col-md-3 col-xs-12 col-form-label">Tipo de tela *:
					<h6>Por favor Ingrese el Tipo de tela</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="cloth" value="<?=$datos["cloth"]?>" id="cloth" >
					</div>
				</div>
				<div class="form-group">
					<label for="productd" class="col-md-3 col-xs-12 col-form-label">Producto principal *:
					<h6>Por favor Ingrese el Producto principal</h6></label>
					<div class="col-12">
						<?= form_dropdown('productd', $productos, $datos['productd'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="state" class="col-md-3 col-xs-12 col-form-label">Estado *:
					<h6>Por favor escoja el estado del Item</h6></label>
					<div class="col-12">						
						<?= form_dropdown('state', $estados, $datos['state'],"class = form-control");?>
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
								<button onclick="formu2(0,<?=$datos["id"]?>);" class="btn btn-success w-100" data-toggle="modal" data-target="#MyModalParte">Agregar proceso</button>
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
					<h3>Procesos</h3>
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
				<h4 class="modal-title" id="detallenModalLabel">Detalle</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<?
				if (empty($datos2)) {
					$datos2['id'] = 0;
					$datos2['destinyd'] = '';
					$datos2['measure'] = '';
					$datos2['SumAdd'] = '';
					$datos2['SumEnd'] = '';
					$datos2['order'] = '';
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
					<label for="destinyd" class="col-md-3 col-xs-12 col-form-label">Proceso *:
					<h6>Por favor Ingresa el proceso a asignar</h6></label>
					<div class="col-9">
						<?= form_dropdown('destinyd', $procesos, $datos2['destinyd'],"name='destinyd' id='destinyd' class = form-control");?>
					</div>
				</div>
				<div class="form-group row">
					<label for="measure" class="col-md-3 col-xs-12 col-form-label">Medición del proceso *:
					<h6>Por favor Ingresa la medición del proceso</h6></label>
					<div class="col-9">
						<?= form_dropdown('measure', $measures, $datos2['measure'],"name='measure' id='measure2' class = form-control");?>
					</div>
				</div>
				<div class="form-group row">
					<label for="SumAdd" class="col-md-3 col-xs-12 col-form-label">Suma adiciónal *:
					<h6>Por favor Ingresa la suma adicional</h6></label>
					<div class="col-9">
						<?= form_dropdown('SumAdd', $SumAdds, $datos2['SumAdd'],"name='SumAdd' id='SumAdd' class = form-control");?>
					</div>
				</div>
				<div class="form-group row">
					<label for="SumEnd" class="col-md-3 col-xs-12 col-form-label">Cuenta producto terminado? *:
					<h6>Por favor Ingresa si este proceso mide el producto terminado</h6></label>
					<div class="col-9">
						<?= form_dropdown('SumEnd', $SumEnds, $datos2['SumEnd'],"name='SumEnd' id='SumEnd' class = form-control");?>
					</div>
				</div>
				<div class="form-group row">
					<label for="order" class="col-md-3 col-xs-12 col-form-label">Orden del proceso *:
					<h6>Por favor Ingresa el orden del proceso</h6></label>
					<div class="col-9">
						<input type="numeric" class="form-control" name="order" value="<?=$datos2["order"]?>" id="order" >
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