<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!function_exists('get_nombre_portal')) {
    function get_nombre_portal() {
        return '';
    }
}
if (!function_exists('get_utilidad')) {
    function get_utilidad($country) {
        $CI = & get_instance();
        $CI->load->model('model_main');
		return $CI->model_main->get_utilidad($country);
    }
}


if (!function_exists('get_calculos')) {
	function get_calculos($id) {
		$stotal = 0;
		$CI = & get_instance();
		$CI->load->model('model_main');
		
		$prodm = $CI->model_main->GetProductsQ($id);
		if (isset($prodm) ){
			foreach ($prodm as $row){
				if($id>0){
					$stotal += $row["final"] * $row["qty"];
				}
			}
		}
		$dato["total"]  = $stotal;
		return $dato;
	}
}
if (!function_exists('get_calculos2')) {
	function get_calculos2($id) {
		$stotal = 0;
		$CI = & get_instance();
		$CI->load->model('model_main');
		
		$prodm = $CI->model_main->GetProductsS($id);
		if (isset($prodm) ){
			foreach ($prodm as $row){
				if($id>0){
					$stotal += $row["final"] * $row["qty"];
				}
			}
		}
		$dato["total"]  = $stotal;
		return $dato;
	}
}
if (!function_exists('formatonumero')) {

    function formatonumero($numero, $cant = "-1") {
		if($numero>0){
			$CI = & get_instance();
			if ($cant >= 0) {
				$dec = $cant;
			} else {
				$dec = 2;
			}
			$sdec = ",";
			$smil = ".";
			return "$" . number_format($numero, $dec, $sdec, $smil);
		}
    }

}
if (!function_exists('ProdStock')) {
	function ProdStock($id, $store) {
		$CI = & get_instance();
        $CI->load->model('model_main');
        $cant = $CI->model_main->getInventoryProd($id, $store);
		$cant2 = $CI->model_main->getInventoryProd2($id, $store);
		//echo "$cant <br>aa";
		// echo "$cant2";
        return $cant-$cant2;
	}
}
if (!function_exists('ProdStockRealBodega')) {
	function ProdStockRealBodega($id,$store) {
		$CI = & get_instance();
        $CI->load->model('model_main'); 
        $cant = $CI->model_main->getInventoryProd($id,$store);
        return $cant;
	}
}
if (!function_exists('MayusToCapital')) {
    function MayusToCapital($texto) {		
		$needle = array("á","é","í","ó","ú","ñ","°","º","ü",
				"Á","É","Í","Ó","Ú","Ñ");
		$replace = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;","o","o","u",
					"&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&ntilde;");
		$texto=str_replace($needle, $replace,$texto);
		$texto=ucwords(strtolower($texto));		
		return $texto;
	}
}
if (!function_exists('nuevafecha')) {
    function nuevafecha($fecha,$meses) {		
		$nuevafecha = strtotime ( "+$meses month" , strtotime ( $fecha ) ) ;
		$nuevafecha = date ( 'Y-m-j' , $nuevafecha );		 
		return $nuevafecha;	
	}
}
if (!function_exists('dias_transcurridos')) {
	function dias_transcurridos($fecha_i,$fecha_f)
	{
		if($fecha_i=="0000-00-00"){
			$fecha_i=date("Y-m-d");
		}
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias = floor($dias);		
		return $dias;
	}
}
if (!function_exists('my_validation_errors')) {

    function my_validation_errors($errors) {
        $salida = '';
        if ($errors) {
            $salida = '<div class="alert alert-danger fade in">';
            $salida = $salida . '<button type="button" class="close" data-dismiss="alert"> x </button>';
            $salida = $salida . '<h4> Mensajes Validacion </h4>';
            $salida = $salida . '<small>' . $errors . '</small>';
            $salida = $salida . '</div>';
        }
        return $salida;
    }

}
if (!function_exists('my_success')) {
    function my_success($success) {
        $salida = '';
        if ($success) {
            $salida = '<div class="alert alert-success fade in">';
            $salida = $salida . '<button type="button" class="close" data-dismiss="alert"> x </button>';
            $salida = $salida . '<h4> Exito </h4>';
            $salida = $salida . '<small>' . $success . '</small>';
            $salida = $salida . '</div>';
        }
        return $salida;
    }
}
if (!function_exists('get_session')) {
    function get_session($sess) {
		$CI = & get_instance();	
		$CI->load->model('model_login','modelhp');
        return $CI->modelhp->get_session($sess);
    }
}
if (!function_exists('enviar_email')) {
    function enviar_email($para,$asunto,$contenido,$copia="",$adjuntos="") {
		$CI = & get_instance();
		$CI->load->library('email');
		$CI->load->model('model_main'); 
		
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = 'chi-node60.websitehostserver.net';
		$config['smtp_user'] = 'test@indicreativos.com';
		$config['smtp_pass'] = '@Test_Andres';
		$config['smtp_port'] = '25';
		$config['charset'] = 'utf-8';
		$config['newline']    = "\r\n";        
		$config['mailtype'] = 'html'; 
		$config['wordwrap'] = TRUE;
		$CI->email->initialize($config);
		
		if(!empty($adjuntos)){
			foreach($adjuntos as $adj){
				//print_r($adj);
				$CI->email->attach($adj);
			}
		}

		$CI->email->from("test@indicreativos.com", 'InDicretivos');
		$CI->email->to($para);
		if(!empty($copia)){
			$CI->email->cc($copia);	
		}		
		$CI->email->subject($asunto);
					
		$CI->email->message($contenido);
		if ( !$CI->email->send() ){
			$arr["msg"]=$CI->email->print_debugger();
			$arr["est"]=0;
		}else{
			$arr["est"]=1;
		}
		return $arr;
		/*
		if( isset($CI->session->userdata("info")->area) ){
			$area=$CI->session->userdata("info")->area;
			if($area=="admin"){
				$usrid=$CI->session->userdata("info")->Id;
			}else{
				$usrid=$CI->session->userdata("info")->Di;
			}			
		}else{
			$area="CLIENTE";
			$usrid=0;
		}
		
		
		if ( !$CI->email->send() ){
			$arr["msg"]=$CI->email->print_debugger();
			$arr["est"]=0;
			$err=$CI->email->print_debugger();
			$err=str_replace("'","",$err);
			$err=str_replace('"',"",$err);
			$err=str_replace('/',"",$err);
			if(empty($para)){
				$para="Sin Correo";
			}
			$CI->model_main->save_log(
				array(											
					'fecha'=>date("Y-m-d H:i"),
					'para' => $para,
					'asunto' => $asunto,
					'copia' => $copia,
					'estado' => 0,
					'error' => $err,
					'area' => $area,
					'usrid' => $usrid
				)
			);
    		return $arr;
		}else{
			$arr["est"]=1;		
			$CI->model_main->save_log(
				array(											
					'fecha'=>date("Y-m-d H:i"),
					'para' => $para,
					'asunto' => $asunto,
					'copia' => $copia,
					'estado' => 1,
					'area' => $area,
					'usrid' => $usrid
				)
			);
			return $arr;
		}
		*/
	}
}
if (!function_exists('save_log_cte')) {
    function save_log_cte($modulo,$accion) {
		$CI = & get_instance();
		$CI->load->model('model_main'); 
		$CI->model_main->save_log_cte(
				array(											
					'fecha'=>date("Y-m-d H:i"),
					'modulo' => $modulo,
					'accion' => $accion,					
					'usrid' => $CI->session->userdata("info")->Di
				)
			);
	}
}
if (!function_exists('mail_actualizacion')) {
    function mail_actualizacion($id,$obs,$copia,$tramite) {
		$CI = & get_instance();
		$CI->load->model('model_main');
		$CI->model_main->mail_actualizacion($id,$obs,$copia,$tramite);
	}
}
if (!function_exists('get_cms')) {
    function get_cms($id) {
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_cms($id);
	}
}
if (!function_exists('get_cmsvariables')) {
	function get_cmsvariables($radicado,$fecha,$mensaje,$cc){
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_cmsvariables($radicado,$fecha,$mensaje,$cc);
	}
}
if (!function_exists('get_cmsvariables2')) {
	function get_cmsvariables2($radicado,$fecha,$mensaje,$cc,$matricula){
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_cmsvariables2($radicado,$fecha,$mensaje,$cc,$matricula);
	}
}
if (!function_exists('get_email_user')) {
	function get_email_user($id){
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_email_user($id);
	}
}
if (!function_exists('crear_carpeta')) {
	function crear_carpeta($carpeta){
		$conta="<html><head><title>Comvezcol</title><meta HTTP-EQUIV='REFRESH' content='0; url=http://comvezcol.org/'></head><body></body></html>";
		if (!file_exists($carpeta)) {
			mkdir($carpeta);
			$fp = fopen($carpeta."index.html","a");
			fwrite($fp, $conta . PHP_EOL);
			fclose($fp);
		}else{
			if (!file_exists($carpeta."index.html")) {
				$fp = fopen($carpeta."index.html","a");
				fwrite($fp, $conta . PHP_EOL);
				fclose($fp);
			}
		}
	}
}
if (!function_exists('creartiff')) {	
	function creartiff($origen,$nombre,$destino){
		/*
		$image = new Imagick($origen);
		$image->writeImage($destino.$nombre.".tiff");	
		*/
		$url="http://administrador.consejoapp.com.co/magick/cambio.php";
		// $url="http://be.demowebs.tk/magick/cambio.php";
		$postData["llave"]="a6756746e0";
		$postData["origen"]=$origen;
		$postData["nombre"]=$nombre;
		$postData["destino"]=$destino;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$result = curl_exec($ch);
		curl_close($ch);
		//$image = new Imagick($origen);						
		//$image->writeImage($destino.$nombre.".tiff");
	}
}
if (!function_exists('get_ciudades')) {
	function get_ciudades(){		
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_ciudades();
	}
}
if (!function_exists('get_dptos')) {
	function get_dptos(){		
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_dptos();
	}
}
if (!function_exists('validamatricula')) {
	function validamatricula($id){		
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->validamatricula($id);
	}
}
if (!function_exists('permisos')) {
	function permisos($permisos){
		$CI = & get_instance();
		if (!in_array($CI->session->userdata("info")->rol, $permisos)){
			$data["tipo"]=0;
			$data["msg"]="No tiene permiso para acceder a este modulo";
			echo json_encode($data);
			die();
		}
	}
}
if (!function_exists('validsession')) {
	function validsession($data){
		if(isset($data["msg"]->info)){
			if($data["tipo"]){
				$info=$data["msg"]->info;
				if(isset($info->area)){
					if($info->area!="admin"){
						$data["tipo"]=-1;
						$data["msg"]="Acceso restringido";
					}
				}else{
					$data["tipo"]=-1;
					$data["msg"]="Acceso restringido";
				}				
			}else{
				echo json_encode($data);
				die();
			}
		}else{
			$data["tipo"]=-1;
			$data["msg"]="Acceso invalido";
		}
		if($data["tipo"]==-1){
			echo json_encode($data);
			die();
		}else{
			return $info;
		}
	}
}
if (!function_exists('validsessionp')) {
	function validsessionp($data){
		if(isset($data["msg"]->info)){
			if($data["tipo"]){
				$info=$data["msg"]->info;
				if(isset($info->area)){
					if($info->area!="provider"){
						$data["tipo"]=-1;
						$data["msg"]="Acceso restringido";
					}
				}else{
					$data["tipo"]=-1;
					$data["msg"]="Acceso restringido";
				}				
			}else{
				echo json_encode($data);
				die();
			}
		}else{
			$data["tipo"]=-1;
			$data["msg"]="Acceso invalido";
		}
		if($data["tipo"]==-1){
			echo json_encode($data);
			die();
		}else{
			return $info;
		}
	}
}
if (!function_exists('ChkToken')) {
	function ChkToken($headers){
		$data["tipo"]=1;
		if(empty($headers["Token"])){			
			$data["tipo"]=-1;
			$data["msg"]="Token invalido1";
		}else{
			$CI = & get_instance();
			$data["msg"]= JWT::decode($headers["Token"], $CI->config->item('encryption_key'), array('HS256'));			
			if($data["msg"]=="Signature verification failed"){
				$data["tipo"]=-1;
				$data["msg"]="Token invalido";
			} 
			if($data["msg"]=="Expired token"){
				$data["tipo"]=-1;
				$data["msg"]="Token vencido";
			}			
		}
		return $data;
	}
}
if (!function_exists('validsession2')) {
	function validsession2($data){
		if($data["tipo"]){
			if(isset($data["msg"]->info)){
				$info=$data["msg"]->info;			
				if(isset($info->area)){
					if($info->area!="client"){
						$data["tipo"]=-1;
						$data["msg"]="Acceso restringido";
					}
				}else{
					$data["tipo"]=-1;
					$data["msg"]="Acceso restringido";
				}				
			}else{
				$data["tipo"]=-1;
				$data["msg"]="Acceso invalido";
			}
			if($data["tipo"]==-1){
				echo json_encode($data);
				die();
			}else{
				return $info;
			}
		}else{
			echo json_encode($data);
			die();
		}
	}
}
if (!function_exists('get_matricula')) {
	function get_matricula($id){		
		$CI = & get_instance();
		$CI->load->model('model_main');
		return $CI->model_main->get_matricula($id);
	}
}
if (!function_exists('antecedente_mail')) {
	function antecedente_mail($id){
		$CI = & get_instance();
		
		$CI->load->model('model_antecedente');
		$suspencion="";
		$matricula=$CI->model_antecedente->get_matricula($id);
				
		$firma=get_firma(4);
		$templatec=$CI->model_antecedente->get_tmp(2);
		$templatec2=$CI->model_antecedente->get_tmp(3);
		$habilitado=get_habilitado($id);
		//print_r($habilitado);
		if($habilitado!=""){
			$suspencion = $habilitado;
			$habilitado = false;
		}else{
			$habilitado = true;
		}		
		$tabla1=get_sanciones($id);
		$item=$CI->model_antecedente->get_acta($id);
		$tabla="<span id='areaprint'>";
		
		$fexp= $item->fexp;
		$fexp=str_replace("-","",$fexp);
		$fexp=substr($fexp,0,8);
		if($fexp!="00000000"){
			$cerid=$fexp."CANT".$item->radicado.$item->mkey.$item->id;			
			$no=$item->nombres." ".$item->primerApellido." ".$item->segundoApellido;
			$no=(strtoupper($no));
			$di=number_format($item->noDocumento,0,".",".")." de ". ucfirst($item->ciudad);
			//$di=(strtoupper($di));
			$ma=number_format($item->matricula,0,".",".");
			$fe=$item->fexp;
			$fe=explode("-",substr($fe,0,10));
							
			if(!empty($item->titulo2)){
				$titu=$item->titulo2;
				$un=( $CI->model_antecedente->buscaruni2($item->uni) );
			}else{
				$titu=$item->titulo;
				$un=($item->universidad);
			}
							
			if($titu=="mvz" or $titu=="MVZ"){
				$ti="M&eacute;dico Veterinario Zootecnista";
			}
			if($titu=="mv" or $titu=="MV"){
				$ti="M&eacute;dico Veterinario";
			}
			if($titu=="z" or $titu=="Z"){
				$ti="Zootecnista";
			}
			
			$dia=substr($fexp,6,2);	
			$ano=substr($fexp,0,4);	
			$mes=substr($fexp,4,2);	
			
			$meses=array(
				"01"=>"Enero",
				"02"=>"Febrero",
				"03"=>"Marzo",
				"04"=>"Abril",
				"05"=>"Mayo",
				"06"=>"Junio",
				"07"=>"Julio",
				"08"=>"Agosto",
				"09"=>"Septiembre",
				"10"=>"Octubre",
				"11"=>"Noviembre",
				"12"=>"Diciembre"
			);
			
			$fe=$fe[2]." de ".$meses[$fe[1]]." de ".$fe[0];
			$mes=$meses[$mes];
			if($habilitado==true){
				$firma=explode("&&",$firma);
				
				$templatec=str_replace('$no',$no,$templatec);
				$templatec=str_replace('$di',$di,$templatec);
				$templatec=str_replace('$ma',$ma,$templatec);
				$templatec=str_replace('$fe',$fe,$templatec);
				$templatec=str_replace('$ti',$ti,$templatec);
				$templatec=str_replace('$un',$un,$templatec);			
				$templatec=str_replace('$cerid',$cerid,$templatec);
				$templatec=str_replace('$profesion',$ti,$templatec);					
								
				$tabla.=$templatec;				
					
				$tabla.=("$tabla1 <p style='text-align:justify;'>Se firma en Bogot&aacute; D.C., a los ($dia) d&iacute;as del mes de $mes de $ano.<br/><br/>
				<img style='height:50px;display:block;margin:0;' src='http://consejoapp.com.co/adminapp/firmas/$firma[2]'></p>						
				<p style='text-align:left;'>
				".($firma[1])."<br>
				".($firma[0])."<br/>
				</p>");
			}else{
				$firma=explode("&&",$firma);				
					
				$templatec2=str_replace('$no',$no,$templatec2);
				$templatec2=str_replace('$di',$di,$templatec2);
				$templatec2=str_replace('$ma',$ma,$templatec2);
				$templatec2=str_replace('$fe',$fe,$templatec2);
				$templatec2=str_replace('$ti',$ti,$templatec2);
				$templatec2=str_replace('$un',$un,$templatec2);			
				$templatec2=str_replace('$cerid',$cerid,$templatec2);
			
				$tabla.=$templatec2;
				$tabla.=$suspencion;
				$tabla.=("<p style='text-align:justify;'>Se firma en Bogotá D.C., a los ($dia) días del mes de $mes de $ano.<br/><br/>
					<img style='height:50px;display:block;margin:0;' src='http://consejoapp.com.co/adminapp/firmas/$firma[2]'></p>						
					<p style='text-align:left;'>				
					".($firma[1])."<br>
					".($firma[0])."<br/>
					</p>");
			}
			
			$tabla.="</span>";
					
			$CI->load->library('pdfgenerator');
			return $CI->pdfgenerator->generate($tabla, "antecedente",false);
		}else{
			$CI->load->library('pdfgenerator');
			return $CI->pdfgenerator->generate("Fecha de aprovación invalida", "antecedente",false);
		}
	}
}
if (!function_exists('get_habilitado')) {
	function get_habilitado($id){		
		$CI = & get_instance();		
		$CI->load->model('model_antecedente');
		
		$tabla="no";
		$item=$CI->model_antecedente->get_habilitado($id);
		
		if(!empty($item)){
			$fec_ini=$item->fechaInicio;
			$dias=$item->tiempoDias;
			$fec_fin=date("Y-m-d", strtotime("$fec_ini + $dias days"));
			$hoy=date("Y-m-d");
			if ($hoy>=$fec_ini and $hoy<=$fec_fin){
				$tabla="
					<table border='1' align='center' width='95%'>		
						<tr>
							<th>Motivo</th>
							<th>Fecha de inicio</th>
							<th>Días</th>
							<th>Fecha de resolución</th>
						</tr>
						<tr>
							<td>".$item->motivo."</td>
							<td><center>".$item->fechaInicio."</center></td>
							<td><center>".$item->tiempoDias."</center></td>
							<td><center>".$item->fechaResolucion."</center></td>
						</tr>
					</table>
				";				
				return $tabla;
			}else{
				return FALSE;
			}
		}else{			
			return FALSE;
		}
	}
}
if (!function_exists('get_sanciones')) {
	function get_sanciones($id){
		$CI = & get_instance();		
		$CI->load->model('model_antecedente');
		
		$tabla="";
		$item=$CI->model_antecedente->get_sanciones($id);		
		if(!empty($item)){
			$tabla="
			<table border='1' align='center' width='95%'>		
					<tr>
						<th>Tipo de sanción</th>
						<th>Motivo</th>
						<th>Fecha de Inicio</th>
						<th>Días</th>
						<th>Fecha de Resolución</th>					
					</tr>
					";
			for($i=0;$i<count($item);$i++){
				if($item[$i]["tipoSancion"]=="suspencion"){
					$item[$i]["tipoSancion"]="suspensión";
				}				
				$tabla.="
					<tr>
						<td>".strtoupper($item[$i]["tipoSancion"])."</td>
						<td>".$item[$i]["motivo"]."</td>
						<td><center>".$item[$i]["fechaInicio"]."</center></td>
						<td><center>".$item[$i]["tiempoDias"]."</center></td>
						<td><center>".$item[$i]["fechaResolucion"]."</center></td>
					</tr>
				";
			}
			$tabla.="</table><br/>";
		}
		return $tabla;
	}
}
if (!function_exists('pass_strong')) {
	function pass_strong($password){
		if (!empty($password)){
			// Validate password strength
			$uppercase = preg_match('@[A-Z]@', $password);
			$lowercase = preg_match('@[a-z]@', $password);
			$number    = preg_match('@[0-9]@', $password);
			$specialChars = preg_match('@[\@\(\)\{\}\<\>\=\.\!\$\%\&\/\¿\?\+\*]@', $password);
			if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
				$dato["tipo"] = 0;
				$dato["msg"] = 'La contraseña debe de tener al menos 8 caracteres y debe de incluir Letras mayusculas, minusculas, numeros y caractares especiales.';
			}else{
				$dato["tipo"] = 1;
			}
		}else{
			$dato["tipo"] = 0;
			$dato["msg"] = "Password vacio";
		}
		return $dato;
	}
}
if (!function_exists('generate_pass')) {
	function generate_pass(){
		$charactersNu = '0123456789';
		$charactersLo = 'abcdefghijklmnopqrstuvwxyz';
		$charactersUp = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersEs = '@(){}<>=.!$%&/¿?+*';
		$randomString = '';
		
		$length = 1;
		$charactersLength = strlen($charactersEs);
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $charactersEs[rand(0, $charactersLength - 1)];
		}
		
		$length = 3;
		$charactersLength = strlen($charactersLo);
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $charactersLo[rand(0, $charactersLength - 1)];
		}
		
		$length = 2;
		$charactersLength = strlen($charactersNu);
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $charactersNu[rand(0, $charactersLength - 1)];
		}		
		
		$length = 2;
		$charactersLength = strlen($charactersUp);
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $charactersUp[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
	}
}
if (!function_exists('delete_file')) {
	function delete_file($route){		
		if(file_exists($route)){
			if( chmod($route, 0777) ) {
				if(@unlink($route)){
					$dato["tipo"]=1;
					$dato["msg"]="Archivo borrado correctamente";
				}else{
					$dato["tipo"]=0;
					$dato["msg"]="El Archivo no ha podido ser borrado";
				} 
			}else{
				$dato["tipo"]=0;
				$dato["msg"]="No se han podido actualizar los permisos del archivo";
			}
		}else{
			$dato["tipo"]=0;
			$dato["msg"]="Archivo no encontrado";
		}
		return $dato;
	}
}
if (!function_exists('updatePtjOP')) {
	function updatePtjOP($idop,$itemid,$tipo){
		$CI = & get_instance();		
		$CI->load->model('model_main');
		$opd=$CI->model_main->getOpDtail($idop,$itemid);
		// die("ss idop");
		if(!empty($opd)){
			$und_cut=$opd->und_cut;
			//Traigo toda la op con todos sus items
			$detail= $CI->model_main->getAOP($idop);
			
			if(!empty($detail)){
				// print_r($detail);
				$CI->load->library('table');
				$tmpl = array(
					'table_open' => '<table class="table color-table success-table table-striped table-hover">',
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
				$CI->table->set_template($tmpl);
				$CI->table->clear();
				$CI->table->set_heading('No.',"Proceso OP $opd->op Item $opd->item",'Meta','Producido','%','T. Avance');
				$i=1;
				//total de procesos
				$tp = count($detail);
				$pp = 100 / $tp;
				//Total avance gloabal
				$taglobla=0;
				$ita='';
				foreach ($detail as $item2){
					if ($ita != $item2->itemid){
						$unidEnd=0;
					}
					$producido=$CI->model_main->getSumProcess($idop,$item2->id,$itemid);
					$meta=$und_cut*$item2->measure;
					// Suma adicionales
					if($item2->SumAdd==1){
						$meta+=$item2->covers;
					}
					if($item2->SumAdd==2){
						$meta+=$item2->bedsheet;
					}
					
					if($meta>0){
						$pmeta=($producido/$meta*100);
					}else{
						$pmeta=0;
					}
					if($item2->measure==0){
						$pmeta=0;
					}
					if($item2->SumEnd==1){
						$unidEnd+=$producido;
						// if ($ita != $item2->itemid){
							// $ita=$item2->itemid;
							// if(isset($datoP[$idop][$ita])){
								// $datoP[$idop][$ita]+=$producido;
							// }else{
								// $datoP[$idop][$ita]=$producido;
							// }
						// }
					}
					
					$tmeta = ($pmeta * $pp) / 100;
					$taglobla+=$tmeta;
					if($pmeta > 100){
						$pmeta='<span style="background:red;color:#fff;padding:5px;">'.round($pmeta,2).'%</span>';
					}else{
						$pmeta=round($pmeta,2).'%';
					}
					$CI->table->add_row($i,$item2->process,$meta,$producido,$pmeta,round($tmeta,2).'%');
					$i++;
				}
				$data['tabla']=$CI->table->generate();
				// $data['taglobla']=$taglobla;
				
				if($tipo==1){
					$CI->model_main->updateOrderItem(
						array(
							"order"=>$idop,
							"item"=>$itemid,
							"PtjAdv"=>$taglobla,
							"und_produced"=>$unidEnd
						)
					);
				}
				return $data;
			}
		}
	}
}
