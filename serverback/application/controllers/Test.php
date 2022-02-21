<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_test','model');
	}
	public function index(){
		
	}
	public function cripto(){
		$this->load->library("Cryptojs");
		$password = $this->config->item('encryption_key_js');
		
		// encrypt
		$originalValue = 'Andres+Londono&&14704'; // this could be any value
		$encrypted = $this->cryptojs::encrypt($originalValue, $password);
		echo "Encrypted: " . base64_encode($encrypted) . "\n";
		//die();
		// decrypt
		$encrypted = '{"ct":"a4w0uAkdbsZW\/zprWbp5Q5W+FWowKbf1P1+wm1Wfvw0=","iv":"762b3b6bd40ba5c03b29aabbda680007","s":"eb5e06c77068a008"}';
		$decrypted = $this->cryptojs::decrypt($encrypted, $password);
		echo "Decrypted: " . print_r($decrypted, true) . "\n";
	}
	public function cargarSinFondo(){
		$directorio = './sinfondo';
		$ficheros1  = scandir($directorio);
		for($i=0;$i<count($ficheros1);$i++){
			// if ( $ficheros1[$i]=='1002919393.png' ){
				$n = explode('.',$ficheros1[$i]);
				$newfile = './assets/archivos/empleados/'.$n[0].'/'.$n[0].'.png';
				if (!copy($directorio."/$n[0].png",$newfile)) {
					echo "$i $ficheros1[$i] ERROR<br>";
				}else{
					echo "$i $ficheros1[$i] OK<br>";
				}
			// }
		}
		
	}
	public function downloadzip(){
		$zip = new ZipArchive;
		$tmp_file = 'myzip.zip';
		if ($zip->open($tmp_file,  ZipArchive::CREATE)) {
			$e = $this->model->employee();
			foreach($e as $item){
				$file = './assets/archivos/empleados/'.$item->id.'/'.$item->picture;
				if ( file_exists($file)){
					if (!empty($item->picture)){
						// echo "$item->picture <br>";
						$zip->addFile($file, $item->picture);
					}
				}
			}
			$zip->close();
			// echo 'Archive created!';
			header('Content-disposition: attachment; filename=files.zip');
			header('Content-type: application/zip');
			readfile($tmp_file);
	   } else {
		   echo 'Failed!';
	   }
	}
	public function carnet(){
		$e = $this->model->employee();
		foreach($e as $item){
			$file = './assets/archivos/empleados/'.$item->id.'/'.$item->picture;
			if ( file_exists($file)){
				if(!empty($item)){
					$this->carnetpdf($item);
				}
			}
		}
	}
	public function carnetpdf($emple){
		if(!empty($emple)){
			$do=$emple->id;				
			$no=$emple->name;
			$rh=$emple->rh;
			$fo=$emple->picture;
			
			if ( !file_exists('./assets/archivos/empleados/'.$do)){
				mkdir( './assets/archivos/empleados/'.$do, 0777 );
				$this->load->helper('file');
				write_file("./assets/archivos/empleados/$do/index.html", '');
			}
			if ( strlen($fo)>0 ){
				$urlfotofin="./assets/archivos/empleados/".$do."/$fo";
				if(file_exists($urlfotofin)){
					$urlfotofin=base_url("serverback/assets/archivos/empleados/".$do."/$fo");
				}else{
					$urlfotofin=base_url("assets/images/noimage.png");
				}
			}else{
				$urlfotofin=base_url("assets/images/noimage.png");
			}
			$this->load->library("Cryptojs");
			$password = $this->config->item('encryption_key_js');
			// encrypt
			$originalValue = "$do&&$no"; // this could be any value
			$encrypted = $this->cryptojs::encrypt($originalValue, $password);
			
			$contentqr="https://aristextil.com/empleados/?e=".base64_encode($encrypted);
			$this->load->library("Barcode");
			$this->barcode->output_image('png', 'qr', $contentqr, "qr_".$do, './assets/archivos/empleados/'.$do.'/', '');
			$qr=base_url("serverback/assets/archivos/empleados/".$do."/qr_$do.png");
			$tabla="
			<img style='z-index:10;position:absolute;width:324px;height:204px;' src='".base_url("assets/images/backgroundCard.png")."'>";
			$tabla.="<div style='border:0px solid #000; z-index:100;position:relative;font-size:12px;width:285px;height:195px;font-family: sans-serif,Arial;margin:5px 0 0 32px;resize:none;'>
				<img src='$urlfotofin' style='margin-left:-15px;position:absolute;margin-top:10px;height:195px;border:0px solid #000;'>
				<span style='width:193px;margin-top:60px;margin-left:90px;position:absolute;border:0px solid #000;text-align:center;'>
					<span style='font-size:12px;font-weight:bold;'>$no</span><br/>
					<span style='font-size:11px;'>DI: ".number_format($do,0,".",".")."</span><br/>
					<span style='font-size:11px;'>RH: ".$rh."</span>
				</span>
				<img style='position:absolute; margin-top:125px;margin-left:216px;' src='$qr' height='66'>
			</div>";
			$this->tabla = $tabla;
			$this->output->set_header('Content-Type: application/octet-stream');
			$this->load->library('pdfgenerator');
			$this->tabla.="<style>@page { margin: 0px; } body { margin: 0px;background:red; } html { margin: 0px}</style>";
			//"c8" => array(0, 0, 161.57, 229.61),
			$custom = array(0, 0, 153.08, 240.95);
			$output=$this->pdfgenerator->generate($this->tabla, "Carnet",false,$custom,"landscape");
			file_put_contents("./sinfondo/$do.pdf", $output);
		}
	}
	public function test(){
		$e = $this->model->employee();
		echo "<table border=1>
			<tr>
				<th>No</th>
				<th>Ced</th>
				<th>Nombre</th>
				<th>Area</th>
				<th>Foto</th>
			</tr>
		";
		foreach($e as $item){
			$c=1;
			echo "<tr>";
			echo "<td>$c</td>
				<td>$item->id</td>
					<td>$item->name</td>
					<td>$item->arean</td>
					<td>";
			$c++;
			$file = './assets/archivos/empleados/'.$item->id.'/'.$item->picture;
			if ( file_exists($file)){
				// $newfile = './assets/archivos/empleados/'.$item->id.'/'.$item->id.'.png';
				// if (!copy($file, $newfile)) {
					// echo "failed to copy";
				// }else{
					// echo "<img height='50px' src='http://192.168.0.225/serverback/	assets/archivos/empleados/$item->id/$item->id.png'/>";
					// $registro = array(
							// 'id'=>$item->id,
							// 'picture'=>$item->id.'.png'
					// );
					// $this->model->updateE($registro);
				// }
				echo "<img height='50px' src='http://192.168.0.225/serverback/	assets/archivos/empleados/$item->id/$item->id.png'/>";
			}else{
				echo "archivo no existe";
			}
			echo "</td><td>";
			$file = './sinfondo/'.$item->id.'.pdf';
			if ( file_exists($file)){
				echo "<a href='".base_url('/serverback/'.$file)."' target='_blank'>Carnet</a>";
			}
			echo "</td></tr>";
		}
		echo "</table>";
	}
	public function test1(){
		die();
		// $a = array('18','26','22','27','20');
		$a = array('1','2','3','4');
		$t=$this->model->test1();
		foreach($t as $item){
			for($i=0;$i<count($a);$i++){
				$array= array(
					'destinyd'=>$a[$i],
					'item'=>$item->id,
					'goal'=>0
				);
				$this->model->test1_insert($array);
			}
		}
	}
	public function qr(){
		$this->load->library("Barcode");
		$ced=31475238;
		$name="MONA LLANOS EUCARIS ";
		$this->barcode->output_image('png', 'qr', $ced."&&".$name, $ced, '');
		echo "<table border=1 width='100%'><tr>";
		for ($i=0; $i<4; $i++){
			echo "<td><center>$name <br/> <img src='http://localhost/planta/serverback/qr/$ced.png'></center></td>";
		}
		echo "</tr></table>";
	}
}