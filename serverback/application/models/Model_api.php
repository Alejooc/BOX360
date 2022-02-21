<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_api extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='employees';
	var $tablaA='destinys';
	
	function findEmployee($id) {
		$this->db->select("a.*, b.name as destinyn");
        $this->db->limit(1);
        $this->db->where('a.id', $id);
		$this->db->join($this->tablaA.' b','a.destiny=b.id');
        return $this->db->get($this->tabla.' a')->row();
    }
}