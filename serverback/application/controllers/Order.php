<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {
	var $titulo = "Orden";
	var $modulo = "order";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_order','model');		
		
		$data=ChkToken($this->input->request_headers());
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
		
		$this->load->library('table');
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
	}
	public function index($segment=''){
        permisos(array(1,2,3));
		$draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
		$order = $this->input->post("order");
		$search = $this->input->post("columns");
		  // print_r($_POST);
		  // die();
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
				"a.op",
				"b.name",
				"c.name",
				"d.name"
			);
			if(!isset($columns_valid[$col])) {
			   $order = null;
			} else {
			   $order = $columns_valid[$col];
			}
        }
		// Busqueda personalizada arranca vacia
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
					$r['id'],
					$r['op'],
					$r['estados'],
					$r['machinen'],
					$r['destinyn'],
					$r['created'],
				);				
			}
			// $total = count($results);
		}else{
			$total=0;
		}
		$output = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $data,
			"tipo" => 1,
			"states" => 'Es una prubra'
		);
		echo json_encode($output);
		exit();
	}	
	public function get_form(){
		$data['titulo'] = $this->titulo;
		if($this->input->post("id")>0){
			permisos(array(1,2,3));
			$configuracion = $this->model->find($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
			$this->load->library('table');
			$detail = $this->model->get_detail($this->input->post("id"));
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
			$this->table->set_heading('Accion', 'Item', 'Rollos','Fecha');
			if(!empty($detail)){				
				foreach ($detail as $item){
					$links = '';
					$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'elim2('.$item->id.','.$this->input->post("id").'); return false;'));
					
					$this->table->add_row(
						$links,
						$item->item.' '.$item->name,
						$item->rolls,
						$item->created
					);
				}
			}
			$data['table']= $this->table->generate();
		}else{
			$data['op']=$this->model->getNoOrden();
		}
		$data['estados'] = $this->model->estados();
		$data['items'] = $this->model->items();

		
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1,3));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('op', 'Orden de produccion', 'required|is_natural');
		$this->form_validation->set_rules('machine', 'Maquina', 'required|is_natural');
		$this->form_validation->set_rules('destiny', 'Destino', 'required|is_natural');
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
				$opid=$this->input->post('id');
				$registro = $this->model->find($this->input->post('id'));
				if (!empty($registro)) {
					$this->model->update(
						array(
							'id' => $this->input->post('id'),
							'op' => $this->input->post('op'),
							'destiny' => $this->input->post('destiny'),
							'machine' => $this->input->post('machine'),
							'observations' => $this->input->post('observations'),
							'edited' => date("Y-m-d H:i:s"),
							'edited_by' => $this->session->userdata('info')->id
						)
					);
					$this->model->insertH(
						array(
							'order' => $opid,
							'state' => $registro->state,
							'comment' => 'Orden Actualizada',
							'created' => date("Y-m-d H:i:s"),
							'created_by' => $this->session->userdata('info')->id
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
						'op' => $this->input->post('op'),							
						'destiny' => $this->input->post('destiny'),
						'machine' => $this->input->post('machine'),
						'observations' => $this->input->post('observations'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				$opid = $dato['id'];
				if ($dato["res"] == "ok"){
					$dato['msg']="Registro Creado!";
					$dato['tipo'] =1;
					$dato['opid'] =$opid;
					$this->model->insertH(
						array(
							'order' => $opid,
							'state' => 1,
							'comment' => 'Orden Creada',
							'created' => date("Y-m-d H:i:s"),
							'created_by' => $this->session->userdata('info')->id
						)
					);
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
		
		if(empty($type)){
			$automodel = $this->model->get_machines($var);
			$f="selectMachine";			
			echo '<ul id="machine-list">';
		}else if ($type=='destiny'){
			$automodel = $this->model->get_destiny($var);	
			$f="selectDestiny";			
			echo '<ul id="destiny-list">';
		}else if ($type=='employee'){
			$automodel = $this->model->get_employee($var);	
			$f="selectEmployeet";			
			echo '<ul id="employee-list">';
		}else if ($type=='item'){
			$automodel = $this->model->get_item($var);	
			$f="selectItem";			
			echo '<ul id="item-list">';
		}
		foreach($automodel as $item) {
			if ($type=='employee'){
				echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.' ('.$item->destiny.')</li>';
			}else{
				echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.'</li>';
			}
		}
		echo '</ul>';
	}
	public function get_states(){
		$estados = $this->model->estados();
		// $data['estados'] = $this->model->estados();
		$data['estados'] = form_dropdown('state', $estados, '',"id=state class = form-control");
		$data['tipo']=1;
		echo json_encode($data);
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
		$this->form_validation->set_rules('idp', 'idp', 'required');
							
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
							'item' => $this->input->post('item'),
							'rolls' => $this->input->post('rolls'),
							'order' => $this->input->post('idp'),
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
						'item' => $this->input->post('item'),
						'rolls' => $this->input->post('rolls'),
						'order' => $this->input->post('idp'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$procesosxitem = $this->model->procesosxitem($this->input->post('item'));
					if(!empty($procesosxitem)){
						foreach ($procesosxitem as $proceso) {
							$empleadosxproceso = $this->model->empleadosxproceso($proceso->destinyd);
							if(!empty($empleadosxproceso)){
								foreach ($empleadosxproceso as $empleado) {
									$this->model->insert_process(
										array(
											'order' => $this->input->post('idp'),
											'proccess' => $proceso->destinyd,
											'employee' => $empleado->employee,
											'item' => $this->input->post('item'),
											'created' => date("Y-m-d H:i:s"),
											'created_by' => $this->session->userdata('info')->id
										)
									);
								}
							}
						}
					}
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
	// CALIDAD
	public function get_calidad(){
		permisos(array(1,3));
		$detail = $this->model->get_calidad($this->input->post("id"));
		
		$this->table->set_heading('Accion', 'Calidad', 'Uni. Audit', 'Part. Imper', '%');
		if(!empty($detail)){				
			foreach ($detail as $item){
				$links = '';
				// $links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.','.$this->input->post("id").'); return false;'));
				$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'del_calidad('.$item->id.','.$this->input->post("id").'); return false;'));
				$this->table->add_row(
					$links,
					$item->quality,
					$item->unid_audited,
					$item->parts_imperfect,
					$item->ptj.' %'
				);
			}
		}
		$data['table']= $this->table->generate();
		$data['tipo']=1;
		echo json_encode($data);
	}
	public function save_calidad(){
		permisos(array(1,3));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('idca', 'Orden de produccion', 'required|max_length[100]');
		$this->form_validation->set_rules('unid_audited', 'Unidades auditadas', 'required|is_natural');
		$this->form_validation->set_rules('parts_imperfect', 'Unidades imperfectas', 'required|is_natural');
		$this->form_validation->set_rules('type_quality', 'Calidad', 'required');
					
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$dato = $this->model->insert_calidad(
				array(
					'order' => $this->input->post('idca'),
					'quality' => $this->input->post('type_quality'),
					'unid_audited' => $this->input->post('unid_audited'),
					'parts_imperfect' => $this->input->post('parts_imperfect'),
					'ptj' => ($this->input->post('parts_imperfect') * $this->input->post('unid_audited'))/100,
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
			echo json_encode($dato);
		}
	}
	public function del_calidad(){		
		permisos(array(1)); 
		$dato=$this->model->del_calidad($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato["id"] = $this->input->post("id2");
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
	// CORTE
	public function get_corte(){
		permisos(array(1,2,3));
		$detail = $this->model->get_corte($this->input->post("id"));
		$dato['tipo'] =0;
		$this->table->set_heading('Item', 'Un. cor', 'Metros', 'Fundas', 'Sábanas', 'Con. cor');
		$itemsArr=array();
		if(!empty($detail)){				
			foreach ($detail as $item){
				$links = '';
				// $links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.','.$this->input->post("id").'); return false;'));
				// $links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'del_calidad('.$item->id.','.$this->input->post("id").'); return false;'));
				$this->table->add_row(
					$item->name,
					$item->und_cut,
					$item->meters,
					$item->covers,
					$item->bedsheet,
					$item->cons_cut
				);
				$itemsArr[$item->id]=$item->name;
			}
			$dato['table']= $this->table->generate();
		}
		$dato['select'] = form_dropdown('item', $itemsArr, '',"id=item class = form-control");
		if(!empty($detail)){				
			$dato['info']=$detail;
			$dato['tipo'] =1;
		}
		echo json_encode($dato);
	}
	public function save_corte(){
		permisos(array(1,2,3));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('idc', 'Orden de produccion', 'required|max_length[100]');
		$this->form_validation->set_rules('meters', 'Metros', 'required');
		$this->form_validation->set_rules('item', 'Items', 'required');
		$this->form_validation->set_rules('und_cut', 'Unidades cortadas', 'required|is_natural');
		$this->form_validation->set_rules('covers', 'covers cortadas', 'required|is_natural');
		$this->form_validation->set_rules('bedsheet', 'Sábanas cortadas', 'required|is_natural');
		$this->form_validation->set_rules('cons_cut', 'Consumo de corte', 'required');
			
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$cons_cut = str_replace(',','.',$this->input->post('cons_cut'));
			if($this->input->post('idc')>0){
				$dato = $this->model->updateCut(
					array(
						'order' => $this->input->post('idc'),
						'item' => $this->input->post('item'),
						'meters' => $this->input->post('meters'),
						'meters' => $this->input->post('meters'),
						'und_cut' => $this->input->post('und_cut'),
						'covers' => $this->input->post('covers'),
						'bedsheet' => $this->input->post('bedsheet'),
						'cons_cut' => $cons_cut,
						'created_cut' => date("Y-m-d H:i:s"),
						'created_cut_by' => $this->session->userdata('info')->id
					)
				);
				$this->model->update(
					array(
						'id' => $this->input->post('idc'),
						'state' => 3
					)
				);
				
				$op = $this->model->find($this->input->post('idc'));
				$this->model->insertH(
					array(
						'order' => $this->input->post('idc'),
						'state' => $op->state,
						'comment' => 'Actualizada Información de Corte',
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				$gs=$this->model->getSupplies($this->input->post('idc'));
				if($gs=TRUE){
					$itemSupplies = $this->model->get_detail($this->input->post('idc'));
					foreach($itemSupplies as $itemSupplie){
						$dsu=$this->model->insertSupplies(
							array(
								'op' => $this->input->post('idc'),
								'item' => $itemSupplie->item,
								'status' =>1,
								'placecreation' =>'Corte',
								'created' => date("Y-m-d H:i:s"),
								'created_by' => $this->session->userdata('info')->id
							)
						);
						$ixi=$this->model->getSuppliesItem($itemSupplie->item);
						foreach($ixi as $aux2){
							$this->model->insert_suppliesDetail(
								array(
									'supplyd' => $dsu['id'],
									'supply' => $aux2->supplies,
									'qty' => $aux2->cons,
									'created' => date("Y-m-d H:i:s"),
									'created_by' => $this->session->userdata('info')->id
								)
							);
						}
					}
				}
				$dato['msg']="Registro Actualizado!";
				$dato['tipo'] =1;
			}else{
				$dato['msg']=$dato["res"];
				$dato['tipo'] =0;
			}
			echo json_encode($dato);
		}
	}
	// Clasificación
	public function get_clasificacion(){
		permisos(array(1,3));
		$data['dato'] = $this->model->get_clasificacion($this->input->post("id"));
		$data['tipo']=1;
		echo json_encode($data);
	}
	public function save_clasificacion(){
		permisos(array(1,3));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('idcl', 'Orden de produccion', 'required|max_length[100]');
		$this->form_validation->set_rules('type_a', 'Tipo A', 'required|is_natural');
		$this->form_validation->set_rules('type_b', 'Tipo B', 'required|is_natural');
		$this->form_validation->set_rules('type_c', 'Tipo C', 'required|is_natural');
					
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			if ($this->input->post('audit')==$this->input->post('type_a')+$this->input->post('type_b')+$this->input->post('type_c')){
				$this->model->update_clasificacion(
					array(
						'id' => $this->input->post('idcl'),
						'audit' => $this->input->post('audit'),
						'auditA' => $this->input->post('type_a'),
						'auditB' => $this->input->post('type_b'),
						'auditC' => $this->input->post('type_c')
					)
				);
				$op = $this->model->find($this->input->post('idcl'));
				$this->model->insertH(
					array(
						'order' => $this->input->post('idcl'),
						'state' => $op->state,
						'comment' => 'Actualizada clasificación ABC',
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				$dato['msg']="Registro Actualizado!";
				$dato['tipo'] =1;
			}else{
				$dato['msg']="La suma de las unidades de A + B + C, No es igual a la cantidad auditada";
				$dato['tipo'] =0;
			}
			
			echo json_encode($dato);
		}
	}
	/*
	public function del_clasificacion(){		
		permisos(array(1)); 
		$dato=$this->model->del_clasificacion($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato["id"] = $this->input->post("id2");
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
	*/
	// Estado
	public function get_estado(){
		permisos(array(1,3));
		$this->load->library('table');
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
		$this->table->set_heading('Accion', 'Estado', 'Detalle', 'Fecha');
		$detail = $this->model->get_estado($this->input->post("id"));
		if(!empty($detail)){				
			foreach ($detail as $item){
				$links = '';
				// $links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.','.$this->input->post("id").'); return false;'));
				$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'del_estado('.$item->id.','.$this->input->post("id").'); return false;'));
				$this->table->add_row(
					$links,
					$item->state,
					$item->comment,
					$item->created
				);
			}
		}
		$data['table']= $this->table->generate();
		$data['tipo']=1;
		echo json_encode($data);
	}
	public function save_estado(){
		permisos(array(1,3)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('idEs', 'Orden de produccion', 'required|max_length[100]');
		$this->form_validation->set_rules('state', 'Estado', 'required');
					
		if ($this->form_validation->run() == FALSE) {
			$dato['msg']=validation_errors();
			$dato['tipo'] =0;
			echo json_encode($dato);
		}else{
			$this->model->update(
				array(
					'id' => $this->input->post('idEs'),
					'state' => $this->input->post('state'),
					'edited' => date("Y-m-d H:i:s"),
					'edited_by' => $this->session->userdata('info')->id
				)
			);
			$dato = $this->model->insert_estado(
				array(
					'order' => $this->input->post('idEs'),
					'state' => $this->input->post('state'),
					'comment' => 'Estado Actualizado',
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
			echo json_encode($dato);
		}
	}
	public function del_estado(){		
		permisos(array(1)); 
		$dato=$this->model->del_estado($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato["id"] = $this->input->post("id2");
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
	// Cambio Estado
	public function listo_form(){		
		permisos(array(1,3));
		if($this->input->post('id')>0){
				$dato = $this->model->update(
					array(
						'id' => $this->input->post('id'),
						'state' => 13,
						'date_closing' => date("Y-m-d H:i:s"),
						'edited_by' => $this->session->userdata('info')->id
					)
				);
	
			$dato['msg']="Registro Creado!";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		
		echo json_encode($dato);
	}
	public function tareas(){
		permisos(array(1,3));
		$this->load->library('table');
		$tmpl = array (
			'table_open'          => '<table class="table color-table warning-table">',
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
		$this->table->set_heading('Accion', 'Orden', 'Empleado','Item','Proceso');
		$detail = $this->model->get_tasks($this->input->post("id"),$this->input->post("tipo"),$this->input->post("busca"));
		if(!empty($detail)){				
			foreach ($detail as $item){
				$links = '';
				$links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'editTask('.$item->id.'); return false;','data-bs-toggle'=>'modal','data-bs-target'=>'#taskEditModal','data-whatever'=>'@getbootstrap'));
				$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'delTask('.$item->id.'); return false;'));
				
				$this->table->add_row(
					$links,
					$item->op,
					$item->cedula.' '.$item->name,
					$item->item.' '.$item->itemn,
					$item->process
				);
			}
		}
		$data['tipo']=1;
		$dato['tabla']= $this->table->generate();
		$dato['tareaid']=$this->input->post("id");
		$data['msg'] = $this->load->view('ajax/ordertask',$dato,TRUE);
		$procesos = $this->model->get_proccess_by_order($this->input->post("id"));
		$items = $this->model->get_items_by_order($this->input->post("id"));
		$data['procesos'] = form_dropdown('procesosT', $procesos, '',"id=procesosT class = form-control");
		$data['items'] = form_dropdown('itemsT', $items, '',"id=itemsT class = form-control");
		echo json_encode($data);
	}
	public function saveTaskCreate(){
		permisos(array(1,3));
		if($this->input->post('ordentareac')>0){
			$dato = $this->model->insertTask(
				array(
					'order' => $this->input->post('ordentareac'),
					'employee' => $this->input->post('employeessT'),
					'item' => $this->input->post('itemsT'),
					'proccess' => $this->input->post('procesosT'),
					'edited' => date("Y-m-d H:i:s"),
					'edited_by' => $this->session->userdata('info')->id
				)
			);
			$dato['msg']="Registro actualizado!";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
	public function saveTaskEdit(){
		permisos(array(1,3));
		if($this->input->post('ordentarea')>0){
			$dato = $this->model->updateTask(
				array(
					'id' => $this->input->post('ordentarea'),
					'edited' => date("Y-m-d H:i:s"),
					'edited_by' => $this->session->userdata('info')->id
				)
			);
			$dato['msg']="Registro actualizado!";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
	public function delTask(){		
		permisos(array(1)); 
		$dato=$this->model->delTask($this->input->post("id"));
		if ($dato["res"] == "ok"){
			$dato["id"] = $this->input->post("id2");
			$dato['msg']="El registro ha sido eliminado";
			$dato['tipo'] =1;
		}else{
			$dato['msg']=$dato["res"];
			$dato['tipo'] =0;
		}
		echo json_encode($dato);
	}
}