<div class='container'>
	<div class='row '>
		<div class='col-lg-12 col-sm-12 mt-3'>	
			<?
				$options = array(
					"1" => "Todo",
					"2" => "Conforme",
					"3" => "No Conforme",
				);
				echo form_dropdown('filtro', $options, $tipo,'onchange="filtro4();" id="filtro"');
			?>
			<button onclick='exportar(<?=$id?>);' class='btn btn-warning'>Exportar</button>
		</div>
		
		<div class='col-12 mt-3'>
			<div class='table-responsive'>
			<?
				echo $tabla;	
			?>
			</div>
		</div>
	</div>
</div>