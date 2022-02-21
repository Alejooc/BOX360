<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_employee extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='employees';
	var $tablaA='destinys';
	var $tablaP='destinys_d';
	var $tablaED='employees_by_proccess';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select('a.id, a.name, b.name as area,a.type');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaA.' b','a.destiny=b.id');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.id' , $cb);
			$this->db->or_like( 'a.name' , $cb);
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'a.type' , $cb);
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
		$this->db->select('a.id, a.name, b.name as area,a.type');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaA.' b','a.destiny=b.id');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.typeID' , $cb);
			$this->db->or_like( 'a.name' , $cb);
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'a.type' , $cb);
		}
        // $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->num_rows();	
		}
	}

	function find($id) {
		$this->db->select("a.*, b.name as destinyn");
        $this->db->limit(1);
        $this->db->where('a.id', $id);
		$this->db->join($this->tablaA.' b','a.destiny=b.id');
        return $this->db->get($this->tabla.' a')->row();
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
	function get_destiny($var){
		$this->db->select('id, name');
		$this->db->from('destinys');
		$this->db->where('type', 'Interna');
		$this->db->like('name', $var);
		$consulta = $this->db->get();
          return $consulta->result();
	}
	function get_detail($id){
		$this->db->select('a.id, b.name as proceso');
        $this->db->from($this->tablaED.' a');
		$this->db->where('a.employee',$id);
		$this->db->join($this->tablaP.' b','a.destinyd=b.id');
		$this->db->order_by('a.id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function find2($id) {
		// $this->db->select('employeeidb.id as ebpid, a.destinyd');
        $this->db->limit(1);
        $this->db->where('id', $id);
        return $this->db->get($this->tablaED)->row();
    }
	function insert_detail($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaED);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	 function update_detail($registro) {
        $this->db->set($registro);
        $this->db->where('id', $registro['id']);
        $this->db->update($this->tablaED);
    }
	function delete2($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tablaED);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	function get_ppal($id){
		$this->db->select('a.id');
        $this->db->from($this->tabla.' a');
		$this->db->join($this->tablaED.' b','a.id=b.employee');
		$this->db->where('b.id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->id;
        }
	}
	function getEmployeeByid($id){
		$this->db->select('NumberID');
        $this->db->from($this->tabla);
		$this->db->where('id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->NumberID;
        }
	}
	function get_procesos($id){
		if($id>0){
			$this->db->select('id,name');
			$this->db->from('destinys_d');
			$this->db->where('destiny',$id);
			$this->db->order_by('id');
			$consulta = $this->db->get();
			if ($consulta->num_rows() > 0) {
				foreach ($consulta->result_array() as $fila) {				
					$data[ $fila["id"] ] = $fila["name"];
				}
				return $data;
			}
		}
	}
}