<div class='container'>
	<div class='row '>
		<div class='col-12 mt-3'>
			<input type="text" name="busca" id="busca"> <button onclick="Buscar();return false;" class="btn btn-warning"><i class="fas fa-search"></i></button>
			<button onclick='exportar(<?=$id?>);' class='btn btn-warning'>Exportar</button>				
			<div class='table-responsive'>
			<?
				echo $tabla;	
			?>
			</div>
		</div>
	</div>
</div>