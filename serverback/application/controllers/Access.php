<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_Controller {
	var $titulo = "Access";
	var $modulo = "access";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_access','model');
		
		$data=ChkToken($this->input->request_headers());
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
		$this->load->library('table');
		$tmpl = array (
			'table_open'          => '<table class="table">',
			'heading_row_start'   => '<tr">',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th>',
			'heading_cell_end'    => '</th>',
			'row_start'           => '<tr>',
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
	}
	public function index($segment=''){
        permisos(array(1,6));
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
				"b.id",
				"b.name",
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
		$data = array();
		if(!empty($results)){
			$total = $this->model->get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb);
			foreach($results as $r) {
				$data[] = array(
					$r->id,
					$r->doc,
					$r->name,
					$r->date_access
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
			permisos(array(1,6));
			$configuracion = $this->model->find($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
		}
		
		$data['types'] = array("Interna"=>"Interna","Externa"=>"Externa");
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1,6)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('employeess', 'Empleado', 'required');
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			//die('aaaa');
			if($type==2){
				die();		
			}else{
				$fecha=date("Y-m-d H:i:s");
				$dato = $this->model->insert(
					array(
						'employee' => $this->input->post('employeess'),
						'date_access' => $fecha,
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$dato['msg']= "Se ha registrado a las $fecha, el acceso de ".$this->input->post('employees');
					$dato['tipo'] =1;
				}else{
					$dato['msg']=$dato["res"];
					$dato['tipo'] =0;
				}
			}
			echo json_encode($dato);
		}
	}
	public function del_form(){		
		permisos(array(1,6)); 
		$this->model->update(
			array(
				'id' => $this->input->post('id'),
				'status' => 1,
				'del' => date("Y-m-d H:i:s"),
				'del_by' => $this->session->userdata('info')->id
			)
		);
		$dato['msg']="Registro Actualizado!";
		$dato['tipo'] =1;
		echo json_encode($dato);
	}
	function autocomplete($type=''){
		$var = $this->input->post('keyword');
		if(empty($type)){
			$automodel = $this->model->get_employees($var);
			$f="selectEmploye";			
			echo '<ul id="employees-list">';
		}else{
			$automodel = $this->model->get_depar($var);
			$f="selectDepar";			
			echo '<ul id="department-list">';
		}
		if(!empty($automodel)){
			foreach($automodel as $item) {
				echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.'</li>';
			}
		}else{
			echo "<li>No hay registros para mostrar</li>";
		}
		echo '</ul>';
	}
	function buscarr(){
		$fechai = $this->input->post('fechai');
		$fechaf = $this->input->post('fechaf');
		$deparid = $this->input->post('deparid');
		$hcompara = $this->input->post('hcompara');
		$expo = $this->input->post('expo');
		
		$this->form_validation->set_rules('fechai', 'Fecha inicial', 'required');
		$this->form_validation->set_rules('fechaf', 'Fecha final', 'required');
		
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$reports = $this->model->buscarr($fechai,$fechaf,$deparid);
			if(!empty($reports)){
				$this->table->set_heading('No.','Fecha reporte','Dif Min','Cedula', 'Nombre','Area','Contrato');
				$i=1;
				foreach ($reports as $item){
					$minutes='';
					if(!empty($hcompara)){
						$fe=explode(" ",$item->date_access);
						$dateTimeObject1 = date_create($fe[1]); 
						$dateTimeObject2 = date_create($hcompara.':00');
						$difference = date_diff($dateTimeObject1, $dateTimeObject2); 
						$minutes = $difference->days * 24 * 60;
						$minutes += $difference->h * 60;
						$minutes += $difference->i;
						if($fe[1]<=$hcompara){
							$minutes=0;
						}
					}
				
			
					$this->table->add_row($i,$item->date_access,$minutes,$item->id,$item->name,$item->catego,$item->type);
					$i++;
				}
			}
			$dato['tablar'] = $this->table->generate();
			$dato['tipo'] =1;
		}
		if($expo){
			echo $dato['tablar'];
		}else{
			echo json_encode($dato);
		}
	}
}