<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_supplydelivery extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='supplies_delivery';
	var $tablaD='supplies_deliveryd';
	var $tablaS='supplies';
	var $tablaI='items';
	var $tablaSE='supplies_deliverys';
	var $tablaO='orders';
	var $tablaOI='orders_item';
	var $tablaSI='supplies_item';
	var $tablaDE='destinys';
	
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		$this->db->select(' a.id, `a`.`op`, b.op as opid,
  `a`.`item`,
  `c`.`name`,
  `a`.`deliverydate`');
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaSE.' c','`c`.`id` = `a`.`status`');
		$this->db->join($this->tablaO.' b','`b`.`id` = `a`.`op`');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->or_like( 'a.op' , $cb);
			$this->db->or_like( 'a.item' , $cb);
			$this->db->or_like( 'c.name' , $cb);
		}
        $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb){
		$this->db->select(' a.id, `a`.`op`,
  `a`.`item`,
  `c`.`name`,
  `a`.`deliverydate`');
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaSE.' c','`c`.`id` = `a`.`status`');
		$this->db->join($this->tablaO.' b','`b`.`id` = `a`.`op`');
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb)){
			$this->db->or_like( 'a.op' , $cb);
			$this->db->or_like( 'a.item' , $cb);
			$this->db->or_like( 'c.name' , $cb);
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
	function get_ops(){
		$this->db->select('id, op as name');
		$this->db->from($this->tablaO);
		$this->db->where('state in (1,2,3)');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			$data[ 0 ] = 'Seleccione';
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
	
	function get_status(){
		$this->db->select('id,name');
		$this->db->from($this->tablaSE);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
	function getItemsOp($op){
		$this->db->select('b.id,a.item as name');
		$this->db->from($this->tablaOI.' a');
		$this->db->join($this->tablaI.' b','a.item=b.id');
		$this->db->where('a.order',$op);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
	function get_detail($id){
		$this->db->select('a.id,a.supplyd,a.supply,b.name as supplyn,a.qty,b.cod');
        $this->db->from($this->tablaD.' a');
		$this->db->join($this->tablaS.' b','b.id=a.supply');
		$this->db->where('supplyd',$id);
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function get_supplies($var){
		$this->db->select('id, name');
		$this->db->from($this->tablaS);
		$this->db->like('name', $var);
		$this->db->or_like('id', $var);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function find2($id) {
        $this->db->limit(1);
        $this->db->where('id', $id);
        return $this->db->get($this->tablaD)->row();
    }
	
	function getSupplyName($id){
		$this->db->select('name');
		$this->db->from($this->tablaS);
		$this->db->where('id', $id);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->row()->name;
        }
	}
	function insert_detail($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaD);
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
        $this->db->update($this->tablaD);
    }
	function getSuppliesItem($item){
		$this->db->select('supplies,cons');
		$this->db->from($this->tablaSI);
		$this->db->where('item', $item);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}

	function consultpdf($id){
		$this->db->select('`a`.`id` AS `id`,
  `a`.`code`,
  `a`.`deliverydate`,
  `d`.`item`,
  `d`.`name`,
  `c`.`und_produced`,
  `e`.`name` AS `satelite`,
  a.comment');
		$this->db->from($this->tabla.' a');
		$this->db->join($this->tablaO.' b','a.op=b.id');
		$this->db->join($this->tablaOI.' c','a.item=c.item and b.id=c.order');
		$this->db->join($this->tablaI.' d','c.item=d.id');
		$this->db->join($this->tablaDE.' e','`b`.`destiny` = `e`.`id`');
		$this->db->where('a.id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row();
        }
	}
	function consultpdf2items($id){
		$this->db->select('a.qty, b.name, b.cod');
		$this->db->from($this->tablaD.' a');
		$this->db->join($this->tablaS.' b','a.supply=b.id');
		$this->db->where('a.supplyd',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
}