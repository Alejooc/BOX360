<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_process extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='destinys_d';
	var $tabla1='destinys';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select('a.id,a.name, b.name as area, a.incentive, a.extra');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tabla1.' b','b.id=a.destiny');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.name' , $cb);
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
		$this->db->select('a.id,a.name, b.name as area, a.incentive, a.extra');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tabla1.' b','b.id=a.destiny');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.name' , $cb);
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
	function get_areas(){
		$this->db->select('id,name');
		$this->db->from('destinys');
		$this->db->where('type', 'Interna');
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