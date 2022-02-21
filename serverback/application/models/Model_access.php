<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_access extends CI_Model {

	function __construct() {
		parent::__construct();
    }
	var $tabla='employees_access';
	var $tablaE='employees';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		$this->db->select("a.id, b.id as doc, b.name, a.date_access");
		$this->db->from($this->tabla." a");
		$this->db->join($this->tablaE." b",'b.id=a.employee');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		$this->db->group_start();
		$this->db->where('a.status',0);
		$this->db->group_end();
		$this->db->order_by('date_access','DESC');
		if (!empty($cb)){
			$this->db->or_like( 'b.id' , $cb);
			$this->db->or_like( 'b.name' , $cb);
		}
        $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb){
		$this->db->select("a.id, b.id as doc, b.name, a.date_access");
		$this->db->from($this->tabla." a");
		$this->db->join($this->tablaE." b",'b.id=a.employee');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		$this->db->group_start();
		$this->db->where('a.status',0);
		$this->db->group_end();
		$this->db->order_by('date_access','DESC');
		if (!empty($cb)){
			$this->db->or_like( 'b.id' , $cb);
			$this->db->or_like( 'b.name' , $cb);
		}
        //$this->db->limit($length,$start);
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
	function insert2($registro) {
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
	function get_employees($var){
		$this->db->select('id, name');
		$this->db->from('employees');
		$this->db->like('name', $var);
		$this->db->or_like('id', $var);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function get_depar($var){
		$this->db->select('id, name');
		$this->db->from('destinys');
		$this->db->like('name', $var);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function buscarr($fechai,$fechaf,$deparid){ 
		$this->db->select('`a`.`date_access`,
  `b`.`id`,
  `b`.`name`,
  `c`.`name` AS `catego`,
  `b`.`type`');
		$this->db->from('`employees_access` `a`');
		$this->db->join('`employees` `b`','`a`.`employee` = `b`.`id`');
		$this->db->join('`destinys` `c`','`b`.`destiny` = `c`.`id`');
		$this->db->where('a.date_access>=', $fechai.' 00:00:00');
		$this->db->where('a.date_access<=', $fechaf.' 23:59:59');
		if ($deparid>0){
			$this->db->where('c.id<=', $deparid);
		}
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
}