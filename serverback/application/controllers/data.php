<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Data extends CI_Controller {
	var $titulo = "Informes";
	var $modulo = "data";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_data','model');		
		
		
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
	public function index(){
		permisos(array(1,3));
		$data['tipo']=1;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporte',$data,TRUE);
	 	
		echo json_encode($data);
	}
	public function reporte(){
		permisos(array(1,3));
		$id= $this->input->post('id');
		switch ($id){
			case 1: $this->reporte1();break;
			case 2: $this->reporte2();break;
			case 3: $this->reporte3();break;
			case 4: $this->reporte4();break;
			case 5: $this->reporte5();break;
			case 6: $this->reporte6();break;
			case 7: $this->reporte7();break;
			case 8: $this->reporte8();break;
			case 9: $this->reporte9();break;
		}
	}
	public function exportar(){
		permisos(array(1,3));
		$id= $this->input->post('id');
		switch ($id){
			case 1: $this->reporte1(1);break;
			case 2: $this->reporte2(2);break;
			case 3: $this->reporte3(3);break;
			case 4: $this->reporte4(4);break;
			case 5: $this->reporte5(5);break;
			case 6: $this->reporte6(6);break;
			case 7: $this->reporte7(7);break;
			case 8: $this->reporte8(8);break;
			case 9: $this->reporte9(9);break;
		}
	}
	public function reporte1($ex=0){
		permisos(array(1,3));
		$detail= $datos = $this->model->reporte1();
		if(!empty($detail)){
			$this->table->set_heading('No.','Cedula', 'Name','Contrato','Area','Proceso');
			$i=1;
			foreach ($detail as $item){
				$procesos = $this->model->procesosxempleado($item->id);
				if(!empty($procesos)){
					foreach ($procesos as $item2){
						$this->table->add_row($i,$item->id,$item->name,$item->type,$item->area1,$item2->name);
					}
				}else{
					$this->table->add_row($i,$item->id,$item->name,$item->type,$item->area1,'');
				}
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=1;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte2($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte2();
		if(!empty($reports)){
			$this->table->set_heading('No.','fecha creacion','Cantidad', 'op','cedula','nombre','tipo contrato','item','nombre item');
			$i=1;
			foreach ($reports as $item){
				$this->table->add_row($i,$item->created,$item->cant,$item->op,$item->employee,$item->name,$item->type,$item->item,$item->itemn);
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=2;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte3($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte3();
		if(!empty($reports)){
			$this->table->set_heading('No.','item ','nombre', 'proceso' );
			$i=1;
			foreach ($reports as $item){
				$this->table->add_row($i,$item->item,$item->name,$item->proceso);
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=3;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte4($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte4();
		$tipo = $this->input->post("tipo");
		// print_r($ex);
		// print_r($id);
		// die('aaaa');
		if(!empty($reports)){
			$this->table->set_heading('No.','Fecha ', 'Cedula','Nombre','Item','Cantidad','Op','Ok','Ok No' );
			$i=1;
			foreach ($reports as $item){
				if($tipo==1){
					if($item->ok>0 or $item->nook>0){
						$this->table->add_row($i,$item->created,$item->employee,$item->name,$item->item,$item->cant,$item->op,$item->ok,$item->nook);
						$i++;
					}
				}
				if($tipo==2){
					if($item->ok>0){
						$this->table->add_row($i,$item->created,$item->employee,$item->name,$item->item,$item->cant,$item->op,$item->ok,$item->nook);
						$i++;
					}
				}
				if($tipo==3){
					if($item->nook>0){
						$this->table->add_row($i,$item->created,$item->employee,$item->name,$item->item,$item->cant,$item->op,$item->ok,$item->nook);
						$i++;
					}
				}
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['tipo'] = $tipo;
		$dato['id']=4;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporte4',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte5($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte5();
		$maquina = array();
		foreach($reports['orders'] as $item){
			if(isset($maquina[$item->machine])){
				$maquina[$item->machine] = $maquina[$item->machine] .' - '. $item->op;
			}else{
				$maquina[$item->machine] = $item->op;
			}
		}
		if(!empty($reports)){
			$this->table->set_heading('No.','Maquinas ','OP');
			$i=1;
			foreach ($reports['machines'] as $item){
				$m='';
				if(isset($maquina[$item->id])){
					$m=$maquina[$item->id];
				}
				$this->table->add_row($i,$item->name,$m);
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=5;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte6($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte6();
		$state = array();
		foreach($reports['orders'] as $item){
			if(isset($state[$item->state])){
				$state[$item->state] = $state[$item->state] .' - '. $item->op;
			}else{
				$state[$item->state] = $item->op;
			}
		}
		if(!empty($reports)){
			$this->table->set_heading('No.','Estado ','OP');
			$i=1;
			foreach ($reports['states'] as $item){
				$d='';
				if(isset($state[$item->id])){
					$d=$state[$item->id];
				}
				$this->table->add_row($i,$item->name,$d);
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=6;		
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte7($ex=0){
		permisos(array(1,3));
		$goals=$this->model->reporte7_goal();
		$ProccessGoals=array();
		foreach($goals as $item){
			$f=$item->dategoal;
			$p=$item->proccess;
			$g=$item->goal;
			$ProccessGoals[$f][$p]=$g;
		}
		$reports = $this->model->reporte7();
		if(!empty($reports)){
			$this->table->set_heading('No.','Fecha','Cedula ','Nombre', 'Contrato', 'Proceso', 'Meta','Total','Val. Incen','Incen','T Incen');
			$i=1;
			foreach ($reports as $item){
				$incen = $item->total;
				if(isset($ProccessGoals[$item->fecha][$item->idp])){
					$goal=$ProccessGoals[$item->fecha][$item->idp];
				}else{
					$goal=0;
				}
				if ($item->type=='NOMINA'){
					$incen = $item->total - $goal;
				}
				if($incen<0){
					$incen=0;
				}
				$tincen = $incen * $item->incentive;
				$this->table->add_row($i,$item->fecha,$item->id,$item->name,$item->type,$item->proccess,$goal,$item->total,$item->incentive,$incen,"$".number_format($tincen,0,".",","));
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=7;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte8($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte8();
		if(!empty($reports)){
			$this->table->set_heading('No.','Empleado ID','Empleado','Destino', 'Cant','Val Hora','Total');
			$i=1;
			foreach ($reports as $item){
				$incen = $item->total;
				
				$tincen = $incen * $item->extra;
				$this->table->add_row($i,$item->id,$item->name,$item->destinyd,$item->total,"$".number_format($item->extra,0,".",","),"$".number_format($tincen,0,".",","));
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=8;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
	public function reporte9($ex=0){
		permisos(array(1,3));
		$reports = $this->model->reporte9();
		if(!empty($reports)){
			$this->table->set_heading('No.','Insumos','Item','Descripción','Cant');
			$i=1;
			foreach ($reports as $item){
				$this->table->add_row($i,$item->name,$item->item,$item->itemn,'');
				$i++;
			}
		}
		$dato['tabla'] = $this->table->generate();
		$dato['id']=9;
		$data['msg'] = $this->load->view('ajax/'.$this->modulo.'/reporteTPL1',$dato,TRUE);
		if($ex){
			echo $dato['tabla'];
		}else{
			echo json_encode($data);
		}
	}
}
	