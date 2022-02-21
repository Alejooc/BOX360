<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_data extends CI_Model {

	function __construct() {
		parent::__construct();
    }
	var $tablaE='employees';
	var $tablaD='destinys';
	var $tablaDD='destinys_d';
	var $tablaEBP='employees_by_proccess';
	var $tablaR='reports';
	var $tablaO='orders';
	var $tablaI='items';
	var $tablaIP='item_by_proccess';
	var $tablaOT='orders_tasks';
	var $tablaM='machines';
	var $tablaS='states';
	var $tablaEX='employees_extra';
	var $tablaSS='supplies';
	var $tablaSI='suppliesitem';
	
	public function reporte1()
	{
		$this->db->select('a.id, a.name, a.type, b.name as area1');
        $this->db->from($this->tablaE.' a');
		$this->db->join($this->tablaD.' b','a.destiny=b.id');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	function get_reportemple(){
		$info = (object)[];
		$this->db->select('a.id, a.name, a.type, b.name as area1');
        $this->db->from($this->tablaE.' a');
		$this->db->join($this->tablaD.' b','a.destiny=b.id');
		// $this->db->where("a.cant<",'a.cantempo');
		// if( !empty($filter) ) {
			// $this->db->like($filter[0]->field,$filter[0]->value);
		// }
		// $this->db->order_by("a.edited","DESC");
		// if ($page->size >= 0 and $offset >= 0){
			// $this->db->limit( $page->size, $offset);
		// }
        $consulta = $this->db->get();		
        if ($consulta->num_rows() > 0) {			
			// $info->page = $page;
			$info->info = $consulta->result_array();			
            return $info;
        }
	}
	public function procesosxempleado($cedula){
		$this->db->select('b.name');
        $this->db->from($this->tablaEBP.' a');
		$this->db->join($this->tablaDD.' b','a.destinyd=b.id');
		$this->db->where('a.employee',$cedula);
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte2()
	{
		$this->db->select('`a`.`created`, `a`.`cant`, `e`.`op`, `d`.`employee`, `b`.`name`,`b`.`type`, `c`.`item`, c.name as itemn');
        $this->db->from($this->tablaR.' a');
		$this->db->join($this->tablaOT.' d','d.id=a.task');
		$this->db->join($this->tablaO.' e','e.id=d.order');
		$this->db->join($this->tablaE.' b','d.employee=b.id');
		$this->db->join($this->tablaI.' c','d.item=c.id');	
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte3()
	{
		$this->db->select('`a`.`item`, `b`.`name`, `c`.name as  proceso');
        $this->db->from($this->tablaIP.' a');
		$this->db->join($this->tablaI.' b','a.item=b.id');
		$this->db->join($this->tablaDD.' c','a.destinyd=c.id');	
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte4()
	{
		$this->db->select('`a`.`created`,`d`.`employee`,`b`.`name`,`d`.`item`,`a`.`cant`,`e`.`op`,`a`.`cant`,`a`.`ok`,`a`.`nook`');
        $this->db->from($this->tablaR.' a');
		$this->db->join($this->tablaOT.' d','d.id=a.task');
		$this->db->join($this->tablaO.' e','e.id=d.order');
		$this->db->join($this->tablaE.' b','d.employee=b.id');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte5()
	{
		$this->db->select('a.id,a.name');
        $this->db->from($this->tablaM.' a');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			$dato['machines']=$consulta->result();		
		}
		
		$this->db->select('a.op,a.machine');
        $this->db->from($this->tablaO.' a');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			$dato['orders']=$consulta->result();		
		}
		return $dato;
	}
	public function reporte6()
	{
		$this->db->select('a.id,a.name');
        $this->db->from($this->tablaS.' a');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			$dato['states']=$consulta->result();		
		}
		
		$this->db->select('a.op,a.state');
        $this->db->from($this->tablaO.' a');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			$dato['orders']=$consulta->result();		
		}
		return $dato;
	}
	public function reporte7(){
		$this->db->query("DROP TABLE IF EXISTS Masterinfo");
		$this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS IncentivosTMP AS (
			SELECT
			  a.created,
			  `c`.`id`,
			  `c`.`name`,
			  `c`.`type`,
			  `d`.`name` AS `proccess`,
			  `a`.`cant`,
			  d.incentive,
			  d.id as idp
			FROM
			  `reports` `a`
			  INNER JOIN `orders_tasks` `b` ON `a`.`task` = `b`.`id`
			  INNER JOIN `employees` `c` ON `b`.`employee` = `c`.`id`
			  INNER JOIN `destinys_d` `d` ON `b`.`proccess` = `d`.`id`
			  INNER JOIN `orders` `e` ON `b`.`order` = `e`.`id`
			)"
		);
		$this->db->select('id, name, type, proccess, sum(cant) as total, incentive, date(created) as fecha, idp');
        $this->db->from('IncentivosTMP');
		$this->db->group_by('date(created), id');
		
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte7_goal(){
		$this->db->select('`dategoal`, proccess, goal');
        $this->db->from('goals');
		$this->db->order_by('dategoal');
		
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte8()
	{
		$this->db->select('b.id,b.name,c.name as destinyd,c.extra, count(b.id) as total');
        $this->db->from($this->tablaEX.' a');
		$this->db->join($this->tablaE.' b','a.employee=b.id');
		$this->db->join($this->tablaDD.' c','a.destinyd=c.id');
		$this->db->group_by('b.id');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
	public function reporte9()
	{
		$this->db->select('c.name as itemn, b.name, c.item');
        $this->db->from($this->tablaSI.' a');
		$this->db->join($this->tablaSS.' b','a.supplies=b.id');
		$this->db->join($this->tablaI.' c','a.item=c.id');
		// $this->db->group_by('b.id');
		$consulta = $this->db->get();
		if ($consulta->num_rows() > 0) {
			return $consulta->result();		
		}
	}
}
	
	
	