<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
	
	public function __construct() 
	{
		parent::__construct();		
		date_default_timezone_set('America/Bogota');
		$this->load->model('model_login','model');
	}	
	public function index($error='')
	{
	}
	public function access(){
		// echo password_hash('123456', PASSWORD_DEFAULT);
		$this->form_validation->set_rules('user', 'Usuario', 'required');
		$this->form_validation->set_rules('pass', 'Contraseña', 'required|max_length[20]');
				
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =0;
		}else {
			$user = $this->input->post('user');
			$pass = $this->input->post('pass');			
			$consulta = $this->model->get_login($user);
			//print_r($consulta);
			if($consulta->num_rows() > 0){
				//print_r($consulta);
				if (password_verify($pass, $consulta->row()->pass)) {
					$consulta->row()->isLoggedIn=TRUE;
					$consulta->row()->area="admin";
					$dato['msg']="Acceso correcto";				
					$dato['tipo'] =1;
					
					$data = [
						'jti' => base64_encode(random_bytes(32)),
						'iat'  => time(),
						'exp' => time() + 12000,
						'info' => $consulta->row()
					];
					$dato['info'] = JWT::encode($data, $this->config->item('encryption_key') );
				}else{
					$dato['msg']="Datos Incorrectos";
					$dato['tipo'] =0;
				}
			}else{
				$dato['msg']="Datos Incorrectos";
				$dato['tipo'] =0;
			}
		}
		echo json_encode($dato);
	}
	// OK
	public function get_menu($sess){
		$dato['tipo'] =-1;
		if($sess){
			if(!$this->model->get_session($this->uri->segment(3))){
				
			}else{
				$dato['msg']=$this->load->view('main_menu',"",TRUE);
				$dato['tipo'] =1;
			}
		}			
		echo json_encode($dato);		
	}
	// OK
	public function salir(){
		$data=ChkToken($this->input->request_headers());
		//$info=validsession2($data);
		$this->session->set_userdata("info",$data["msg"]->info);    
		
		if ($this->session->userdata("info")->area=="client"){
			save_log_cte("Login","Salir");
		}		
		$dato['msg']="Hasta pronto";
		$dato['tipo'] =1;
		echo json_encode($dato);
	}
	// OK
	public function accessct(){
		// $this->input->post(NULL, TRUE);
		$this->form_validation->set_rules('usuario', 'Usuario', 'required|max_length[50]|alpha_numeric');
		$this->form_validation->set_rules('pass', 'Contraseña', 'required|max_length[20]');
		$this->form_validation->set_rules('captcha', 'Captcha', 'required|min_length[6]|max_length[6]|alpha_numeric');
				
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =0;
		}else {
			$captcha = $this->model->get_captcha($_SERVER['REMOTE_ADDR']);
			if ($captcha === 0){
				$dato['msg'] = "Captcha expirado por favor genere uno nuevo";
				$dato['tipo'] = 0;
			} else {
				if ($captcha == $this->input->post("captcha")){
					$usuario = $this->input->post('usuario');
					$pass = $this->input->post('pass');			
					$consulta = $this->model->get_loginct($usuario,$pass);
					if($consulta->num_rows() > 0){
						if($consulta->row()->estado!=1){
							$dato['msg']="Cuenta pendiente por validación";
							$dato['tipo'] =0;
						}else{						
							$then = new DateTime($consulta->row()->lastpass);
							$now = new DateTime();
							$sinceThen = $then->diff($now);
							if ($consulta->row()->changepass == 1 or $sinceThen->days >90){
								$dato['msg']="Su contraseña ha vencido, es necesario que reinicie su contraseña en la seccion Olvido su contraseña";
								$dato['tipo'] =0;
							} else {
								//echo $pass."<br>";
								//echo  $consulta->row()->clave;
								if (password_verify($pass, $consulta->row()->clave)) {
									$consulta->row()->isLoggedIn=TRUE;
									$consulta->row()->area="client";
									$consulta->row()->clave="";
									$this->session->set_userdata("info",$consulta->row());
									$dato['msg']="Acceso correcto";				
									$dato['tipo'] =1;
									save_log_cte("Login","Ingresar");
									$data = [
										'jti' => base64_encode(random_bytes(32)),
										'iat'  => time(),
										'exp' => time() + 3000,
										'info' => $consulta->row()
									];
									$dato['info'] = JWT::encode($data, $this->config->item('encryption_key') );
								} else {
									$dato['msg']="Datos de acceso invalidos";
									$dato['tipo'] =0;
								}
							}
						}
					}else{
						$dato['msg']="Datos Incorrectos";
						$dato['tipo'] =0;
					}
				}else{
					$dato['tipo'] = 0;
					$dato['msg'] = "Captcha invalido";
				}
			}
		}
		echo json_encode($dato);
	}
	//OK
	public function recuperarct(){
		// $this->input->post(NULL, TRUE);S
		$this->form_validation->set_rules('docid', 'Documento', 'required|max_length[50]|alpha_numeric');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('captcha', 'Captcha', 'required|min_length[6]|max_length[6]|alpha_numeric');
		
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =2;
		}else {
			$captcha = $this->model->get_captcha($_SERVER['REMOTE_ADDR']);
			if ($captcha === 0){
				$dato['msg'] = "Captcha expirado por favor genere uno nuevo";
				$dato['tipo'] = 2;
			} else {
				if ($captcha == $this->input->post("captcha")){
					$docid = $this->input->post('docid');
					$email = $this->input->post('email');
					$data = $this->model->recuperarct($docid,$email);
					if ($data['estate']){
						$npass = generate_pass();
						$clave = password_hash($npass, PASSWORD_DEFAULT);
						$this->model->updatect(
							array(
								"clave" => $clave,
								'changepass' => 0,
								'lastpass' => date("Y-m-d H:i:s")
							), $docid
						);
						$this->model->InsertPassOld(
							array(
								'cliente' => $docid,
								'pass' => $clave,
								'fecha' => date("Y-m-d H:i:s")
							), $docid
						);
						$mensaje=get_cms(9);
						$mensaje=str_replace("{pass}",$npass,$mensaje);					
						$mensaje=str_replace("{profesional}",$data["usr"],$mensaje);						
						enviar_email($email,"Recordatorío de contraseña Comvezcol",$mensaje);
						
						$dato['tipo'] = 1;
						$dato['msg'] = "Su nueva contraseña se ha enviado a su correo";
					}else{
						$dato['tipo'] = 2;
						$dato['msg'] = "Datos errados";
					}
				} else {
					$dato['tipo'] = 2;
					$dato['msg'] = "Captcha invalido";
				}
			}
		}
		echo json_encode($dato);
	}
	//OK
	public function recuperarcta(){
		// $this->input->post(NULL, TRUE);S
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('captcha', 'Captcha', 'required|min_length[6]|max_length[6]|alpha_numeric');
		
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =2;
		}else {
			$captcha = $this->model->get_captcha($_SERVER['REMOTE_ADDR']);
			if ($captcha === 0){
				$dato['msg'] = "Captcha expirado por favor genere uno nuevo";
				$dato['tipo'] = 2;
			} else {
				if ($captcha == $this->input->post("captcha")){
					$email = $this->input->post('email');
					$data = $this->model->chkuseradmin($email);
					if ($data['estate']){
						$mensaje=get_cms(9);
						$url=site_url("login/ValidAdmin/".$data["id"]."/".sha1($data["id"].$email));
						$mensaje=str_replace("{pass}",$url,$mensaje);
						$mensaje=str_replace("{profesional}",$data["usr"],$mensaje);
						enviar_email($email,"Recuperar contraseña Comvezcol",$mensaje);
						
						$dato['tipo'] = 1;
						$dato['msg'] = "Su nueva contraseña se ha enviado a su correo";
					}else{
						$dato['tipo'] = 2;
						$dato['msg'] = "Datos errados";
					}
				} else {
					$dato['tipo'] = 2;
					$dato['msg'] = "Captcha invalido";
				}
			}
		}
		echo json_encode($dato);
	}
	public function ValidAdmin($id,$encry){
		if(!empty($id)){
			$item=$this->model->buscavalidaradmin($id);
			if($item["estate"]){
				if (sha1($id.$item["email"]) == $encry){
					$npass = generate_pass();
					$clave = password_hash($npass, PASSWORD_DEFAULT);
					$this->model->updateAd(
						array(
							"clave" => $clave,
							'changepass' => 0,
							'lastpass' => date("Y-m-d H:i:s")
						), $id
					);
					$this->model->InsertPassOldAdmin(
						array(
							'user' => $id,
							'pass' => $clave,
							'fecha' => date("Y-m-d H:i:s")
						), $id
					);
					$mensaje=get_cms(9);
					$mensaje=str_replace("{pass}",$npass,$mensaje);
					$mensaje=str_replace("{profesional}",$item["usr"],$mensaje);
					enviar_email($item["email"],"Recuperar contraseña Comvezcol",$mensaje);
					redirect("http://nuevo.consejoapp.com.co/msg1.html");
					die();
				}else{
					redirect("http://nuevo.consejoapp.com.co/msg2.html");
					die();
				}
			} else {
				redirect("http://nuevo.consejoapp.com.co/msg2.html");
				die();
			}
		}else{
			redirect("http://nuevo.consejoapp.com.co/msg2.html");
			die();
		}
	}
	// OK
	public function validarct(){
		$this->form_validation->set_rules('docid', 'Documento', 'required|max_length[20]|alpha_numeric');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('captcha', 'Captcha', 'required|min_length[6]|max_length[6]|alpha_numeric');
		
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =2;
		}else {
			$captcha = $this->model->get_captcha($_SERVER['REMOTE_ADDR']);
			if ($captcha === 0){
				$dato['msg'] = "Captcha expirado por favor genere uno nuevo";
				$dato['tipo'] = 2;
			} else {
				if ($captcha == $this->input->post("captcha")){
					$docid = $this->input->post('docid');
					$email = $this->input->post('email');			
					$dato = $this->model->validarct($docid,$email);
					if($dato["tipo"]==1){			
						$mensaje=get_cms(8);
						$url=site_url("login/validar/$docid/".sha1($dato["msg"]->Di.$dato["msg"]->email));
						$mensaje=str_replace("{URL}",$url,$mensaje);
						$mensaje=str_replace("{profesional}",$dato["msg"]->usr,$mensaje);
						enviar_email($dato["msg"]->email,"Correo de validación de cuenta Comvezcol",$mensaje);
						$dato["msg"]="Email enviado con los datos para validar su cuenta";
					}
				} else {
					$dato['tipo'] = 2;
					$dato['msg'] = "Captcha invalido";
				}
			}
		}		
		echo json_encode($dato);
	}
	// OK
	public function validar($docid,$encry){
		if(!empty($docid)){
			$item=$this->model->buscavalidar($docid);			
			if($item["tipo"]==1){
				if (sha1($item["msg"]->Di.$item["msg"]->email) == $encry){
					$this->model->validar($item["msg"]->Di);					
					redirect("http://nuevo.consejoapp.com.co/msg1.html");
					die();
				}else{ 
					redirect("http://nuevo.consejoapp.com.co/msg2.html");
					die();
				}
			}else{
				redirect("http://nuevo.consejoapp.com.co/msg2.html");
				die();
			}
		}else{
			redirect("http://nuevo.consejoapp.com.co/msg2.html");
			die();
		}
	}
	// OK
	public function captcha(){
		header("Content-type: image/gif");
		$captchanumber = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; // Initializing PHP variable with string
		$captchanumber = substr(str_shuffle($captchanumber), 0, 6); // Getting first 6 word after shuffle.
		$this->model->savecaptcha(array(
			'ip' => $_SERVER['REMOTE_ADDR'],
			'captcha' => $captchanumber,
			'fecha' => date("Y-m-d H:i:s"),
		));
		$image = imagecreatefromgif("bgcaptcha.gif"); // Generating CAPTCHA
		$foreground = imagecolorallocate($image, 13, 120, 0); // Font Color
		imagestring($image, 5, 16, 7, $captchanumber, $foreground);
		imagegif($image);
		imagedestroy($image);
	}
	// OK
	public function validced(){
		$this->input->post(NULL, TRUE);
		$this->form_validation->set_rules('captcha', 'Captcha', 'required|min_length[6]|max_length[6]|alpha_numeric');
		$this->form_validation->set_rules('nodoc', 'Documento', 'required|max_length[20]|alpha_numeric');
		
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =3;
		}else {
			$captcha = $this->model->get_captcha($_SERVER['REMOTE_ADDR']);
			
			if ($captcha === 0){
				$dato['msg'] = "Captcha expirado por favor genere uno nuevo";
				$dato['tipo'] = 3;
			} else {
				if ($captcha == $this->input->post("captcha")){
					$dato=$this->model->validced($this->input->post("nodoc"));
				} else {
					$dato['tipo'] = 3;
					$dato['msg'] = "Captcha invalido";
				}
			}
		}
		echo json_encode($dato);
	}
	// OK
	function valid_pass($str)
	{
		$field_value = $str; //this is redundant, but it's to show you how
		//the content of the fields gets automatically passed to the method
		$dato = pass_strong($field_value);
		if($dato["tipo"] == 1)
		{
			return TRUE;
		} else {
			$this->form_validation->set_message('valid_pass', $dato["msg"]);
			return FALSE;
		}
	}
	// OK
	public function registro(){
		$this->input->post(NULL, TRUE);
		$this->form_validation->set_rules('nodoc', 'Cedula', 'required|max_length[20]|alpha_numeric');
		$this->form_validation->set_rules('titulo', 'Titulo', 'required');
		$this->form_validation->set_rules('universidad', 'Universidad', 'required');
		$this->form_validation->set_rules('pass', 'Contraseña', 'required|max_length[20]|min_length[8]|callback_valid_pass');
		$this->form_validation->set_rules('cpass', 'Confirmación de contraseña', 'required|matches[pass]');
		$this->form_validation->set_rules('nombre', 'Nombre', 'required|max_length[100]');
		$this->form_validation->set_rules('apellido1', 'Primer apellido', 'required|max_length[100]');
		$this->form_validation->set_rules('apellido2', 'Segundo apellido', 'required|max_length[100]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[clientes.email]');
		$this->form_validation->set_rules('cemail', 'Confirmación de email', 'required|valid_email|matches[email]');
		$this->form_validation->set_rules('captcha', 'Captcha', 'required|min_length[6]|max_length[6]|alpha_numeric');
				
		if($this->form_validation->run() == FALSE) {
			$dato['msg'] =validation_errors();
			$dato['tipo'] =0;
		}else {
			$captcha = $this->model->get_captcha($_SERVER['REMOTE_ADDR']);
			
			if ($captcha === 0){
				$dato['msg'] = "Captcha expirado por favor genere uno nuevo";
				$dato['tipo'] = 0;
			} else {
				if ($captcha == $this->input->post("captcha")){
					$chkpass = pass_strong($this->input->post('pass'));
					if ($chkpass["tipo"] == 1){
						$ChkPassOld = $this->model->ChkPassOld($this->input->post('nodoc'));
						$pasa = 1;
						foreach ($ChkPassOld as $hash) {
							if (password_verify($this->input->post('pass'), $hash->pass)) {
								$pasa = 0;
								break;
							}
						}
						if($pasa == 1){
							$clave = password_hash($this->input->post('nodoc'), PASSWORD_DEFAULT);
							$dato = $this->model->crea_registro(
								array(
									'fechareg'=> date("Y-m-d H:i:s"),
									'Di' => $this->input->post('nodoc'),
									'titulo' => $this->input->post('titulo'),
									'unitxt' => $this->input->post('universidad'),
									'clave' => $clave,
									'nombre' => $this->input->post('nombre'),
									'apellido1' => $this->input->post('apellido1'),
									'apellido2' => $this->input->post('apellido2'),
									'email' => $this->input->post('email'),
									'estado' => 0,
									'nivel' => 1,
									'lastpass' => date("Y-m-d H:i:s"),
									'changepass' => 0
								)
							);
							if ($dato["res"] == "ok"){
								$this->model->InsertPassOld(array(
									'cliente' => $this->input->post('nodoc'),								
									'pass' => $clave,
									'fecha' => date("Y-m-d H:i:s")
								));
								$mensaje=get_cms(8);
								$url=site_url("login/validar/".$this->input->post('nodoc')."/".sha1($this->input->post('nodoc').$this->input->post('email')));
								$mensaje=str_replace("{URL}",$url,$mensaje);						
								$mensaje=str_replace("{profesional}",utf8_decode($this->input->post('nombre'))." ".utf8_decode($this->input->post('apellido1'))." ".utf8_decode($this->input->post('apellido2')),$mensaje);				
								
								enviar_email($this->input->post('email'),"Registro Comvezcol",$mensaje);
												
								$dato['msg']="Registro Creado!, Se ha enviado un correo para validar tu cuenta";
								$dato['tipo'] =1;
							}else{
								$dato['msg']="El usuario ya existe o No se ha agregado el usuario";
								$dato['tipo'] =0;
							}
						}else{
							$dato['msg']='La contraseña ya ha sido utilizada, por favor utilice otra.';
							$dato['tipo'] =0;
						}
					} else {
						$dato['msg'] = $chkpass["msg"];
						$dato['tipo'] = 0;
					}
				} else {
					$dato['tipo'] = 0;
					$dato['msg'] = "Captcha invalido";
				}
			}
		}
		echo json_encode($dato);
	}
}