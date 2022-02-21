<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplyitem extends CI_Controller {
	var $titulo = "Insumos por item";
	var $modulo = "supplyitem";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_supplyitem','model');		
		
		$data=ChkToken($this->input->request_headers());
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
	}
	public function index($segment=''){
        permisos(array(1));
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
				"b.name",
				"a.item",
				"a.cons",
				"c.name",
				
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
					$r->insumo,
					$r->item,
					$r->iteem,
					$r->cons,
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
			permisos(array(1));
			$configuracion = $this->model->find($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
			$data['datos']['suppliess']=$this->model->get_supplies($user['supplies']);
			$data['datos']['items']=$this->model->get_itemm($user['item']);
		}
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */	
		// print_r($_POST);
		// DIE;
		$this->form_validation->set_rules('supplies', 'Insumo', 'required');
		$this->form_validation->set_rules('item', 'Item', 'required');
		$this->form_validation->set_rules('cons', 'Consumo', 'required');
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
							'supplies' => $this->input->post('supplies'),
							'item' => $this->input->post('item'),
							'cons' => $this->input->post('cons'),
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
						'supplies' => $this->input->post('supplies'),
						'item' => $this->input->post('item'),
						'cons' => $this->input->post('cons'),
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
	function autocomplete($type=''){
		$var = $this->input->post('keyword');
		if(empty($type)){
			$automodel = $this->model->get_suppliesss($var);
			$f="selectSuppli";			
			echo '<ul id="suppliesss-list">';
			if ($automodel){
				foreach($automodel as $item) {
					echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.'</li>';
				}		
			}
			echo '</ul>';
		}
	}
	
	function autocomplete2($type=''){
		$var = $this->input->post('keyword');
		if(empty($type)){
			$automodel = $this->model->get_item($var);
			$f="selectSitem";			
			echo '<ul id="itemss-list">';
			if($automodel){
				foreach($automodel as $item) {
					echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->item).'");>'.$item->item.'</li>';
				}
			}
			echo '</ul>';
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
}