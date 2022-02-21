<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_item extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='items';
	var $tabla1='item_by_proccess';
	var $tabladd='destinys_d';
	var $tablad='destinys';
	
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select('a.id, a.item, a.name, a.state, c.name as producto, b.name as detalle');
		$this->db->from($this->tabla.' a');
		$this->db->join('productsd b','b.id = a.productd');
		$this->db->join('products c','c.id = b.product');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.id' , $cb);
			$this->db->or_like( 'a.item' , $cb);
			$this->db->or_like( 'a.name' , $cb);
			$this->db->or_like( 'c.name' , $cb);
			$this->db->or_like( 'b.name' , $cb);
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
		$this->db->select('a.id, a.item, a.name, a.state, c.name as producto, b.name as detalle');
		$this->db->from($this->tabla.' a');
		$this->db->join('productsd b','b.id = a.productd');
		$this->db->join('products c','c.id = b.product');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.id' , $cb);
			$this->db->or_like( 'a.item' , $cb);
			$this->db->or_like( 'a.name' , $cb);
			$this->db->or_like( 'c.name' , $cb);
			$this->db->or_like( 'b.name' , $cb);
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
	function get_productos(){
		$this->db->select("b.id, a.name as producto, b.name as detalle");
		$this->db->from('productsd b');
		$this->db->join('products a','a.id = b.product');
		$this->db->order_by('a.id');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			foreach ($consulta->result_array() as $fila) {				
				$data[ $fila["id"] ] = $fila["producto"].' '.$fila["detalle"];
			}
			return $data;
		}
	}
	function get_destiny($var){
		$this->db->select('id, name');
		$this->db->from('destinys');
		$this->db->like('name', $var);
		$consulta = $this->db->get();
          return $consulta->result();
	}
	function get_detail($id){
		$this->db->select('a.id, a.order, a.measure, b.name as dd, a.item, c.name as d, a.goal');
        $this->db->from($this->tabla1.' a');
		$this->db->join($this->tabladd.' b','a.destinyd	=b.id');
		$this->db->join($this->tablad.' c','c.id	=b.destiny');
		$this->db->where('a.item',$id);
		$this->db->order_by('a.order');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function find2($id) {
		// $this->db->select('employeeidb.id as ebpid, a.destinyd');
        $this->db->limit(1);
        $this->db->where('id', $id);
        return $this->db->get($this->tabla1)->row();
    }
	function insert_detail($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tabla1);
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
        $this->db->update($this->tabla1);
    }
	function update_disbleEnd($registro) {
        $this->db->set($registro);
        $this->db->where('item', $registro['item']);
        $this->db->update($this->tabla1);
    }
	function delete2($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tabla1);
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
		$this->db->join($this->tabla1.' b','a.id=b.item');
		$this->db->where('b.id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->id;
        }
	}
	function get_procesos(){
		$this->db->select('a.id,a.name as dd, b.name as d');
		$this->db->from('destinys_d a');
		$this->db->join('destinys b','a.destiny=b.id');
		// $this->db->where('destiny',$id);
		$this->db->order_by('a.id');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			foreach ($consulta->result_array() as $fila) {				
				$data[ $fila["id"] ] = $fila["d"].' '.$fila["dd"];
			}
			return $data;
		}
	}
}