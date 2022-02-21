<div class='container'>
	<div class='row '>
		<div class='col-12 mt-3'>
			<input type='hidden' id='ide' value=<?=$idE?>>
			<button onclick='formuM(0,<?=$idp?>);return false;' class='btn btn-info'>Crear Medida</button>
			<button onclick='formu(<?=$idE?>);return false;' class='btn btn-success'>Volver</button>
			<div class='table-responsive'>
			<?
				echo $tabla;	
			?>
			</div>
		</div>
	</div>
</div>