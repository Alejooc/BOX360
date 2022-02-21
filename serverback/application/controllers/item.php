<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Item extends CI_Controller {
	var $titulo = "Item";
	var $modulo = "item";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_item','model');		
		
		$data=ChkToken($this->input->request_headers());
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
	}
	public function index($segment=''){
        permisos(array(1,3));
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
				"a.item",
				"a.state",
				"c.name",
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
		$estados = array("1"=>"Activo","0"=>"InActivo");
		if(!empty($results)){
			$total = $this->model->get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb);
			foreach($results as $r) {
				$data[] = array(
					$r->id,
					$r->item,
					$r->name,
					$r->producto,
					$r->detalle,
					$estados[$r->state],
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
			permisos(array(1,3));
			$configuracion = $this->model->find($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
			$this->load->library('table');
			
			$detail = $this->model->get_detail($user['id']);
			$tmpl = array (
				'table_open'          => '<table class="table">',
				'heading_row_start'   => '<tr>',
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
			$this->table->set_heading('Accion', 'Orden', 'Area', 'Proceso', 'Meta');
			if(!empty($detail)){				
				foreach ($detail as $item){
					$links = '';
					$links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.','.$this->input->post("id").'); return false;'));
					$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'elim2('.$item->id.','.$this->input->post("id").'); return false;'));
					
					$this->table->add_row(
						$links,
						$item->order,
						$item->d,
						$item->dd." (X $item->measure)",
						$item->goal,
					);
				}
			}
			$data['table']= $this->table->generate();
			$data['procesos'] = $this->model->get_procesos();
			$data['measures'] = array('0'=>'X 0','1'=>'X 1','2'=>'X 2');
			$data['SumAdds'] = array('0'=>'N/A','1'=>'Fundas','2'=>'Sábanas');
			$data['SumEnds'] = array('0'=>'No','1'=>'Si');
		}
		
		$data['estados'] = array("1"=>"Activo","0"=>"InActivo");
		$data['productos'] = $this->model->get_productos();
		
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1,3)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('item', 'Orden de produccion', 'required|max_length[100]');
		// $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[100]');
		// if(!$this->input->post('id')>0){
			// $this->form_validation->set_rules('docum', 'Numero de documento', 'required');
		// }
			
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
							'item' => $this->input->post('item'),							
							'name' => $this->input->post('name'),							
							'state' => $this->input->post('state'),
							'measure' => $this->input->post('measure'),							
							'prom' => $this->input->post('prom'),
							'cloth' => $this->input->post('cloth'),
							'edited' => date("Y-m-d H:i:s"),
							'edited_by' => $this->session->userdata('info')->id
						)
					);
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
						'item' => $this->input->post('item'),							
						'name' => $this->input->post('name'),							
						'state' => $this->input->post('state'),
						'measure' => $this->input->post('measure'),							
						'prom' => $this->input->post('prom'),
						'cloth' => $this->input->post('cloth'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
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
	function autocomplete($type=''){
		$var = $this->input->post('keyword');
		if($type=='destiny'){
			$automodel = $this->model->get_destiny($var);	
			$f="selectDestiny";			
			echo '<ul id="destiny-list">';
			foreach($automodel as $item) {
				echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.'</li>';
			}
			echo '</ul>';
		}
	}
	public function get_form2(){
		$data['titulo'] = $this->titulo;
		if($this->input->post("id")>0){
			permisos(array(1,3));
			$configuracion = $this->model->find2($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$dato['datos'] = $user;
		}
		$dato['idp'] =$this->input->post("id2");
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send2($type){
		permisos(array(1,3));
		/* PERSONALIZAR CAMPOS REQUERIDOS */
		$this->form_validation->set_rules('destinyd', 'destinyd', 'required');
							
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			// Si hay un proceso que cuente producto desactiva los demas
			if ($this->input->post('SumEnd')){
				$this->model->update_disbleEnd(
					array(
						'item' => $this->input->post('idp'),
						'SumEnd' => 0
					)
				);
			}
			if($type==2){
				$registro = $this->model->find2($this->input->post('id2'));
				if (!empty($registro)) {
					$this->model->update_detail(
						array(
							'id' => $this->input->post('id2'),
							'destinyd' => $this->input->post('destinyd'),
							'measure' => $this->input->post('measure'),
							'SumAdd' => $this->input->post('SumAdd'),
							'SumEnd' => $this->input->post('SumEnd'),
							'order' => $this->input->post('order'),
							'edited' => date("Y-m-d H:i:s"),
							'edited_by' => $this->session->userdata('info')->id
						)
					);
					$dato['msg']="Registro Actualizado!";
					$dato['idp'] = $this->input->post('idp');
					$dato['tipo'] =1;
				} else {					
					$dato['msg']="El registro no existe";
					$dato['tipo'] =0;
				}
			} else {
				$dato = $this->model->insert_detail(
					array(
						'item' => $this->input->post('idp'),
						'destinyd' => $this->input->post('destinyd'),
						'measure' => $this->input->post('measure'),
						'SumAdd' => $this->input->post('SumAdd'),
						'SumEnd' => $this->input->post('SumEnd'),
						'order' => $this->input->post('order'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$dato['idp'] = $this->input->post('idp');
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
	public function del_form2(){		
		permisos(array(1)); 
		$id=$this->model->get_ppal($this->input->post("id"));
		$dato=$this->model->delete2($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato["id"] = $id;
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
}