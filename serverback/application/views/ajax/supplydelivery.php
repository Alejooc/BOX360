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
					$datos['op'] = '';
					$datos['item'] = '';
					$datos['code'] = '';
					$datos['deliverydate'] = '';
					$datos['status'] = '';

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
					<label for="op" class="col-md-3 col-xs-12 col-form-label">OP *:
					<h6>Por favor escoja la op</h6></label>
					<div class="col-12">						
						<?= form_dropdown('op', $ops, $datos['op'],"id='op' onchange='getItemsOp();' class = 'form-control' ");?>
					</div>
				</div>
				<div class="form-group">
					<label for="item" class="col-md-3 col-xs-12 col-form-label">Item *:
					<h6>Por favor escoja el item</h6></label>
					<div class="col-12">						
						<span id='itemfd'>
							<div class="col-12">						
								<?= form_dropdown('item', $items, $datos['item'],"class = form-control");?>
							</div>
						</span>
					</div>
				</div>
				<div class="form-group">
					<label for="deliverydate" class="col-md-3 col-xs-12 col-form-label">Fecha de entrega *:
					<h6>Por favor escoja el item</h6></label>
					<div class="col-12">						
						<input type="date" class="form-control" name="deliverydate" value="<?=$datos["deliverydate"]?>" id="deliverydate" required>
					</div>
				</div>
				<div class="form-group">
					<label for="code" class="col-md-3 col-xs-12 col-form-label">Codigo de ensanble:
					<h6>Por favor Ingrese el codigo de ensanble</h6></label>
					<div class="col-12">
						<input type="text" class="form-control" name="code" value="<?=$datos["code"]?>" id="code">
					</div>
				</div>
				<div class="form-group">
					<label for="status" class="col-md-3 col-xs-12 col-form-label">Estado *:
					<h6>Por favor escoja el estdao</h6></label>
					<div class="col-12">						
						<?= form_dropdown('status', $statusL, $datos['status'],"class = form-control");?>
					</div>
				</div>
				<div class="form-group">
					<label for="status" class="col-md-3 col-xs-12 col-form-label">Comentarios *:
					<h6>Por favor ingrese los comentarios</h6></label>
					<div class="col-12">						
						<textarea class="form-control"  name='comment' id='comment'><?=$datos["comment"]?></textarea>
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
								<button onclick="formu2(0,<?=$datos["id"]?>);" class="btn btn-success w-100" data-toggle="modal" data-target="#ModalInsumos">Agregar insumo</button>
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
					<h3>Insumos x item</h3>
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
				<h4 class="modal-title" id="detallenModalLabel">Insumo</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true"></span></button>
			</div>
			<div class="modal-body">
				<?
				if (empty($datos2)) {
					$datos2['id'] = 0;
					$datos2['supply'] = '';
					$datos2['supplyn'] = '';
					$datos2['qty'] = '';
					$accion="accs";
				}else{
					$accion="acce";
				}
				echo form_open_multipart('#', array('class' => "col-md-12 $accion", 'id' => 'forminterno1'));
				?>				
				<div class="form-group row">
					<input name="id2" id="id2" value="0" type="hidden">
					<input name="deliverydid" id="deliverydid" type="hidden">
				</div>
				<div class="form-group row">
					<label for="qty" class="col-md-3 col-xs-12 col-form-label">Insumo *:
					<h6>Por favor Ingresa el insumo</h6></label>
					<div class="col-9">
						<input type="hidden" id="supply" name="supply" value="<?=$datos2["supply"]?>" required>
						<input onkeyup='buscainsumo();return false;' type="text" autocomplete="false" class="form-control" name="supplyn" value="<?=$datos2["supplyn"]?>" id="supplyn" required>
						<div id="suggesstion-box"></div>
					</div>
				</div>
				<div class="form-group row">
					<label for="qty" class="col-md-3 col-xs-12 col-form-label">Cantidad *:
					<h6>Por favor Ingresa el nombre</h6></label>
					<div class="col-9">
						<input class="form-control" value="<?=$datos2["qty"]?>" name="qty" id="qty" type="text" required>
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