<div class='container'>
	<div class='row '>
		<div class='col-lg-4 col-sm-12 mt-3'>
			<button onclick="CreaTask(<?=$tareaid?>);return false;" data-bs-toggle="modal" data-bs-target="#taskCreateModal" data-whatever="@getbootstrap" class="btn btn-warning">Crear Tarea</button>
		</div>
		<div class='col-lg-7 col-sm-12 mt-3'>	
			<input type='hidden' id='tareaid' name='tareaid' value=<?=$tareaid?>> 
			Buscar por: &nbsp;
			<select id="buscaTipoTask" name="buscaTipoTask">
				<option value='0'>Todo</option>
				<option value='1'>Empleado</option>
				<option value='2'>Item</option>
				<option value='3'>Proceso</option>
			</select>
			<input type='text' id='buscaDatoTask' name='buscaDatoTask'> 
			<button onclick="BuscarTask();return false;" class="btn btn-warning"><i class="fas fa-search"></i></button>
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