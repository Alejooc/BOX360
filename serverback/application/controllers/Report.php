<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {
	var $titulo = "Reportes";
	var $modulo = "report";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_report','model');		
		
		$data=ChkToken($this->input->request_headers());
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
	}
	public function reportes($segment=''){
        permisos(array(1,3,4));
		$draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
		$order = $this->input->post("order");
		$search = $this->input->post("columns");
		  
        $col = 0;
        $dir = "";
        if(!empty($order)) {
			foreach($order as $o) {
				$col = $o['column'];
				$dir= $o['dir'];
			}
			if($dir != "asc" && $dir != "desc") {
			   $dir = "asc";
			}
			/* PERSONALIZAR NOMBRE DE LAS COLUMNAS */
			$columns_valid = array(
				"",
				"a.id",
				"a.op",
				"b.name",
				"c.item",
				"h.name",
			);
			if(!isset($columns_valid[$col])) {
			   $order = null;
			} else {
			   $order = $columns_valid[$col];
			}
        }
		$cb="";
		foreach($search as $itemse){
			$cb .= $itemse['search']['value'];
		}
		// Si eesta vacia no hay busqueda por columnas
		if(empty($cb)){
			$customSearch=$this->input->post("search");
			//Si hay busqueda personalizada que la aplique
			if(!empty($customSearch['value'])){
				$cb = $customSearch['value'];
			}
		}
        $results = $this->model->get_all($start, $length, $order, $dir,$search,$columns_valid,$cb);
		// print_r($results);
		$data = array();
		$estados = array("1"=>"Activo","0"=>"Suspendido");
		if(!empty($results)){
			$total = $this->model->get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb);
			foreach($results as $r) {
				$data[] = array(
					$r->id,
					$r->op,
					$r->empleado,
					$r->item.' '.$r->name,
					$r->cant,
					$r->process,
					$r->created
				);				
			}
		}else{
			$total=0;
		}
		
		$output = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data,
			"tipo" => 1
		);
		echo json_encode($output);
		exit();
	}
	public function get_form(){
		$data['titulo'] = $this->titulo;
		if($this->input->post("id")>0){
			permisos(array(1,3,4));
			$configuracion = $this->model->find($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
		}

		$data['empleados'] = $this->model->empleados();
		
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function find(){
		if($this->input->post("id")>0){
			permisos(array(1,3,4));
			$configuracion = $this->model->find($this->input->post("id"));
			$dato['ok'] =$configuracion->ok;
			$dato['nok'] =$configuracion->nook;
			$dato['cant'] =$configuracion->cant;
			$dato['tipo'] =1;
			echo json_encode($dato);
		}
	}
	public function index(){
		permisos(array(1,3,4));
		$this->load->library('table');
		
		$tmpl = array (
			'table_open'          => '<table class="table">',
			'heading_row_start'   => '<tr">',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th>',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr style="background: #fec107;Color:#fff;">',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',
			'row_alt_start'       => '<tr>',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
		);
		$this->table->set_template($tmpl);
		$detail = $this->model->get_OpsItems();
		$this->table->set_heading('Ordenes de producción');
		if(!empty($detail)){				
			foreach ($detail as $item){
				$this->table->add_row(
					'OP No. '.$item->op
				);
				$items = $this->model->get_Itemss($item->id);
				$tabla='';
				if(!empty($items)){
					foreach ($items as $itemp){
						$linkp ='<a href="#" onclick=itemsmodal("'.$item->id.'","'.$itemp->item.'");return false; data-bs-toggle="modal" data-bs-target="#reporteModal" data-whatever="@getbootstrap"><i class="fas fa-check text-warning m-r-10"></i></a>';
						$tabla .= "<tr>
							<td>$linkp</td>
							<td>$itemp->itemid $itemp->name</td>
						</tr>";
					}
				}
				$detalle ='
					<table class="table">
						<tr>
							<th>Accion</th>
							<th>item</th>
						</tr>
						'.$tabla.'
					</table>
				';
				$cell_data = array(
					'data' => $detalle
				);
				$this->table->add_row($cell_data);
			}
		}
		$dato['table']= $this->table->generate();
		// print_r($data);
		// $dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
		echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1,3,4));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('item', 'item', 'required');
		$this->form_validation->set_rules('cant', 'cant', 'required');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			//die('aaaa');
			if($type==2){
				$registro = $this->model->find($this->input->post('id'));
				if (!empty($registro)) {
					$this->model->update(
						array(
							'id' => $this->input->post('id'),
							'employee' => $this->input->post('employee'),							
							'op' => $this->input->post('op'),
							'item' => $this->input->post('item'),
							'cant' => $this->input->post('cant'),
							'edited' => date("Y-m-d H:i:s"),
							'edited_by' => $this->session->userdata('info')->id
						)
					);
					updatePtjOP($this->input->post('op'),$this->input->post('item'),1);
					$dato['msg']="Registro Actualizado!";
					$dato['tipo'] =1;
				} else {					
					$dato['msg']="El registro no existe";
					$dato['tipo'] =0;
				}				
			}else{
				$dato = $this->model->insert(
					array(
						'id' => $this->input->post('id'),
						'employee' => $this->input->post('employee'),							
						'op' => $this->input->post('op'),
						'item' => $this->input->post('item'),
						'cant' => $this->input->post('cant'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					updatePtjOP($this->input->post('op'),$this->input->post('item'),1);
					$dato['msg']="Registro Creado!";
					$dato['tipo'] =1;
				}else{
					$dato['msg']=$dato["res"];
					$dato['tipo'] =0;
				}
			}
			echo json_encode($dato);
		}
	}
	public function saveReport(){
		permisos(array(1,3,4));
		/* PERSONALIZAR CAMPOS REQUERIDOS */
		$this->form_validation->set_rules('op', 'OP', 'required');
		$this->form_validation->set_rules('item', 'Item', 'required');
		$this->form_validation->set_rules('employeess', 'Empleado', 'required');
		$this->form_validation->set_rules('tareasL', 'Tarea', 'required');
		$this->form_validation->set_rules('a', 'Cantidad A', 'required|is_natural');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$a=$this->input->post('a');
			$b=$this->input->post('b');
			$c=$this->input->post('c');
			$r=$this->input->post('r');
			if (!is_numeric($a)){
				$a=0;
			}
			if (!is_numeric($b)){
				$b=0;
			}
			if (!is_numeric($c)){
				$c=0;
			}
			if (!is_numeric($r)){
				$r=0;
			}
			$dato = $this->model->insert_report(
				array(
					'task' => $this->input->post('tareasL'),
					'a' => $a,
					'b' => $b,
					'c' => $c,
					'r' => $r,
					'cant' => $a+$b+$c+$r,
					'created' => date("Y-m-d H:i:s"),
					'created_by' => $this->session->userdata('info')->id
				)
			);
			if ($dato["res"] == "ok"){
				updatePtjOP($this->input->post('op'),$this->input->post('item'),1);
				$dato['msg']="Registro Creado!";
				$dato['tipo'] =1;
			}else{
				$dato['msg']=$dato["res"];
				$dato['tipo'] =0;
			}
			echo json_encode($dato);
		}
	}
	function autocomplete($type=''){
		$employee = $this->input->post('keyword');
		$op = $this->input->post('op');
		$item = $this->input->post('item');
		if(empty($type)){
			$automodel = $tareas = $this->model->get_employees($op,$item,$employee);
			$f="selectEmploye";			
			echo '<ul id="employees-list">';
		}
		if (!empty($automodel)){
			foreach($automodel as $item) {
				echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.'</li>';
			}
		}
		echo '</ul>';
	}
	public function del_form(){		
		permisos(array(1)); 
		$dato=$this->model->delete($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
	public function save_calidadr(){
		permisos(array(1,3,4));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('taskid', 'Tarea', 'required');
		$this->form_validation->set_rules('conforme', 'Conforme', 'required|is_natural');
		$this->form_validation->set_rules('noconforme', 'No Conforme', 'required|is_natural');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$dato=$this->model->update(
				array(
					'id' => $this->input->post('taskid'),
					'ok' => $this->input->post('conforme'),							
					'nook' => $this->input->post('noconforme'),
					'edited' =>date("Y-m-d H:i:s"),
					'edited_by' => $this->session->userdata('info')->id
				)
			);
			$dato['msg']="Registro Actualizado!";
			$dato['tipo'] =1;
		}
		echo json_encode($dato);
	}
	public function edicionSave(){
		permisos(array(1,3,4));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('id', 'Tarea', 'required');
		$this->form_validation->set_rules('icantd', 'Cantidad', 'required');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$dato=$this->model->update(
				array(
					'id' => $this->input->post('id'),
					'cant' => $this->input->post('icantd'),
					'edited' =>date("Y-m-d H:i:s"),
					'edited_by' => $this->session->userdata('info')->id
				)
			);
			$dato['msg']="Registro Actualizado!";
			$dato['tipo'] =1;
		}
		
		echo json_encode($dato);
	}
	public function ConsultaTareas(){
		permisos(array(1,3,4));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('op', 'Orden de produccion', 'required|is_natural');
		$this->form_validation->set_rules('item', 'Item', 'required|is_natural');
		$this->form_validation->set_rules('employee', 'Empleado', 'required|is_natural');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$op = $this->input->post('op');
			$item  = $this->input->post('item');
			$employee  = $this->input->post('employee');
			
			$data['tipo']=1;
			$tareas = $this->model->get_tareas($op,$item,$employee);
			if(!empty($tareas)){
				$data['tareas'] = form_dropdown('tareasL', $tareas, '',"id=tareasL class = form-control");
			}else{
				$data['tareas'] = '<span style="color:red">El empleado no tiene tareas asignadas para esta OP</span>';
			}
			echo json_encode($data);
		}
	}
}