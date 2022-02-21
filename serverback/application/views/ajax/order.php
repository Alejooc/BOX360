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
					$datos['op'] = $op;
					// $datos['rollos'] = '';
					$datos['machine'] = '';
					$datos['machinen'] = '';
					$datos['destiny'] = '';
					$datos['destinyn'] = '';
					$datos['observations'] = '';
					
					
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
					<label for="op" class="col-md-3 col-xs-12 col-form-label">Orden de produccion *:
					<h6>Por favor Ingrese orden de produccion</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="op" value="<?=$datos["op"]?>" id="op" required>
					</div>
				</div>
				<div class="form-group">
					<label for="machine" class="col-md-3 col-xs-12 col-form-label">Maquina *:
					<h6>Por favor Ingrese la maquina</h6></label>
					<div class="col-12">
						<input type="hidden" name="machine" value="<?=$datos["machine"]?>" id="machine" required>
						<input autocomplete="off" type="text" class="form-control" name="machinen" value="<?=$datos["machinen"]?>" id="machinen" required>
						<div id="suggesstion-box"></div>
					</div>
				</div>
				<div class="form-group">
					<label for="destiny" class="col-md-3 col-xs-12 col-form-label">Destino *:
					<h6>Por favor Ingrese el destino</h6></label>
					<div class="col-12">
						<input type="hidden" id="destiny" name="destiny" value="<?=$datos["destiny"]?>" required>
						<input autocomplete="off" type="text" class="form-control" name="destinyn" value="<?=$datos["destinyn"]?>" id="destinyn" required>
						<div id="suggesstion-box-d"></div>
					</div>
				</div>
				<div class="form-group">
					<label for="observations" class="col-md-3 col-xs-12 col-form-label">Observaciones:
					<h6>Por favor escoja el estado del usuario</h6></label>
					<div class="col-12">						
						<textarea class="form-control" name="observations" id="observations"><?=$datos["observations"]?></textarea>
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
								<button onclick="formu2(0,<?=$datos["id"]?>);" class="btn btn-success w-100" data-toggle="modal" data-target="#MyModalParte">Agregar Item</button>
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
					<h3>Items</h3>
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
				<h4 class="modal-title" id="detallenModalLabel">Crear Item</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<?
				if (empty($datos2)) {
					$datos2['id'] = 0;
					$datos2['item'] = '';
					$datos2['itemn'] = '';
					$datos2['rolls'] = '';
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
				<!--
				<div class="form-group row">
					<label for="qty2" class="col-md-3 col-xs-12 col-form-label">Item *:
					<h6>Por favor Ingresa el Item</h6></label>
					<div class="col-9">
						<?= form_dropdown('item', $items, $datos2['item'],"class = form-control");?>
					</div>
				</div>
				-->
				<div class="form-group">
					<label for="item" class="col-md-3 col-xs-12 col-form-label">Item *:
					<h6>Por favor Ingrese el item</h6></label>
					<div class="col-12">
						<input type="hidden" id="item" name="item" value="<?=$datos2["item"]?>" required>
						<input autocomplete="off" type="text" class="form-control" name="itemn" value="<?=$datos2["itemn"]?>" id="itemn" required>
						<div id="suggesstion-box-item"></div>
					</div>
				</div>
				<div class="form-group row">
					<label for="marca2" class="col-md-3 col-xs-12 col-form-label">Rollos *:
					<h6>Por favor Ingrese la cantidad de rollos</h6></label>
					<div class="col-9">						
						<input class="form-control" value="<?=$datos2['rolls']?>" name="rolls" id="rolls" type="number" required>
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
<!-- <link href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.min.css" rel="stylesheet">
     <script src="https://code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>-->
<script>
$(document).ready(function(){
	$("#machinen").keyup(function(){
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/"+modulo+"/autocomplete/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			data:'keyword='+$(this).val(),
			success: function(data){
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data);
				$("#machinen").css("background","#FFF");
			}
		});
	});
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
	$("#itemn").keyup(function(){
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/"+modulo+"/autocomplete/item",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			data:'keyword='+$(this).val(),
			success: function(data){
				$("#suggesstion-box-item").show();
				$("#suggesstion-box-item").html(data);
				$("#itemn").css("background","#FFF");
			}
		});
	});	
});
function selectMachine(id,name){
	$("#machine").val(id);
	$("#machinen").val(name);
	$("#suggesstion-box").hide();
}
function selectDestiny(id,name){
	$("#destiny").val(id);
	$("#destinyn").val(name);
	$("#suggesstion-box-d").hide();
}

</script>