<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {
	var $titulo = "Productos";
	var $modulo = "product";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_product','model');		
		
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
				"id",
				"name",
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
					$r->name
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
			
			$detail = $this->model->get_detail($this->input->post("id"));
			$this->table->set_heading('Accion', 'Producto');
			if(!empty($detail)){				
				foreach ($detail as $item){
					$links = '';
					$links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.',0); return false;'));
					$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'elim2('.$item->id.'); return false;'));
					// $links .= anchor('#' ,'<i class="fa fa-cut text-warning m-r-10"></i>', array('onclick'=>'measures('.$item->id.','.$this->input->post("id").'); return false;'));
					
					$this->table->add_row(
						$links,
						$item->name
					);
				}
			}
			$data['table']= $this->table->generate();
		}
		
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1,3)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('name', 'Nombre', 'required|max_length[100]');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			//die('aaaa');
			if($type==2){
				$opid=$this->input->post('id');
				$registro = $this->model->find($this->input->post('id'));
				if (!empty($registro)) {
					$this->model->update(
						array(
							'id' => $this->input->post('id'),
							'name' => $this->input->post('name')
						)
					);
					$dato['msg']="Registro Actualizado!";
					$dato['tipo'] =1;
					$dato['opid'] =$opid;
				} else {					
					$dato['msg']="El registro no existe";
					$dato['tipo'] =0;
				}				
			}else{
				$dato = $this->model->insert(
					array(
						'id' => $this->input->post('id'),
						'name' => $this->input->post('name')
					)
				);
				$opid = $dato['id'];
				if ($dato["res"] == "ok"){
					$dato['opid'] =$opid;
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
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send2($type){
		permisos(array(1,3)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */
		$this->form_validation->set_rules('productid', 'productid', 'required');
							
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			if($type==2){
				$registro = $this->model->find2($this->input->post('id2'));
				if (!empty($registro)) {
					$this->model->update_detail(
						array(
							'id' => $this->input->post('id2'),
							'name' => $this->input->post('name1')
						)
					);
					$dato['msg']="Registro Actualizado!";
					$dato['productid'] = $this->input->post('productid');
					$dato['tipo'] =1;
				} else {					
					$dato['msg']="El registro no existe";
					$dato['tipo'] =0;
				}
			} else {				
				$dato = $this->model->insert_detail(
					array(
						'product' => $this->input->post('productid'),
						'name' => $this->input->post('name1'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$dato['productid'] = $this->input->post('productid');
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
	public function measuresIndex(){
        permisos(array(1,3));
		$idp = $this->input->post('id');
		$idE = $this->input->post('idE');
		$reports = $this->model->get_all_measure($idp);
		$dato['tabla']="Sin registros";
		if(!empty($reports)){
			$this->table->set_heading('Accion','Nombre');
			foreach ($reports as $item){
				$links = '';
				$links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formuM('.$item->id.','.$idp.'); return false;'));
				$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'elimM('.$item->id.','.$idp.'); return false;'));
				$this->table->add_row($links,$item->name);
			}
			$dato['tabla'] = $this->table->generate();
		}
		$dato['idp']=$idp;
		$dato['idE']=$idE;
		$data['msg'] = $this->load->view('ajax/productm',$dato,TRUE);
		echo json_encode($data);
	}
	public function get_formM(){
		permisos(array(1,3));
		$data['titulo'] = $this->titulo;
		if($this->input->post("id")>0){
			$configuracion = $this->model->findM($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
		}
		$data['idp'] = $this->input->post("idp");
		$data['ide'] = $this->input->post("ide");
		$dato['msg'] = $this->load->view('ajax/productmf',$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_sendM($type){
		permisos(array(1,3)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */
		$this->form_validation->set_rules('idp', 'Id de producto', 'required');
		$this->form_validation->set_rules('name', 'Nombre', 'required');
							
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			if($type==2){
				$registro = $this->model->findM($this->input->post('id'));
				if (!empty($registro)) {
					$this->model->updateM(
						array(
							'id' => $this->input->post('id'),
							'name' => $this->input->post('name'),
							'edited' => date("Y-m-d H:i:s"),
							'edited_by' => $this->session->userdata('info')->id
						)
					);
					$dato['msg']="Registro Actualizado!";
					$dato['idp'] = $this->input->post('idp');
					$dato['ide'] = $this->input->post('ide');
					$dato['tipo'] =1;
				} else {					
					$dato['msg']="El registro no existe";
					$dato['tipo'] =0;
				}
			} else {				
				$dato = $this->model->insertM(
					array(
						'productd' => $this->input->post('idp'),
						'name' => $this->input->post('name'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$dato['idp'] = $this->input->post('idp');
					$dato['ide'] = $this->input->post('ide');
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
	public function del_formM(){		
		permisos(array(1)); 
		// $id=$this->model->get_ppal($this->input->post("id"));
		$dato=$this->model->deleteM($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato['idp'] = $this->input->post('idp');
			$dato['ide'] = $this->input->post('ide');
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
}