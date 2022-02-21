<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {
	var $titulo = "Empleado";
	var $modulo = "employee";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_employee','model');		
		
		$data=ChkToken($this->input->request_headers());
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
	}
	public function index($segment=''){
        permisos(array(1,3,6));
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
				"a.name",
				"b.name",
				"type"
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
		$tipe_emple = array("NOMINA"=>"NOMINA","CONTRATISTA"=>"CONTRATISTA");
		if(!empty($results)){
			$total = $this->model->get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb);
			foreach($results as $r) {
				$data[] = array(
					$r->id,
					$r->name,
					$r->area,
					$r->type
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
		$data['procesos'] = array();
		if($this->input->post("id")>0){
			permisos(array(1,3,6));
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
			$this->table->set_heading('Accion', 'Proceso');
			if(!empty($detail)){				
				foreach ($detail as $item){
					$links = '';
					$links .= anchor('#' ,'<i class="fa fa-pencil-alt text-info m-r-10"></i>', array('onclick'=>'formu2('.$item->id.','.$this->input->post("id").'); return false;'));
					$links .= anchor('#' ,'<i class="fa fa-trash text-danger m-r-10"></i>', array('onclick'=>'elim2('.$item->id.','.$this->input->post("id").'); return false;'));
					
					$this->table->add_row(
						$links,
						$item->proceso
					);
				}
			}
			$data['table']= $this->table->generate();
			$data['procesos'] = $this->model->get_procesos($user['destiny']);
		}
		$data['tipe_emple'] =array("NOMINA"=>"NOMINA","CONTRATISTA"=>"CONTRATISTA");
		$data['tipe_docu'] = array("1"=>"CC","2"=>"TI","3"=>"PA");
		$data['genders'] = array("H"=>"Hombre","M"=>"Mujer");
		
		// $data['stores'] = $this->model->get_stores();
		
		$dato['msg'] = $this->load->view('ajax/'.$this->modulo,$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function form_send($type){
		permisos(array(1,3,6));
		/* PERSONALIZAR CAMPOS REQUERIDOS */		
		$this->form_validation->set_rules('name', 'Nombre', 'required');
		$this->form_validation->set_rules('destiny', 'Destino', 'required');
		$this->form_validation->set_rules('id', 'Numero de documento', 'required');
					
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
							'typeID' => $this->input->post('typeID'),
							'name' => $this->input->post('name'),
							'type' => $this->input->post('type'),
							'salary' => $this->input->post('salary'),
							'rh' => $this->input->post('rh'),
							'destiny' => $this->input->post('destiny'),
							'position' => $this->input->post('position'),
							'schedule' => $this->input->post('schedule'),
							'emergency' => $this->input->post('emergency'),
							'gender' => $this->input->post('gender'),
							'birth' => $this->input->post('birth'),
							'nationality' => $this->input->post('nationality'),
							'address' => $this->input->post('address'),
							'city' => $this->input->post('city'),
							'bank' => $this->input->post('bank'),
							'work_start' => $this->input->post('work_start'),
							'work_end' => $this->input->post('work_end'),
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
						'typeID' => $this->input->post('typeID'),
						'name' => $this->input->post('name'),
						'type' => $this->input->post('type'),
						'salary' => $this->input->post('salary'),
						'rh' => $this->input->post('rh'),
						'destiny' => $this->input->post('destiny'),
						'position' => $this->input->post('position'),
						'schedule' => $this->input->post('schedule'),
						'emergency' => $this->input->post('emergency'),
						'gender' => $this->input->post('gender'),
						'birth' => $this->input->post('birth'),
						'nationality' => $this->input->post('nationality'),
						'address' => $this->input->post('address'),
						'city' => $this->input->post('city'),
						'bank' => $this->input->post('bank'),
						'work_start' => $this->input->post('work_start'),
						'work_end' => $this->input->post('work_end'),
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
			permisos(array(1,3,6));
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
		permisos(array(1,3,6));
		/* PERSONALIZAR CAMPOS REQUERIDOS */
		$this->form_validation->set_rules('destinyd', 'destinyd', 'required');
							
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
							'destinyd' => $this->input->post('destinyd'),
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
				// $employee = $this->model->getEmployeeByid($this->input->post('idp'));
				$dato = $this->model->insert_detail(
					array(
						'employee' => $this->input->post('idp'),
						'destinyd' => $this->input->post('destinyd'),
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
	public function tjta(){
		permisos(array(1,6)); 
		$id=$this->input->post("id");
		$imp=$this->input->post("imp");
		$empleado=$this->model->find($id);
		if(!empty($empleado)){
			$do=$empleado->id;				
			$no=$empleado->name;
			$rh=$empleado->rh;
			$fo=$empleado->picture;
			
			if ( !file_exists('./assets/archivos/empleados/'.$do)){
				mkdir( './assets/archivos/empleados/'.$do, 0777 );
				$this->load->helper('file');
				write_file("./assets/archivos/empleados/$do/index.html", '');
			}
			if ( strlen($fo)>0 ){
				$urlfotofin="./assets/archivos/empleados/".$do."/$fo";
				if(file_exists($urlfotofin)){
					$urlfotofin=base_url("serverback/assets/archivos/empleados/".$do."/$fo");
				}else{
					$urlfotofin=base_url("assets/images/noimage.png");
				}
			}else{
				$urlfotofin=base_url("assets/images/noimage.png");
			}
			$this->load->library("Cryptojs");
			$password = $this->config->item('encryption_key_js');
			// encrypt
			$originalValue = "$do&&$no"; // this could be any value
			$encrypted = $this->cryptojs::encrypt($originalValue, $password);
			
			$contentqr="https://aristextil.com/empleados/?e=".base64_encode($encrypted);
			$this->load->library("Barcode");
			$this->barcode->output_image('png', 'qr', $contentqr, "qr_".$do, './assets/archivos/empleados/'.$do.'/', '');
			$qr=base_url("serverback/assets/archivos/empleados/".$do."/qr_$do.png");
			$tabla="
			<img style='z-index:10;position:absolute;width:324px;height:204px;' src='".base_url("assets/images/backgroundCard.png")."'>";
			$tabla.="<div style='border:0px solid #000; z-index:100;position:relative;font-size:12px;width:285px;height:195px;font-family: sans-serif,Arial;margin:5px 0 0 32px;resize:none;'>
				<img src='$urlfotofin' style='margin-left:-15px;position:absolute;margin-top:10px;height:195px;border:0px solid #000;'>
				<span style='width:193px;margin-top:60px;margin-left:90px;position:absolute;border:0px solid #000;text-align:center;'>
					<span style='font-size:12px;font-weight:bold;'>$no</span><br/>
					<span style='font-size:11px;'>DI: ".number_format($do,0,".",".")."</span><br/>
					<span style='font-size:11px;'>RH: ".$rh."</span>
				</span>
				<img style='position:absolute; margin-top:125px;margin-left:216px;' src='$qr' height='66'>
			</div>";
			if ($imp==2){
				$this->tabla = $tabla;
				$this->output->set_header('Content-Type: application/octet-stream');
				$this->load->library('pdfgenerator');
				$this->tabla.="<style>@page { margin: 0px; } body { margin: 0px;background:red; } html { margin: 0px}</style>";
				//"c8" => array(0, 0, 161.57, 229.61),
				$custom = array(0, 0, 153.08, 240.95);
				$var=$this->pdfgenerator->generate($this->tabla, "Carnet",false,$custom,"landscape");
				echo base64_encode($var); 
				die();
				$this->tabla.="<a href='#' onclick=imprimir('areaprint');return false;>Imprimir</a>";
			}else{
				$this->tabla="<span id='areaprint' style='display:block;'>";
				$this->tabla .= $tabla;
				$this->tabla.="</span>";
				$navegador = getenv("HTTP_USER_AGENT");
				if (preg_match("/MSIE/i", "$navegador") and $_GET["v"]==2){				
					$this->modelo->tabla=($this->modelo->tabla);
				}
				$this->tabla.="<center>";					
				$this->tabla.="<a href='#' style='position: relative;top: 30px;' onclick='tjta(".$this->input->post("id").",2)'>Versi&oacute;n de Impresi&oacute;n</a><br><br>";
				$this->tabla.="</center>";
				echo $this->tabla;
			}
			
		}else{
			echo "No existe empleado";
		}	
	}
	public function uploadpicture(){
		//print_r($_POST);
		$empleado=$this->model->find($this->input->post('cedfoto'));
		
		if( empty($empleado->picture) ){
			$this->subir(1,$empleado);
		}else{
			$this->subir(2,$empleado);
		}
	}
	public function subir($tipo=1,$empleado)
	{	
		$this->load->helper('file');
		$id = $this->input->post('cedfoto');
		$update=0;
		$arreglo["id"]=$id;
		$msg=array();
		$msg['tipo']=1;
		$msg['msg']="<br>";
		if ( !file_exists('./assets/archivos/empleados/'.$id)){
			mkdir( './assets/archivos/empleados/'.$id, 0777 );
			write_file("./assets/archivos/empleados/$id/index.html", '');
		}
		$config['upload_path']       = './assets/archivos/empleados/'.$id;
		$config['allowed_types']     = 'png|jpg';
		$config['max_size']          = 300;	
		$config['file_name']         = "$id.png";
		$config['overwrite']         = TRUE;
		$config['encrypt_name']      = FALSE;
		$config['max_width']         = 700;
        $config['max_height']        = 700;
	
		// print_r( getimagesize($_FILES["file1"]['tmp_name']) );
		
		$this->load->library('upload', $config);
		if($tipo==1){
			if ( ! $this->upload->do_upload('file1')){
				$msg['tipo']=0;
				$msg['msg'] .= 'Fotografía : '.$this->upload->display_errors().'<br>';
			}else{
				$update=1;
				$msg['msg'] .= 'Fotografía : Subida correctamente';
				$data = $this->upload->data();
				$arreglo["picture"]=$data["file_name"];
				if ($this->input->post('tipo')=='M'){
					$source = imagecreatefrompng('./assets/archivos/empleados/'.$id.'/'.$data["file_name"]);
					$rotate = imagerotate($source, 270, 0);
					imagepng($rotate, './assets/archivos/empleados/'.$id.'/'.$data["file_name"]);
				}					
			}
			// $this->upload->initialize($config);
		}else{
			if ( $_FILES["file1"]["name"]!="" ){
				if ( ! $this->upload->do_upload('file1')){
					$msg['tipo']=0;
					$msg['msg'] .= 'Fotografía : '.$this->upload->display_errors().'<br>';
				}else{
					$update=1;
					$msg['msg'] .= 'Fotografía : Subida correctamente<br>';
					$data = $this->upload->data();
					$arreglo["picture"]=$data["file_name"];	
					if ($this->input->post('tipo')=='M'){
						$source = imagecreatefrompng('./assets/archivos/empleados/'.$id.'/'.$data["file_name"]);
						$rotate = imagerotate($source, 270, 0);
						imagepng($rotate, './assets/archivos/empleados/'.$id.'/'.$data["file_name"]);
					}
					// $borrar = delete_file(getcwd()."/assets/archivos/empleados/$id/$empleado->picture");
					// if ($borrar["tipo"]==0){
						// $msg['msg'] .= 'Error al borrar archivo antiguo :'.$borrar["msg"];
					// }		
				}
			}
		}
		if($update){
			$this->model->update($arreglo); 	
		}
		echo json_encode($msg);
	}
}