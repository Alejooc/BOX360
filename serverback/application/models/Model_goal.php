<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_goal extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='goals';
	var $tablad='destinys_d';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select('a.id, a.dategoal, a.proccess, a.goal, b.name as process');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablad.' b','a.proccess=b.id');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.dategoal' , $cb);
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
		$this->db->select('a.id, a.dategoal, a.proccess, a.goal, b.name as process');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablad.' b','a.proccess=b.id');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->like( 'a.dategoal' , $cb);
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
	function get_proccess($var){
		$this->db->select('a.id, a.name, b.name as area');
		$this->db->from('destinys_d a');
		$this->db->join('destinys b','b.id=a.destiny');
		$this->db->group_start();
		$this->db->like('a.name', $var);
		$this->db->or_like('a.id', $var);
		$this->db->group_end();
		$this->db->order_by('b.name,a.name');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
          return $consulta->result();
		}
	}
	function get_proccessn($id){
		$this->db->select('name');
		$this->db->from('destinys_d');
		$this->db->where('id', $id);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
          return $consulta->row()->name;
		}
	}
}