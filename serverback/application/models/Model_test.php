<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_test extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	function test1(){
		$this->db->select('*');
		$this->db->from('TEST');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function test1_insert($array){
		$this->db->set($array);
		$this->db->insert('item_by_proccess');
	}
	function employee(){
		// echo "finfin2";
		// die();   s
		$this->db->select('a.*,b.name as arean');
		$this->db->from('employees a');
		$this->db->join('destinys b','a.destiny=b.id');
		// $this->db->where('a.id','1002919393'); 
		$this->db->order_by('b.name,a.picture');
		
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function updateE($registro) {
        $this->db->set($registro);
        $this->db->where('id', $registro['id']);
        $this->db->update('employees');
    }
}