<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_report extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='reports';
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select("h.name as process, a.id, e.op, c.id as employee, c.item, c.name, a.cant, b.name as empleado, a.created");
		$this->db->from("reports a");
		$this->db->join("orders_tasks d","d.id=a.task");
		$this->db->join("orders e","e.id=d.order");
		$this->db->join("employees b","b.id=d.employee");
		$this->db->join("items c","c.id=d.item");
		$this->db->join("destinys_d h","h.id=d.proccess");
		
		$e=1;
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$e=0;
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb) and $e){
			$this->db->group_start();
			$this->db->like( 'a.id' , $cb);
			$this->db->or_like( 'd.order' , $cb);
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'c.item' , $cb);
			$this->db->or_like( 'c.name' , $cb);
			$this->db->group_end();
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
		$this->db->select("h.name as process, a.id, e.op, c.id as employee, c.item, c.name, a.cant, b.name as empleado, a.created");
		$this->db->from("reports a");
		$this->db->join("orders_tasks d","d.id=a.task");
		$this->db->join("orders e","e.id=d.order");
		$this->db->join("employees b","b.id=d.employee");
		$this->db->join("items c","c.id=d.item");
		$this->db->join("destinys_d h","h.id=d.proccess");
		
		$e=1;
		foreach($search as $i=>$where){
			$ni=$i;
			if(isset($columns_valid[$ni]) and !empty($where["search"]["value"])) {
				$e=0;
				$this->db->like( $columns_valid[$ni] , $where["search"]["value"]);
			}
		}
		if (!empty($cb) and $e){
			$this->db->group_start();
			$this->db->like( 'a.id' , $cb);
			$this->db->or_like( 'd.order' , $cb);
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'c.item' , $cb);
			$this->db->or_like( 'c.name' , $cb);
			$this->db->group_end();
		}
        // $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->num_rows();
		}
	}
	function get_OpsItems(){
		$this->db->select('id, op');
		$this->db->from('orders');
		if ($this->session->userdata('info')->rol==4){
			$this->db->where('(state=3 or state=4)');
			$this->db->where('destiny',$this->session->userdata('info')->destiny);
		}
		$this->db->order_by('id');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}

	}
	function get_Itemss($id){
		$this->db->select('a.id, a.item, b.item as itemid, b.name, a.order');
		$this->db->from('orders_item a');
		$this->db->join('items b','a.item=b.id');
		$this->db->where('order', $id);
		$this->db->order_by('id');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function empleados() {
		$this->db->select('id, name as empleado');
		$this->db->from('employees');
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["empleado"];
            }
            return $data;
        }
    }
	function get_auto(){
		$this->db->select('name');
		$this->db->from('machines');
		$consulta = $this->db->get();
          return $consulta->result_array();
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
	function insert_report($registro) {
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
	function get_employees($op,$item,$employee){
		$this->db->select('b.id, b.name');
        $this->db->from('orders_tasks a');
		$this->db->join('employees b','b.id=a.employee');
		
		$this->db->where('a.order', $op);
		$this->db->where('a.item', $item);
		$this->db->like('b.name', $employee);
		
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
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
	function get_tareas($op,$item,$employee){
		$this->db->select('a.id, b.name');
        $this->db->from('orders_tasks a');
		$this->db->join('destinys_d b','b.id=a.proccess');
		$this->db->where('a.order', $op);
		$this->db->where('a.employee', $employee);
		$this->db->where('a.item', $item);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
}