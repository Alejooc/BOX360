<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_Main extends CI_Model {

	function __construct() {
		parent::__construct();
    }
	function GetOP() {
		$this->db->select('e.PtjAdv, f.id as itemid, f.prom, a.created, e.rolls, f.item, 
		e.cons_cut, e.covers, e.bedsheet, e.und_cut, e.und_produced, e.meters,
		a.id, a.op, b.name as state, c.name as machine, d.name as destiny');
        $this->db->from('orders a');
		$this->db->join('states b','a.state=b.id');
		$this->db->join('machines c','a.machine=c.id');
		$this->db->join('destinys d','a.destiny=d.id');
		$this->db->join('orders_item e','a.id=e.order');
		$this->db->join('items f','e.item=f.id');
		// $this->db->where('a.op',1459);
		$this->db->where('a.state <> 10');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function getAOP($id){
		$this->db->select('`a`.`op`,
		c.covers,
		c.bedsheet,
  `c`.`item`,
  `b`.`name`,
  b.id as itemid,
	e.id,
  `e`.`name` AS `process`,
  `d`.`order`,
  `d`.`measure`,
  d.SumAdd,
  d.SumEnd
  ');
		$this->db->from('orders a');
		$this->db->join('orders_item c','`a`.`id` = `c`.`order`');
		$this->db->join('items b','`c`.`item` = `b`.`item`');
		$this->db->join('item_by_proccess d','`b`.`item` = `d`.`item`');
		$this->db->join('destinys_d e','`d`.`destinyd` = `e`.`id`');
		$this->db->where('a.id',$id);
		$this->db->order_by('d.order');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function getSumProcess($op,$process,$item){
		$this->db->select('Sum(`a`.`cant`) AS total');
		$this->db->from('reports a');
		$this->db->join('orders_tasks b','`a`.`task` = `b`.`id`');
		$this->db->where('b.order',$op);
		$this->db->where('b.proccess',$process);
		$this->db->where('b.item',$item);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->total;
        }else{
			return 0;
		}
	}
	function GetOPS() {
		$this->db->select('b.name as label, count(a.id) as value');
        $this->db->from('orders a');
		$this->db->join('states b','a.state=b.id');	
		$this->db->group_by('b.name');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function GetMachines() {
		$this->db->select('b.name as label, count(a.id) as value');
        $this->db->from('orders a');
		$this->db->join('machines b','a.machine=b.id');	
		$this->db->group_by('b.name');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function GetTotal($tipo) {
		$this->db->select('count(*) as total');
		if($tipo==1){
			$this->db->from('orders a');
			$this->db->where('a.state<>',10);
		}
		if($tipo==2){
			$this->db->from('orders a');
			$this->db->join('orders_item b','a.id=b.order');
			$this->db->where('state<>',10);
		}
		if($tipo==3){
			$this->db->from('employees');
		}
		if($tipo==4){
			$this->db->from('reports');
		}
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->total;
        }
	}
	function getPDP(){
		$this->db->select('`a`.`created`,
  `a`.`op`,
  `c`.`item`,
  `c`.`name` as ref,
  `c`.`cloth`,
  `c`.`measure`,
  `b`.`rolls`,
  `d`.`name` AS `maquina`,
  `e`.`name` AS `destino`,
  `b`.`und_cut`,
  `b`.`und_produced`,
  `b`.`cons_cut`,
  `c`.`prom`,
  `a`.`auditA`,
  `a`.`auditB`,
  `a`.`auditC`,
  `a`.`audit`,
  `a`.`date_closing`');
        $this->db->from('orders a');
		$this->db->join('machines d','`a`.`machine` = `d`.`id`');
		$this->db->join('destinys e','`a`.`destiny` = `e`.`id`');
		$this->db->join('orders_item b','`a`.`id` = `b`.`order`');
		$this->db->join('items c','`b`.`item` = `c`.`id`');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function getOpDtail($id,$itemid){
		$this->db->select('b.und_cut,a. op, c.item');
        $this->db->from('orders a');
		$this->db->join('orders_item b','`a`.`id` = `b`.`order`');
		$this->db->join('items c','`b`.`item` = `c`.`id`');
		$this->db->where('a.id',$id);
		$this->db->where('b.item',$itemid);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row();
        }else{
			return 0;
		}
	}
	function updateOrder($registro) {
        $this->db->set($registro);
        $this->db->where('id', $registro['id']);
        $this->db->update('orders');
    }
	function updateOrderItem($registro) {
        $this->db->set($registro);
        $this->db->where('order', $registro['order']);
		$this->db->where('item', $registro['item']);
        $this->db->update('orders_item');
    }
}