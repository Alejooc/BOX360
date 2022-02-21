<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->form_validation->set_message('required', 'Debe ingresar un valor para %s');
		$this->form_validation->set_message('valid_email', 'Debe ingresar un %s');
		date_default_timezone_set('America/Bogota');
	}	
	
	public function index($error='')
	{
		$data['titulo']= 'Acceso al Sistema';
		$data['error'] = $error;
		$this->load->view('login',$data);
	}	
	public function access(){
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('pass', 'Password', 'required');
		if($this->form_validation->run() == FALSE) {
			$this->index();
		}else {
			$email = $this->input->post('email');
			$pass = $this->input->post('pass');
			$this->load->model('model_login');
			$consulta = $this->model_login->get_login($email,$pass);
			if($consulta->num_rows() > 0){
				$this->set_sesion_admin($consulta->row());
				redirect('main/index');
			}else{
				$this->index('Datos Incorrectos');
			}
		}
	}	
	private function set_sesion_admin($datos){
		$this->session->set_userdata(
			array(
				'id'=>$datos->id,
				'nombres'=>$datos->nombres,
				'apellidos'=>$datos->apellidos,
				'email'=>$datos->email,
				'nivel'=>$datos->nivel,				
				'isLoggedIn'=>TRUE
			)
		);		
	}	
	public function salir(){
		$this->session->sess_destroy();
		redirect(site_url());
	}
}