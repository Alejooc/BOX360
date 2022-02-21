<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_supplyitem extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='supplies_item';
	var $tablaE='supplies';
	var $tablaD='items';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		$this->db->select('a.id, a.item, b.name as insumo, c.name as iteem, a.created, a.cons');
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaE.' b','a.supplies=b.id');
		$this->db->join($this->tablaD.' c','a.item=c.id');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'c.name' , $cb);
		}
        $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb){
		$this->db->select('a.id, b.name as insumo, c.name as iteem, a.created, a.cons');
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaE.' b','a.supplies=b.id');
		$this->db->join($this->tablaD.' c','a.item=c.id');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'c.name' , $cb);
		}
        // $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->num_rows();	
		}
	}
	public function get_total($search,$columns_valid)
	 {
		$this->db->select("COUNT(*) as num");
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		// $this->db->limit(1);
		$query = $this->db->get($this->tabla);
		$result = $query->row();
		if(isset($result)) return $result->num;
		return 0;
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
	function get_suppliesss($var){
		$this->db->select('id, name');
		$this->db->from('supplies');
		$this->db->like('name', $var);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function get_item($var){
		$this->db->select('id, item');
		$this->db->from('items');
		$this->db->like('item', $var);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function get_supplies($ced){
		$this->db->select('name');
		$this->db->from('supplies');
		$this->db->like('id', $ced);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->name;
        }
	}
	function get_itemm($ced){
		$this->db->select('item');
		$this->db->from('items');
		$this->db->like('id', $ced);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->item;
        }
	}
	function ChkPassOldAdmin($ced) {
		$this->db->select("pass");
        $this->db->where('user', $ced);
        return $this->db->get("usuarios_passh")->result();
    }
	function get_stores(){		
		$this->db->select('id,name');
        $this->db->from('stores');
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