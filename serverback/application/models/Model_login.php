<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Login extends CI_Model {

	function __construct() {
		parent::__construct();
    }
	function get_session($id) {
		$this->session->sess_destroy();
		$this->session->userdata('session_id', $id);
		
        $this->db->where( 'id', $id );
		$session = $this->db->get( 'ci_sessions' );
		
		if($session->num_rows() > 0){			
			$data = $session->row()->data;
			
			// Turn our data into an array so we can parse through it
			$data_arr = explode( ';', $data );
			// Loop through each of our items to parse it out
			foreach( $data_arr as $session_key ) {
				// Explode out to separate our key name from our values
				$session_key_arr = explode( '|', $session_key );
				$key_index = $session_key_arr[0];
				if(strlen($key_index)>0){				
					// Explode out to parse our values
					$session_value_arr = explode( ':', $session_key_arr[1] );
					
					if(isset($session_value_arr[2])){
						$key_value = str_replace('"','',$session_value_arr[2]);
						// Build our new session index
						$this->session->set_userdata( $key_index, $key_value );
					}
				}
			}
			return true;
		}else{
			return false;
		}
    }	
	function get_login($user) {
        $this->db->where('user', $user);
		$this->db->where('status', 1);
		$this->db->limit(1);
        return $this->db->get('users');
    }
	function del_session($id) {
        $this->db->where('id', $id);
        $this->db->delete('ci_sessions');        
    }
	function get_loginct($user,$pass) {
		$this->db->select("a.noDocumento as Di, a.nombres as nombre, a.primerApellido as apellidos1, a.segundoApellido as apellidos2, a.email, b.estado, b.nivel, 
			b.unitxt, a.titulo, b.graduado, b.clave, b.lastpass, b.changepass");
		$this->db->join('clientes b', 'a.noDocumento=b.Di');
        $this->db->where('b.Di', $user);
        // $this->db->where('b.clave', sha1($pass));		
		$this->db->limit(1);
        return $this->db->get('profesionales a');
    }
	public function buscavalidaradmin($id){
		$this->db->select("id, email, concat(nombre,' ',apellido) as usr");
		$this->db->where('id', $id);
		$this->db->limit(1);
		$consulta = $this->db->get('users');
        if ($consulta->num_rows() == 1) {
			$data["estate"] = true;
			$data["email"] = $consulta->row()->email;
			$data["id"] = $consulta->row()->id;
			$data["usr"] = $consulta->row()->usr;
        }else{
			$data["estate"] = false;
		}
		return $data;
	}
	public function chkuseradmin($email){
		$this->db->select("id, concat(nombre,' ',apellido) as usr");
		$this->db->where('email', $email);
		$this->db->limit(1);
		$consulta = $this->db->get('users');
        if ($consulta->num_rows() == 1) {
			$data["estate"] = true;
			$data["usr"] = $consulta->row()->usr;
			$data["id"] = $consulta->row()->id;
        }else{
			$data["estate"] = false;
		}
		return $data;
	}
	public function recuperarct($user,$email){
		$this->db->select("Di, email, clave, concat(nombre,' ',apellido1,' ',apellido2) as usr");
		$this->db->where('Di', $user);
		$this->db->where('email', $email);
		$this->db->limit(1);
		$consulta = $this->db->get('clientes');
        if ($consulta->num_rows() == 1) {
			$data["estate"] = true;
			$data["usr"] = $consulta->row()->usr;
        }else{
			$data["estate"] = false;
		}
		return $data;
	}
	public function updatect($array, $user){
		$this->db->set($array);
		$this->db->where('Di', $user);
		$this->db->update("clientes");
	}
	public function validarct($user,$email){
		$this->db->select("Di, email, clave, concat(nombre,' ',apellido1,' ',apellido2) as usr");
		$this->db->where('Di', $user);
		$this->db->where('email', $email);
		$this->db->where('estado', 0);
		$this->db->limit(1);
		$consulta = $this->db->get('clientes');
        if ($consulta->num_rows() == 1) {
			$items=$consulta->row();
			$dato["tipo"]=1;
			$dato["msg"]=$items;
		}else{
			$dato["tipo"]=2;
			$dato["msg"]="Datos errados, o la cuenta ya esta validada";
		}
		return $dato;
	}
	public function buscavalidar($Di){
		$this->db->select("Di, email, clave, concat(nombre,' ',apellido1,' ',apellido2) as usr");
		$this->db->where('Di', $Di);		
		$this->db->limit(1);
		$consulta = $this->db->get('clientes');
        if ($consulta->num_rows() == 1) {
			$items=$consulta->row();
			$dato["tipo"]=1;
			$dato["msg"]=$items;
        }else{
			$dato["tipo"]=2;			
		}
		return $dato;
	}
	public function validar($Di){
		$this->db->set(array("estado"=>1));
		$this->db->where('Di', $Di);
		$this->db->update("clientes");
	}
	function validced($Di=0){
		$this->db->where('Di', $Di);		
		$this->db->limit(1);
		$consulta = $this->db->get('clientes');
        if ($consulta->num_rows() == 0) {
			$this->db->select("b.idUniversidad,b.nombres,b.primerApellido,b.segundoApellido,b.titulo");
			$this->db->where("b.noDocumento",$Di);
			$this->db->limit(1);
			$consulta = $this->db->get('profesionales b');
			if ($consulta->num_rows() == 1) {
				$items["universidad"]=$consulta->row()->idUniversidad;
				$items["nombre"]=$consulta->row()->nombres;
				$items["apellido1"]=$consulta->row()->primerApellido;
				$items["apellido2"]=$consulta->row()->segundoApellido;
				$items["titulo"]=$consulta->row()->titulo;				
				
				$dato["tipo"]=1;
				$dato["msg"]=$items;
			}else{
				$dato["tipo"]=3;
				$dato["msg"]="Cedula no encontrada, comunícate con nosotros al teléfono: 643 4135 - 226 6741 - 226 6722 o al eMail: registro@consejoprofesionalmvz.gov.co";
			}
		}else{
			$dato["tipo"]=2;				
			$dato["msg"]="Cedula ya registrada en el sistema, si haz olvidado tu contraseña puedes recuperarla ene link olvide mi contraseña";
		}
		return $dato;
	}	
	function crea_registro($registro) {
        $this->db->set($registro);
        $this->db->insert("clientes");
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	function savecaptcha($array){
		$this->db->where('ip', $array['ip']);
        $this->db->delete('captcha');
		
		$this->db->set($array);		
		$this->db->insert("captcha");
	}
	function get_captcha($ip){
		$this->db->select('captcha');
		$this->db->from('captcha');
		$this->db->where('ip', $ip);		
		$this->db->limit(1);
		$consulta = $this->db->get();
        if ($consulta->num_rows() == 1) {
			return $consulta->row()->captcha;
        }else{
			return 0;
		}
	}
	function ChkPassOld($ced) {
        $this->db->where('cliente', $ced);
        return $this->db->get("clientes_passh")->result();
    }
	function InsertPassOld($registro){
		$this->db->set($registro);
        $this->db->insert("clientes_passh");
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
	}
	public function updateAd($array, $user){
		$this->db->set($array);
		$this->db->where('id', $user);
		$this->db->update("usuarios");
	}
	function InsertPassOldAdmin($registro){
		$this->db->set($registro);
        $this->db->insert("usuarios_passh");
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
	}
}