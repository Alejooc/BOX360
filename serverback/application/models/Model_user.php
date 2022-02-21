<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_user extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='users';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->from($this->tabla);
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'name' , $cb);
			$this->db->or_like( 'email' , $cb);
			$this->db->or_like( 'phone' , $cb);
		}
        $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->from($this->tabla);
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'name' , $cb);
			$this->db->or_like( 'email' , $cb);
			$this->db->or_like( 'phone' , $cb);
		}
        // $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->num_rows();
		}
	}
	function find($id) {
        $this->db->limit(1);
        $this->db->where('id', $id);
        return $this->db->get($this->tabla)->row();
    }
	function insert($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tabla);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
    function update($registro) {
        $this->db->set($registro);
        $this->db->where('id', $registro['id']);
        $this->db->update($this->tabla);
    }
    function delete($id) {
        $this->db->where('Id', $id);
        $this->db->delete($this->tabla);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
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
	function ChkPassOldAdmin($ced) {
		$this->db->select("pass");
        $this->db->where('user', $ced);
        return $this->db->get("usuarios_passh")->result();
    }
	function get_roles(){
		$this->db->select('id,name');
        $this->db->from('userrols');
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
	function get_areas(){		
		$this->db->select('id,name');
        $this->db->from('destinys');
		$this->db->where('type','Interna');
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			$data[0] = "Ninguna";
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
}