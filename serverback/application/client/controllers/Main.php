<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if( $this->session->userdata('isLoggedIn') ) {
			$this->load->library('table');            
            $tmpl = array(
                'table_open' => '<table class="table table-striped table-hover">',
                'heading_row_start' => '<tr>',
                'heading_row_end' => '</tr>',
                'heading_cell_start' => '<th>',
                'heading_cell_end' => '</th>',
                'row_start' => '<tr>',
                'row_end' => '</tr>',
                'cell_start' => '<td>',
                'cell_end' => '</td>',
                'row_alt_start' => '<tr>',
                'row_alt_end' => '</tr>',
                'cell_alt_start' => '<td>',
                'cell_alt_end' => '</td>',
                'table_close' => '</table>'
            );
            $this->table->set_template($tmpl);
			date_default_timezone_set('America/Bogota');
			$this->load->model('model_main');
		}else{
			redirect('login');
		}
              
	}
	public function index()
	{
		$data['titulo']='Bienvenido';
		$data['tabla']='';
		$data['campos'] = array('1' => 'N/A','2' => 'Empresa', '3' => 'Propietario', '4' => 'Placa', '5' => 'Factura');
		$data['informe'] = array('0' => 'Todo','1' => 'Activas','2' => 'Anuladas','3' => 'Pagadas','4'=>'Activas x manifiesto (Empresa)','5'=>'Activas x manifiesto (Placa)');
		/*
		$data['prod']=$this->model_main->get_prod();
		$data['clientes']=$this->model_main->get_clientes();
		$data['ventas']=$this->model_main->get_ventas();
		$data['gastos']=$this->model_main->get_gastos();
		$data['compras']=$this->model_main->get_compras();
		$data['locales']=$this->model_main->get_locales();
		*/
		$output= $this->load->view('main/home',$data,TRUE);
		$css_files = array(
            base_url('assets/admin') . '/css/datepicker3.css'
        );
		$js_files = array(			
            base_url('assets/admin/js/plugins/datepicker/bootstrap-datepicker.js'),
            base_url('assets/admin/js/plugins/datepicker/locales/bootstrap-datepicker.es.js'),            
            base_url('assets/admin/js/custom/main.js')
        );
		$this->output((object) array('output' => $output, 'js_files' => $js_files,
        'css_files' => $css_files, 'styletmpl' => 2, 'titulo' => $data['titulo']));		
	}
	public function index2()
	{
		$this->load->view('welcome_message');
	}
	public function output($output = null)
	{	
		$this->load->view('template.php', $output);
	}
}