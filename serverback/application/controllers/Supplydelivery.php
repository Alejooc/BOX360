<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplydelivery extends CI_Controller {
	var $titulo = "Entrega de Insumos";
	var $modulo = "supplydelivery";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_supplydelivery','model');		
		
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
				"a.op",
				"a.item",
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
					$r->opid,
					$r->item,
					$r->name,
					$r->deliverydate
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
		$data['items']=array();
		if($this->input->post("id")>0){
			permisos(array(1));
			$configuracion = $this->model->find($this->input->post("id"));
			foreach ($configuracion as $k => $campo) {
				$user[$k] = trim($campo);
			}
			$data['datos'] = $user;
			$data['items']=$this->model->getItemsOp($user["op"]);
			
			$detail = $this->model->get_detail($this->input->post("id"));
			$this->table->set_heading('Accion', 'Cod Insumo', 'Insumo', 'Cantidad');
			if(!empty($detail)){				
				foreach ($detail as $item){
					$links = '';
					$links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.','.$item->supplyd.'); return false;'));
					
					$this->table->add_row(
						$links,
						$item->cod,
						$item->supplyn,
						$item->qty
					);
				}
			}
			$data['table']= $this->table->generate();
		}
		$data['ops']=$this->model->get_ops();
		$data['statusL']=$this->model->get_status();
		
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function getItemsOp(){
		$op=0;
		if($this->input->post("op")){
			$op=$this->input->post("op");
		}
		
		$items = $this->model->getItemsOp($op);
		$data['items'] = form_dropdown('item', $items, '',"id=item class = form-control");
		$data['tipo']=1;
		echo json_encode($data);
	}
	public function form_send($type){
		permisos(array(1)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */	
		// print_r($_POST);
		// DIE;
		$this->form_validation->set_rules('op', 'OP', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('item', 'Item', 'required|is_natural_no_zero');
		$this->form_validation->set_rules('deliverydate', 'Fecha de entrega', 'required');
		
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
							'op' => $this->input->post('op'),
							'item' => $this->input->post('item'),
							'code' => $this->input->post('code'),
							'deliverydate' => $this->input->post('deliverydate'),
							'comment' => $this->input->post('comment'),
							'status' => $this->input->post('status'),
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
						'op' => $this->input->post('op'),
						'item' => $this->input->post('item'),
						'code' => $this->input->post('code'),
						'deliverydate' => $this->input->post('deliverydate'),
						'comment' => $this->input->post('comment'),
						'status' => $this->input->post('status'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$ixi=$this->model->getSuppliesItem($this->input->post('item'));
					foreach($ixi as $aux2){
						$this->model->insert_detail(
							array(
								'supplyd' => $dato['id'],
								'supply' => $aux2->supplies,
								'qty' => $aux2->cons,
								'created' => date("Y-m-d H:i:s"),
								'created_by' => $this->session->userdata('info')->id
							)
						);
					}
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
		if(empty($type)){
			$automodel = $this->model->get_supplies($var);
			$f="selectInsumo";			
			echo '<ul id="supplies-list">';
			if ($automodel){
				foreach($automodel as $item) {
					echo '<li onClick='.$f.'('.$item->id.',"'.urlencode($item->name).'");>'.$item->name.'</li>';
				}		
			}
			echo '</ul>';
		}
	}
	public function pdf(){
		permisos(array(1)); 
		$id=$this->input->post("id");
		$insumo=$this->model->consultpdf($id);
		$insumo2=$this->model->consultpdf2items($id);
		if(!empty($insumo)){
			$tabla="<table border='1' cellspacing='0' width='80%' style='margin-left:10%;margin-top:15px;'>
				  <tr>
					<td width='180px'><img width='160px' src='".base_url('serverback/assets/img/logo.png')."'></td>
					<td>ENTREGA DE INSUMOS Y CORTE</td>
					<td>
						<span style='color:red;font-weight:bold;'>No ".$insumo->id."</span><br/>
						<b>Ensamble</b> ".$insumo->code."
					</td>
				  </tr>
				  </table><br/>";
			$tabla.="<table border='1' cellspacing='0' width='80%' style='margin-left:10%;'>
				  <tr>
					<td><b>FECHA:</b></td>
					<td>".$insumo->deliverydate."</td>
				  </tr>
				  <tr>
					<td><b>SATELITE:</b></td>
					<td>".$insumo->satelite."</td>
				  </tr>
				  <tr>
					<td><b>ARTICULO DE FABRICA:</b></td>
					<td>".$insumo->item."    +    ".$insumo->name."</td>
				  </tr>
				  <tr>
					<td><b>CANTIDAD:</b></td>
					<td>".$insumo->und_produced."</td>
				  </tr>
				  </table><br/>";
			$tabla.="<table border='1' cellspacing='0' width='80%' style='margin-left:10%;'>
				  <tr>
					<th bgcolor='#e1e1e1' style='color:#000;'><center>Item</center></th>
					<th bgcolor='#e1e1e1' style='color:#000;'><center>Descripcion</center></th>
					<th bgcolor='#e1e1e1' style='color:#000;'><center>Cantidad</center></th>
					<th bgcolor='#e1e1e1' style='color:#000;'><center>Consumo</center></th>
				  </tr>";
			if (!empty($insumo2)){
				foreach($insumo2 as $item){
					$tabla.="<tr>
						<td><center>".$item->cod."</center></td>
						<td><center>".$item->name."</center></td>
						<td><center>".$item->qty."</center></td>
						<td><center>".round($item->qty/$insumo->und_produced,2)."</center></td>
					</tr>";	 
				} 
			}
			$tabla.="</table><br>";
			$tabla.="<table border='1' cellspacing='0' width='80%' style='margin-left:10%;'>
				<tr>
					<td><b>OBSERVACIONES</b></td>
				</tr>
				<tr>
					<td style='height:100px;'>$insumo->comment</td>
				</tr>
				";				
				$this->tabla = $tabla;
				$this->output->set_header('Content-Type: application/octet-stream');
				$this->load->library('pdfgenerator');
				$this->tabla.="<style>@page { margin: 0px; } body { margin: 5px; } html { margin: 0px}</style>";
				// "c8" => array(0, 0, 161.57, 229.61),
				// $custom = array(0, 0, 300.08, 500.95);
				$var=$this->pdfgenerator->generatepdf($this->tabla, "Pdfinsumo",false, 'A4',"portrait");
				echo base64_encode($var); 
				die();
		}else{
			echo "No existe la orden de entrega";
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
			$user['supplyn']=$this->model->getSupplyName($user['supply']);
			$dato['datos'] = $user;
		}
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send2($type){
		permisos(array(1,3)); 
		/* PERSONALIZAR CAMPOS REQUERIDOS */
		$this->form_validation->set_rules('deliverydid', 'deliverydid', 'required');
		$this->form_validation->set_rules('supply', 'Insumo ', 'required');
		$this->form_validation->set_rules('qty', 'Cantidad ', 'required');
							
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
							'supply' => $this->input->post('supply'),
							'qty' => $this->input->post('qty'),
							'edited' => date("Y-m-d H:i:s"),
							'edited_by' => $this->session->userdata('info')->id
						)
					);
					$dato['msg']="Registro Actualizado!";
					$dato['deliverydid'] = $this->input->post('deliverydid');
					$dato['tipo'] =1;
				} else {					
					$dato['msg']="El registro no existe";
					$dato['tipo'] =0;
				}
			} else {				
				$dato = $this->model->insert_detail(
					array(
						'supplyd' => $this->input->post('deliverydid'),
						'supply' => $this->input->post('supply'),
						'qty' => $this->input->post('qty'),
						'created' => date("Y-m-d H:i:s"),
						'created_by' => $this->session->userdata('info')->id
					)
				);
				if ($dato["res"] == "ok"){
					$dato['deliverydid'] = $this->input->post('deliverydid');
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
}