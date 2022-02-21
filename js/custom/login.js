$(document).ready(function () {
	var storage = window.localStorage;
	var user = storage.getItem('user');
	var pass = storage.getItem('pass');
	if(user!==''){
		$('#user').val(user);
		$('#pass').val(pass);
	}
});
$(".loginclick").click(function(event) {
	var user = $('#user').val();
	var pass = $('#pass').val();
	var captcha = $('#captcha').val();
	
	storage.setItem('user', user);
	storage.setItem('pass', pass);
	
	var data = new FormData();
	data.append('user', user);
	data.append('pass', pass);
	data.append('captcha', captcha);
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/login/access",
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo == 1){
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);				
				sessionStorage.mysession=dato.info;
				sess = jwt_decode(dato.info);
				sessionStorage.name=sess.info.name;
				sessionStorage.rol=sess.info.rol;
				setTimeout(function(){
					window.location="index.html"; 
				}, 1000);
			}else{
				var d = $("#msgno").html()
				$("#msgno").html( d + '<br>' + dato.msg);
				$(".alertbottom").show();
				// setTimeout(function(){
					// $(".alertbottom").fadeToggle(350);
				// }, 5000);
			}					
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		}   
	});
	return false;
});
$(".recoverclick").click(function(event) {
	var email = $('#email2').val();
	var captcha = $('#captcha2').val();	
	
	var data = new FormData();
	data.append('email', email);
	data.append('captcha', captcha);
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/login/recuperarcta",
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo == 1){
				// console.log("aa");
				// console.log(dato);
				$('#email').val("");
				$('#captcha').val("");
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(450);				
				$("#recoverform").slideUp();
				$("#loginform").fadeIn();
				setTimeout(function(){
					$(".successbottom").fadeToggle(450);
				}, 5000);
			}else{
				// console.log(dato);
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(450);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(450);
				}, 5000);
			}					
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
	return false;
});
function cerrarModal(){
	$(".alertbottom").hide();
	$("#msgno").html('');
}