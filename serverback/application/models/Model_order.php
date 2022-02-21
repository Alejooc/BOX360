<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_order extends CI_Model {

	function __construct() {
		parent::__construct();
    }	
	var $tabla='orders';
	var $tablaC='orders_quality';
	var $tablaCL='orders_type';
	var $tablaCI='orders_item';
	var $tablaH='orders_history';
	var $tablaI='items';
	var $tablaT='orders_tasks';
	var $tablaS='supplies_delivery';
	var $tablaSD='supplies_deliveryd';
	var $tablaSI='supplies_item';
	
	function get_all($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select("a.id, a.op, a.created, b.name as estados, a.machine, c.name as machinen, d.name as destinyn");
		$this->db->from('orders a');
		// $this->db->where('a.state <> 13');
		$this->db->join("states b","b.id=a.state");
		$this->db->join("machines c","c.id=a.machine");
		$this->db->join("destinys d","d.id=a.destiny");
		if ($this->session->userdata('info')->rol==2){
			$this->db->where('(state=1 or state=2)');
		}
		if ($this->session->userdata('info')->rol==3){
			$this->db->where('(state>=3 and state<=9)');
		}
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
			$this->db->like( 'a.op' , $cb);
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'c.name' , $cb);
			$this->db->or_like( 'd.name' , $cb);
			$this->db->group_end();
		}
        $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->result_array();		
		}
	}
	function get_all_total($start, $length, $order, $dir,$search,$columns_valid,$cb){
		if($order !=null) {
		   $this->db->order_by($order, $dir);
		}
		$this->db->select("a.id, a.op, a.created, b.name as estados, a.machine, c.name as machinen, d.name as destinyn");
		$this->db->from('orders a');
		// $this->db->where('a.state <> 13');
		$this->db->join("states b","b.id=a.state");
		$this->db->join("machines c","c.id=a.machine");
		$this->db->join("destinys d","d.id=a.destiny");
		if ($this->session->userdata('info')->rol==2){
			$this->db->where('(state=1 or state=2)');
		}
		if ($this->session->userdata('info')->rol==3){
			$this->db->where('(state>=3 and state<=9)');
		}
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
			$this->db->like( 'a.op' , $cb);
			$this->db->or_like( 'b.name' , $cb);
			$this->db->or_like( 'c.name' , $cb);
			$this->db->or_like( 'd.name' , $cb);
			$this->db->group_end();
		}
        // $this->db->limit($length,$start);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
			return $consulta->num_rows();	
		}
	}
	function find($id) {
		$this->db->select("a.*, c.name as machinen, d.name as destinyn");
        $this->db->limit(1);
        $this->db->where('a.id', $id);
		$this->db->join("states b","b.id=a.state");
		$this->db->join("machines c","c.id=a.machine");
		$this->db->join("destinys d","d.id=a.destiny");
        return $this->db->get($this->tabla.' a')->row();
    }
	function getNoOrden(){
		$this->db->select("max(a.op) as op");
        return $this->db->get($this->tabla.' a')->row()->op + 1;
	}
	function get_detail($id){
		$this->db->select('a.id,b.item,b.name,a.rolls,a.created');
        $this->db->from($this->tablaCI.' a');
		$this->db->join($this->tablaI.' b',"b.id=a.item");
		$this->db->where('order',$id);
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function find2($id) {
        $this->db->limit(1);
        $this->db->where('id', $id);
        return $this->db->get($this->tablaCI)->row();
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
	function insertH($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaH);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	function estados() {
       $this->db->select('id, name as estados');
		$this->db->from('states');
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["estados"];
            }
            return $data;
        }
    }
	function items() {
       $this->db->select('id, item, name');
		$this->db->from('items');
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["item"].' - '.$fila["name"];
            }
            return $data;
        }
    }
	
	
	function insert_items($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaCI);
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
	function updateCut($registro) {
        $this->db->set($registro);
        $this->db->where('order', $registro['order']);
		$this->db->where('item', $registro['item']);
        $this->db->update($this->tablaCI);
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
	function get_machines($var){
		$this->db->select('id, name');
		$this->db->from('machines');
		$this->db->like('name', $var);
		$consulta = $this->db->get();
          return $consulta->result();
	}
	function get_destiny($var){
		$this->db->select('id, name');
		$this->db->from('destinys');
		$this->db->like('name', $var);
		$consulta = $this->db->get();
          return $consulta->result();
	}
	function get_employee($var){
		$this->db->select('a.id, a.name, b.name as destiny');
		$this->db->from('employees a');
		$this->db->join('destinys b','a.destiny=b.id');
		$this->db->like('a.name', $var);
		$this->db->or_like('a.id', $var);
		$this->db->or_like('b.name', $var);
		$consulta = $this->db->get();
          return $consulta->result();
	}
	function get_item($var){
		$this->db->select('id, name');
		$this->db->from('items');
		$this->db->like('name', $var);
		$this->db->or_like('item', $var);
		$consulta = $this->db->get();
          return $consulta->result();
	}
	function get_states(){		
		$this->db->select('id,name');
        $this->db->from('states');
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
	function get_ppal($id){
		$this->db->select('a.order');
        $this->db->from($this->tablaCI.' a');
		$this->db->where('a.id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->order;
        }
	}
	// Detalle
	function insert_detail($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaCI);
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
        $this->db->update($this->tablaCI);
    }
	function delete2($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tablaCI);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	// Calidad
	function get_calidad($id){
		$this->db->select('a.*');
        $this->db->from('orders_quality a');
		$this->db->where('a.order',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	
	function insert_calidad($registro) {
		
        $this->db->set($registro);
        $this->db->insert($this->tablaC);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	function del_calidad($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tablaC);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	// Clasificación
	function get_clasificacion($id){
		$this->db->select('a.audit, a.auditA, a.auditB, a.auditC');
        $this->db->from('orders a');
		$this->db->where('a.id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row();
        }
	}
	function update_clasificacion($registro) {
		$this->db->set($registro);
        $this->db->where('id', $registro['id']);
        $this->db->update($this->tabla);
    }
	function del_clasificacion($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tablaCL);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	// Corte
	function get_corte($id) {
        $this->db->select('b.id, b.item as name, a.meters, a.covers, a.bedsheet, a.und_cut,a.und_produced,a.cons_cut');
        $this->db->from('orders_item a');
		$this->db->join('items b','b.id=a.item');
		$this->db->where('a.order',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
    }
	// Estado
	function get_estado($id){
		$this->db->select('a.id, a.created, a.comment, b.name as state');
        $this->db->from($this->tablaH.' a'); 
		$this->db->join("states b","b.id=a.state");
		$this->db->where('a.order',$id);
		$this->db->order_by('a.created','DESC');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
	}
	function insert_estado($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaH);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	function getSupplies($op) {
		$this->db->select('a.id');
        $this->db->from($this->tablaS.' a'); 
		$this->db->where('a.op',$op);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return false;
        }else{
			return true;
		}
	}
	function insertSupplies($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaS);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
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
	function insert_suppliesDetail($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaSD);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	function del_estado($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tablaH);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	function procesosxitem($item) {
       $this->db->select('destinyd,goal');
		$this->db->from('item_by_proccess');
		$this->db->where('item', $item);
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
    }
	function empleadosxproceso($proceso) {
       $this->db->select('employee');
		$this->db->from('employees_by_proccess');
		$this->db->where('destinyd', $proceso);
		$this->db->order_by('id');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
    }
	function insert_process($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaT);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;		
    }
	function get_tasks($id,$tipo,$busca){
		$busca=trim($busca);
		$this->db->select('a.id,e.op,a.order,b.id as cedula, b.name, c.item, c.name as itemn, d.name as process');
		$this->db->from($this->tablaT.' a');
		$this->db->join("employees b","b.id=a.employee");
		$this->db->join("items c","c.id=a.item");
		$this->db->join("destinys_d d","d.id=a.proccess");
		$this->db->join("orders e","e.id=a.order");
		$this->db->where('a.order', $id);
		if($tipo==1){
			$this->db->like('b.name', $busca);
			$this->db->or_like('b.id', $busca);			
		}
		if($tipo==2){
			$this->db->like('c.item', $busca);
			$this->db->or_like('c.name', $busca);	
		}
		if($tipo==3){
			$this->db->like('d.name', $busca);	
		}
		$this->db->order_by('c.item','DESC');
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->result();
        }
    }
	function insertTask($registro) {
        $this->db->set($registro);
        $this->db->insert($this->tablaT);
		$error=$this->db->error();		
		if($error["code"]>0){
			$dato["res"]= "Error: ".$error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	function updateTask($registro) {
        $this->db->set($registro);
        $this->db->where('id', $registro['id']);
        $this->db->update($this->tablaT);
    }
	function delTask($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->tablaT);
		$error=$this->db->error();		
        if($error["code"]>0){
			$dato["res"]= $error["message"];
		}else{
			$dato["res"]= "ok";
			$dato["id"]=$this->db->insert_id();	
		}
		return $dato;
    }
	function get_employees(){
		$this->db->select('id, name');
		$this->db->from('employees');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["name"];
            }
            return $data;
        }
	}
	function get_proccess(){
		$this->db->select('a.id, a.name, b.name as destino');
		$this->db->from('destinys_d a');
		$this->db->join('destinys b','b.id = a.destiny');
		$this->db->order_by('b.name,a.name');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["destino"].' --- '.$fila["name"];
            }
            return $data;
        }
	}
	function get_proccess_by_order($op){
		$this->db->select('`a`.`id`,
  `a`.`name`,
  `d`.`name` AS `destiny`');
		$this->db->from('item_by_proccess b');
		$this->db->join('orders_item c','`b`.`item` = `c`.`item`');
		$this->db->join('destinys_d a','`b`.`destinyd` = `a`.`id`');
		$this->db->join('destinys d','`a`.`destiny` = `d`.`id`');
		$this->db->order_by('d.name,a.name');
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["destiny"].' --- '.$fila["name"];
            }
            return $data;
        }
	}
	function get_items_by_order($op){
		$this->db->select('b.id, b.item, b.name');
		$this->db->from('orders_item a');
		$this->db->join('items b','b.id = a.item');
		$this->db->where('order', $op);
		$consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            foreach ($consulta->result_array() as $fila) {				
                $data[ $fila["id"] ] = $fila["item"].' - '.$fila["name"];
            }
            return $data;
        }
	}
	function consulop($id){
		$this->db->select('id,op');
        $this->db->from($this->tabla);
		$this->db->where('id',$id);
        $consulta = $this->db->get();
        if ($consulta->num_rows() > 0) {
            return $consulta->row()->op;
        }
	}
}