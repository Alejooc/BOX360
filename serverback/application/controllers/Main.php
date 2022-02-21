<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
	var $tabla = "";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_main','model');		
		
		$headers = $this->input->request_headers();		
		$data["tipo"]=1;
		
		if(empty($headers["Token"])){
			$data["tipo"]=-1;
			$data["msg"]="Token invalido";
		}else{
			$data["msg"]= JWT::decode($headers["Token"], $this->config->item('encryption_key'), array('HS256'));
			
			if($data["msg"]=="Signature verification failed"){
				$data["tipo"]=-1;
				$data["msg"]="Token invalido";
			} 
			if($data["msg"]=="Expired token"){
				$data["tipo"]=-1;
				$data["msg"]="Token vencido";
			}
		}
		$info=validsession($data);
		$this->session->set_userdata("info",$info);
		
		$this->load->library('table');
		$tmpl = array(
                'table_open' => '<table class="table table-striped table-hover">',
                'heading_row_start' => '<tr>',
                'heading_row_end' => '</tr>',
                'heading_cell_start' => '<th>',
                'heading_cell_end' => '</th>',
                'row_start' => '<tr>',
                'row_end' => '</tr>',
                'cell_start' => '<td>',
                'cell_end' => '</td>',
                'row_alt_start' => '<tr>',
                'row_alt_end' => '</tr>',
                'cell_alt_start' => '<td>',
                'cell_alt_end' => '</td>',
                'table_close' => '</table>'
            );
         $this->table->set_template($tmpl);
	}
	public function index()
	{
		$dato['ops']=$this->model->GetOPS();
		$dato['machines']=$this->model->GetMachines();
		$dato['Top']=$this->model->GetTotal(1);
		$dato['Tit']=$this->model->GetTotal(2);
		$dato['Tem']=$this->model->GetTotal(3);
		$dato['Trp']=$this->model->GetTotal(4);
		// $dato['pdp']=$this->getPDP();
		$dato['pdp']='';
		$dato['aop']=$this->getAOP();
		$data['test']=0;
		$dato['msg'] = $this->load->view('ajax/main',$data,TRUE);		
		$dato['tipo'] =1;
        echo json_encode($dato);
	}
	public function getAOP($ex=0){
		$tabla="";
		$tablaD='';
		$tablaAux='';
		// Consulta OP estados 2, 3 y 4
		$ops = $this->model->GetOP();
		if(!empty($ops)){
			$tabla = "<table class='table color-table warning-table table-striped table-hover'>
				<thead>
					<tr>
						<th>Fecha</th>
						<th>OP No.</th>
						<th>Estado</th>
						<th>% Ava</th>
						<th>Item</th>
						<th>Rollos</th>
						<th>destino</th>
						<th>Metros</th>
						<th>U. cor</th>
						<th>Fun</th>
						<th>Sab</th>
						<th>U. Prod</th>
						<th>Diff</th>
						<th>C. Cor</th>
						<th>C. Pro</th>
						<th>Desp</th>
						<th>M Des</th>
						<th>Maquina</th>
					</tr>
				</thead>
			<tbody>";
			foreach ($ops as $item){
				// print_r($item);
				// echo "bbb<br><br>";
				if(!is_numeric($item->und_produced)){
					$item->und_produced=0;
				}
				if(!is_numeric($item->und_cut)){
					$item->und_cut=0;
				}
				if(!is_numeric($item->cons_cut)){
					$item->cons_cut=0;
				}
				if(!is_numeric($item->prom)){
					$item->prom=0;
				}
				$desp=$item->cons_cut-$item->prom;
				$diff=$item->und_cut-$item->und_produced;
				$idop=$item->id;
				$itemid=$item->itemid;
				if(is_numeric($item->PtjAdv)){
					$pmeta=round($item->PtjAdv,2);
					if($pmeta > 100){
						$pmeta='<span style="background:red;color:#fff;padding:5px;">'.round($pmeta,2).'%</span>';
					}else{
						$pmeta=round($pmeta,2).'%';
					}
				}else{
					$pmeta=0;
				}
				$tabla .= "
					<tr>
						<td>$item->created</td>
						<td>$item->op <button class='btn btn-warning' onclick='detalleop($idop,$itemid)'>Más</button></td>
						<td>$item->state</td>
						<td>$pmeta</td>
						<td>$item->item</td>
						<td>$item->rolls</td>
						<td>$item->destiny</td>
						<td>$item->meters</td>
						<td>$item->und_cut</td>
						<td>$item->covers</td>
						<td>$item->bedsheet</td>
						<td>$item->und_produced</td>
						<td>".$diff."</td>
						<td>$item->cons_cut</td>
						<td>$item->prom</td>
						<td>$desp</td>
						<td>".$desp*$item->und_cut."</td>
						<td>$item->machine</td>
					</tr>
				";
				// Consulto los procesos
				// $tablaD=updatePtjOP($idop,$itemid,0);
				// $tg=0;
				$tt="<span id='detalle$idop$itemid' class='opdetail' style='display:none;'></span>";
				// if(!empty($tablaD)){
					// $tg=$tablaD['taglobla'];
					// $tt=$tablaD['tabla'];
				// }
				$tablaAux.=$tt;
			}
			$tabla .= "</tbody></table>".$tablaAux;
		}
		if ($ex){
			echo $tabla;
		}else{
			return $tabla;
		}
	}
	public function detalleop(){
		$id=$this->input->post('id');
		$itemid=$this->input->post('itemid');
		$dato=updatePtjOP($id,$itemid,0);
		echo json_encode($dato);
	}
	public function getPDP($ex=0){
		// echo 0.5-0.4;
		$detail= $datos = $this->model->getPDP();
		if(!empty($detail)){
			$this->table->set_heading('No.','Fecha', 'OP','Item','Referencia','Tela',
			'Medida','Rollos','Maquina','Destinos',
			'U. Cort','U. Prod','Dif','C corte',
			'Prom','Desp','Mt Desp','Audi','A','B','C','Cierre'
			);
			$i=1;
			foreach ($detail as $item){
				// echo "$item->cons_cut $item->prom;";
				$Desp=0;
				if($item->cons_cut>0 and $item->prom>0){
					if (!is_numeric($item->cons_cut)){
						$item->cons_cut=0;
					}
					if (!is_numeric($item->prom)){
						$item->prom=0;
					}
					$Desp=$item->cons_cut-$item->prom;
					// echo "aaa $item->cons_cut-$item->prom;";
				}
				$pa="0%";
				if($item->auditA>0){
					$pa=round($item->auditA/$item->audit,2)*100;
					$pa.="%";
				}
				$pb="0%";
				if($item->auditB>0){
					$pb=round($item->auditB/$item->audit,2)*100;
					$pb.="%";
				}
				$pc="0%";
				if($item->auditC>0){
					$pc=round($item->auditC/$item->audit,2)*100;
					$pc.="%";
				}
				$this->table->add_row($i,$item->created,$item->op,$item->item,$item->ref,$item->cloth,
				$item->measure,$item->rolls,$item->maquina,$item->destino,
				$item->und_cut,$item->und_produced,$item->und_cut-$item->und_produced,$item->cons_cut,
				$item->prom,$Desp,$Desp*$item->und_cut,$item->audit,$item->auditA." ($pa)",$item->auditB." ($pb)",$item->auditC." ($pc)",$item->date_closing
				);
				$i++;
			}
			if ($ex){
				echo $this->table->generate();
			}else{
				return $this->table->generate();
			}
		}
	}
	public function exportar(){
		$id=$this->input->post('id');
		if($id==1){
			$this->getPDP(1);
		}
	}
	public function getMenu(){
		$rol=$this->session->userdata('info')->rol;
		$dato['tipo']=1;
		$dato['menu']='<ul id="sidebarnav">';
		$dato['menu'].='<li class="user-pro">
							<center>
								<span><a id="username5">'.$this->session->userdata('info')->name.'</a></span>
							</center>
                        </li>';
		if(in_array($rol,array(1,2,3,4,6))){
			$dato['menu'].='<li>
								<a class="waves-effect waves-dark" href="index.html" aria-expanded="false">
									<i class="fas fa-home text-warning"></i><span class="hide-menu">Inicio</span>
								</a>
							</li>';
		}
		if(in_array($rol,array(1,3,6))){
			$dato['menu'].='<li id="itemmenu3">
				<a onclick="expandir(3);" class="has-arrow  waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
				<i class="fas fa-users text-warning"></i><span class="hide-menu">Empleados</span></a>
				<ul aria-expanded="false" class="collapse">
					<li><a href="employee.html">Empleados </a></li>
					<li><a href="access.html">Control de accesso</a>
					<li><a href="accessR.html">Reporte de C.A.</a>
					<li><a href="extra.html">Horas Extras</a>
				</ul>
			</li>';
		}
		if(in_array($rol,array(1,3))){
			$dato['menu'].='<li id="itemmenu1">
				<a onclick="expandir(1);" class="has-arrow  waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
				<i class="fas fa-boxes text-warning"></i><span class="hide-menu">Productos</span></a>
				<ul aria-expanded="false" class="collapse">
					<li><a href="product.html">Producto </a></li>
					<li><a href="item.html">Items</a></li>
					<li><a href="supply.html">Insumos</a></li>
					<li><a href="supplyitem.html">Insumos por item</a></li>	
				</ul>
			</li>';
		}
		if(in_array($rol,array(1,2,3,4))){
			$dato['menu'].='<li id="itemmenu4">
							<a onclick="expandir(4)" class="has-arrow  waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
							<i class="fas fa-clipboard text-warning"></i><span class="hide-menu">Produccion</span></a>
							<ul aria-expanded="false" class="collapse">
								<li><a href="order.html">OP </a></li>
								<li><a href="report.html">Reportes</a></li>
								<li><a href="goal.html">Metas de díarias </a></li>
								<li><a href="supplydelivery.html">Entrega de insumos</a></li>
							</ul>
						</li>';
		}
		if(in_array($rol,array(1,3))){
			$dato['menu'].='<li>
							<a class="waves-effect waves-dark" href="data.html" aria-expanded="false">
								<i class="fas fa-chart-area text-warning"></i><span class="hide-menu">Informes</span>
							</a>
						</li>';
		}
		if(in_array($rol,array(1))){
			$dato['menu'].='<li id="itemmenu2">
							<a onclick="expandir(2)" class="has-arrow  waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false">
							<i class="fas fa-cogs text-warning"></i><span class="hide-menu">Configuración</span></a>
							<ul aria-expanded="false" class="collapse">
								<li><a href="user.html">Usuarios </a></li>
								<li><a href="area.html">Area </a></li>
								<li><a href="process.html">Procesos</a></li>
								<li><a href="machine.html">Maquinas</a></li>
								
							</ul>
						</li>';
		}
		if(in_array($rol,array(1,2,3,4,6))){
			$dato['menu'].='<li>
							<a class="waves-effect waves-dark" id="salir" onclick="salir()" href="#" aria-expanded="false">
								<i class="fas fa-power-off text-danger"></i><span class="hide-menu">Salir</span>
							</a>
						</li>';
		}
		$dato['menu'].='</ul>';
		echo json_encode($dato);
	}
	public function sendFile()
	{	
		$config['upload_path']          = './assets/emailimg/';
		$config['allowed_types']        = 'jpg|png|pdf';
		$config['max_size']             = 1024;	
		$config['encrypt_name']         = TRUE;
 
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('file')){			
			$dato['msg'] = $this->upload->display_errors();
			$dato['tipo'] = 0;
		}else{
			$data = $this->upload->data();
			$dato['tipo'] = 1;
			$dato['msg'] = $data["file_name"];			
		}
		echo json_encode($dato); 
	}
	
	public function output($output = null)
	{	
		$this->load->view('template.php', $output);
	}
}