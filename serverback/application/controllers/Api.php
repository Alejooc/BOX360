<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {
	var $titulo = "Api";
	var $modulo = "api";
	public function __construct()
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_api','model');
	}
	public function index(){
	}
	public function get_tjta(){
		$id=$_GET['e'];
		if (!empty($id)){
			$this->load->library("Cryptojs");
			$encrypted = base64_decode($id);
			$password = $this->config->item('encryption_key_js');
			$decrypted = $this->cryptojs::decrypt($encrypted, $password);
			$de=explode("&&",$decrypted);
			if(!empty($de[0])){
				$empleado=$this->model->findEmployee($de[0]);
				if(!empty($empleado)){
					if ($empleado->status){
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
							<img style='position:absolute; margin-top:135px;margin-left:223px;' src='$qr' height='60'>
						</div>";
						$this->tabla="<div id='cont' style='display: block;margin: 0 auto;width: 325px;'><span id='areaprint' style='display:block;'>";
						$this->tabla .= $tabla;
						$this->tabla.="</span></div>";
						$datos["tabla"]=$this->tabla;
						$datos["msg"]= "El presente carnet acredita al portador como empleado de la empresa Aristextil SAS con NIT 900.538.591-6.<br>
									En caso de extravío ó perdida, favor comunicarse al teléfono  602 695 95 03 ó entregarlo en la dirección <br>
									Calle 16 # 35b - 97 Acopi - Yumbo";
					}else{
						$datos["msg"]="Aristextil SAS con NIT 900.538.591-6, informa que el presente carnet, no se encuentra en vigencia, <br>
							para mayor información comunicarse al teléfono  602 695 95 03 ó acercarse a la oficina principal en la dirección <br>
									Calle 16 # 35b - 97 Acopi - Yumbo";
						$datos["tabla"]='';
					}
				}else{
					$datos["msg"]="Empleado invalido";
					$datos["tabla"]='';
				}
			}else{
				$datos["msg"]="Documento invalido";
				$datos["tabla"]='';
			}
		}else{
			$datos["msg"]="Código invalido";
			$datos["tabla"]='';
		}
		$this->load->view("ajax/api_empleados",$datos);
	}
}