var storage = window.localStorage;
// storage.removeItem('IpServer');
var IpServer = storage.getItem('IpServer');
if(IpServer==null){
	// storage.setItem('IpServer', 'http://192.168.0.225');
	storage.setItem('IpServer', 'http://localhost');
}
// var urlserver="http://192.168.0.252/planta/serverback/";
var urlserver = storage.getItem('IpServer')+'/serverback/';

var NewYear = new Date().getFullYear();
var lang = { 
	"deltitle":"Estas seguro de eliminar este registro?",
	"deltxt":"Una vez eliminado no podras recuperarlo",
	"delyes":"Si, borrarlo!",
	"delconf":"Borrado!",
	"delerr":"Error!"
};
function cerrarmodal(){
	$('body').removeClass('modal-open');
	$('.modal-backdrop').remove();
}
function getMenu() {
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/main/getMenu/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: "",
		success: function (dato) {
			if(dato.tipo==1){
				$("#mainMenu").html(dato.menu);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
/*
var langdate = {
    days: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
    daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
    daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
    months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
    monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    today: "Hoy",
    clear: "Limpiar",
    format: "yyyy-mm-dd",
    titleFormat: "MM yyyy",
    weekStart: 1	
};
var langfile = {
	default: 'Arrastra y suelta un archivo o dale click aqui',
	replace: 'Arrastra y suelta un archivo o dale click para reemplazar',
	remove: 'Quitar',
	error: 'Ha sucedido un error.'
}
var langfilerr = {
	'fileSize': 'El archivo es demasiado grande maximo ({{ value }} MB).',					
	'imageFormat': 'Formato no permitido, solo ({{ value }}).',
	'fileExtension': 'Formato no permitido, solo ({{ value }}).'
}
*/
function expandir(id){
	if ( !$("#itemmenu"+id+" ul").hasClass("in")) {
		$("#itemmenu"+id+" ul").addClass('in');
	}else{
		$("#itemmenu"+id+" ul").removeClass('in');
	}
	if ( !$("#itemmenu"+id+" a").hasClass("active")) {
		$("#itemmenu"+id+" a").addClass('active');
	}else{
		$("#itemmenu"+id+" a").removeClass('active');
	}
}
var body = $("body");
$(document).ready(function () {
	getMenu();
});
$( window ).on( "load", function() {
	
	// console.log(sessionStorage.getItem("name"));
	$("#NewYear").html(NewYear);
	$("#username5").html(sessionStorage.getItem("name"));
	$(".validaalphanumerico").keypress(function(e) {
		var txt = String.fromCharCode(e.which);
        // console.log(txt + ' : ' + e.which);
		if(!txt.match(/[A-Za-z0-9]/)){
			return false;
		}
	});
	$(".validanumero").keypress(function(e) {
		var txt = String.fromCharCode(e.which);
        // console.log(txt + ' : ' + e.which);
		if(!txt.match(/[0-9]/)){
			return false;
		}
	});
	$(".validaespecial").keypress(function(e) {
		var txt = String.fromCharCode(e.which);
        // console.log(txt + ' : ' + e.which);
		if(!txt.match(/[A-Za-z0-9@#()}{<>=.!$%&/¿?+*]/)){
			return false;
		}
	});
	
});
function salir(){
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/login/salir/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: "",
		success: function (dato) {
			if(dato.tipo==1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
	//return false;
}
function recargar() {
	location.reload();
}
$(document).on("submit", "#formuprof5", function (e) {
	e.preventDefault();									
	var formid = $(this).attr("id");
	
	var myform = document.getElementById("formuprof5");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/profesional/form_send/"+type,
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo){
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				cerrarprof();
			}else{
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(350);
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
function redraw(){
	$("#main").css("display","block");
	$('#index-table').DataTable().clear().draw();
	$("#ppal").html("");
	$("#ppal").css("display","none");
}
/*
function get_menu(){
	var data = new FormData();	
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/login/get_menu/"+sess.sid,
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: "",
		success: function (dato) {
			if(dato.tipo==-1){				
			}else{
				$("#menuppal").html(dato.msg);				
			}
		}
	});
}
function sendFile(file, editor) {					
	data = new FormData();
	data.append("file", file);
	$.ajax({
		data: data,
		type: "POST",
		url: urlserver+"index.php/main/sendFile/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		cache: false,
		contentType: false,
		processData: false,
		dataType: "JSON",
		success: function(dato) {			
			if(dato.tipo==-1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}else{				
				if(dato.tipo==1){										
					$(editor).summernote('insertImage', urlserver+'assets/emailimg/'+dato.msg);
				}else{					
					$("#msgno").html(dato.msg);
					$(".alertbottom").fadeToggle(350);
					setTimeout(function(){
						$(".alertbottom").fadeToggle(350);
					}, 5000);
				}
			}
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.log(jqXHR);
		}
	});
}

$(".myadmin-alert-click").click(function(event) {
	$(this).fadeToggle(350);
	return false;
});

function imprimir(id){
	var divToPrint=document.getElementById(id);
	var newWin=window.open('','Imprimir');
	newWin.document.open();
	newWin.document.write('<html><body>'+divToPrint.innerHTML+'</body></html>');
	newWin.document.close();	
}
function selectprod(id,nom){	
	$("#nombrer").html(nom);
	$("#product").val(id);
	$("#prod").val("");	
	$("#resultadoprod").html("");
}
function selectprod2(id,val1,nom){	
	$("#nombrer").html(nom);
	$("#product").val(id);
	$("#val").val(val1);
	$("#prod").val("");	
	$("#resultadoprod").html("");
}
function buscarprod(){
	var data = new FormData();
	
	var prod = $("#prod").val();
	var cat = $("#cat").val();
	var mod = $("#mod").val();	
	
	if(prod!=""){
		data.append('prod', prod);
		data.append('cat', cat);
		data.append('mod', mod);		
				
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/main/buscarprod/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			contentType: false,
			processData: false,
			cahe: false,
			dataType: "JSON",
			data: data,
			success: function (dato) {
				if(dato.tipo==-1){
					sessionStorage.mysession="";
					sessionStorage.clear();
					window.location="login.html"; 
				}else{
					$("#resultadoprod").html(dato.msg);				
				}
			}
		});
	}
}
function buscarprod2(){
	var data = new FormData();
	
	var prod = $("#prod").val();
	var cat = $("#cat").val();
	var mod = $("#mod").val();
	var store = $("#store").val();
	
	if(store>0){
		data.append('prod', prod);
		data.append('cat', cat);
		data.append('mod', mod);
		data.append('store', store);
				
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/main/buscarprod2/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			contentType: false,
			processData: false,
			cahe: false,
			dataType: "JSON",
			data: data,
			success: function (dato) {
				if(dato.tipo==-1){
					sessionStorage.mysession="";
					sessionStorage.clear();
					window.location="login.html"; 
				}else{
					$("#resultadoprod").html(dato.msg);				
				}
			}
		});
	}
}
function selectcte(id,nom,dir,st,cy){	
	$("#nombrec").html(nom);
	$("#client").val(id);
	$("#address").val(dir);
	$("#state").val(st);
	$("#city").val(cy);
	$("#custumer").val("");	
	$("#resultadocte").html("");
}
function buscarcte(){
	var data = new FormData();
	
	var custumer = $("#custumer").val();
	
	if(custumer!=""){
		data.append('custumer', custumer);
				
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/main/buscarcte/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			contentType: false,
			processData: false,
			cahe: false,
			dataType: "JSON",
			data: data,
			success: function (dato) {
				if(dato.tipo==-1){
					sessionStorage.mysession="";
					sessionStorage.clear();
					window.location="login.html"; 
				}else{
					$("#resultadocte").html(dato.msg);				
				}
			}
		});
	}
}
$(document).on("submit", "#formuprof5", function (e) {
	e.preventDefault();									
	var formid = $(this).attr("id");
	
	var myform = document.getElementById("formuprof5");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/profesional/form_send/"+type,
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo){
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				cerrarprof();
			}else{
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(350);
				}, 5000);
			}	
		}
	});
	return false;			
});
function searchoperator(id,operador){
	var idc = document.getElementById(operador);
	if (idc){
		for (var i=idc.options.length; i-->0;){
			idc.options[i] = null;
		}	
	}
	var data = new FormData();
	data.append('id', id);
	data.append('operador', operador);
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/main/searchoperator/", 
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo==-1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}else{
				if(dato.tipo){					
					$('#'+dato.idd).append($('<option>', {
						value: 0,
						text: 'Seleccione'
					}));
					var carr=dato.det;
					for(i=0;i<carr.length;i++){
						$('#'+dato.idd).append($('<option>', {
							value: carr[i].id,
							text: carr[i].name
						}));
					}
				}
			}
		}
	});	
}
function searchclient(id,client){
	var idc = document.getElementById(client);
	if (idc){
		for (var i=idc.options.length; i-->0;){
			idc.options[i] = null;
		}	
	}
	var data = new FormData();
	data.append('id', id);
	data.append('client', client);
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/main/searchclient/", 
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo==-1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}else{
				if(dato.tipo){
					$('#'+dato.idd).append($('<option>', {
						value: 0,
						text: 'Seleccione'
					}));
					var carr=dato.det;
					for(i=0;i<carr.length;i++){
						$('#'+dato.idd).append($('<option>', {
							value: carr[i].id,
							text: carr[i].name
						}));
					}
				}
			}
		}
	});	
}

function searchparte(id,marca){
	var idc = document.getElementById(marca);
	if (idc){
		for (var i=idc.options.length; i-->0;){
			idc.options[i] = null;
		}	
	}
	var data = new FormData();
	data.append('id', id);
	data.append('marca', marca);
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/main/searchparte/", 
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo==-1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}else{
				if(dato.tipo){
					$('#'+dato.idd).append($('<option>', {
						value: 0,
						text: 'Seleccione'
					}));
					var carr=dato.det;
					for(i=0;i<carr.length;i++){
						$('#'+dato.idd).append($('<option>', {
							value: carr[i].id,
							text: carr[i].name
						}));
					}
				}
			}
		}
	});	
}
$(".right-side-toggle").on("click", function () {
	$(".right-sidebar").slideDown(50).toggleClass("shw-rside");
	$(".fxhdr").on("click", function () {
		body.toggleClass("fix-header"); 
	});
	$(".fxsdr").on("click", function () {
		body.toggleClass("fix-sidebar"); 
	});

	var fxhdr = $('.fxhdr');
	if (body.hasClass("fix-header")) {
		fxhdr.attr('checked', true);
	} else {
		fxhdr.attr('checked', false);
	}
	if (body.hasClass("fix-sidebar")) {
		fxhdr.attr('checked', true);
	} else {
		fxhdr.attr('checked', false);
	}
});


$(function () {
	$('#to-login').on("click", function () {
        $("#recoverform").slideUp();
        $("#loginform").fadeIn();
    });
	var set = function () {
			var topOffset = 60,
				width = (window.innerWidth > 0) ? window.innerWidth : this.screen.width,
				height = ((window.innerHeight > 0) ? window.innerHeight : this.screen.height) - 1;
			if (width < 768) {
				$('div.navbar-collapse').addClass('collapse');
				topOffset = 100; 
			} else {
				$('div.navbar-collapse').removeClass('collapse');
			}


			if (width < 1170) {
				body.addClass('content-wrapper');
				$(".open-close i").removeClass('icon-arrow-left-circle');
				$(".sidebar-nav, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
				$(".logo span").hide();
			} else {
				body.removeClass('content-wrapper');
				$(".open-close i").addClass('icon-arrow-left-circle');
				$(".logo span").show();
			}

			height = height - topOffset;
			if (height < 1) {
				height = 1;
			}
			if (height > topOffset) {
				$("#page-wrapper").css("min-height", (height) + "px");
			}
		},
		url = window.location,
		element = $('ul.nav a').filter(function () {
			return this.href === url || url.href.indexOf(this.href) === 0;
		}).addClass('active').parent().parent().addClass('in').parent();
	if (element.is('li')) {
		element.addClass('active');
	}
	$(window).ready(set);
	$(window).on("resize", set);
});


$(".open-close").on('click', function () {
	if ($("body").hasClass("content-wrapper")) {
		$("body").trigger("resize");
		$(".sidebar-nav, .slimScrollDiv").css("overflow", "hidden").parent().css("overflow", "visible");
		$("body").removeClass("content-wrapper");
		$(".open-close i").addClass("icon-arrow-left-circle");
		$(".logo span").show();
	} else {
		$("body").trigger("resize");
		$(".sidebar-nav, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
		$("body").addClass("content-wrapper");
		$(".open-close i").removeClass("icon-arrow-left-circle");
		$(".logo span").hide();
	}
});


$('.slimscrollright').slimScroll({
	height: '100%',
	position: 'right',
	size: "5px",
	color: '#dcdcdc'
});
$('.slimscrollsidebar').slimScroll({
	height: '100%',
	position: 'right',
	size: "5px",
	railVisible: true,
	color: '#dcdcdc'
});
$('.chat-list').slimScroll({
	height: '100%',
	position: 'right',
	size: "5px",
	color: '#dcdcdc'
});

body.trigger("resize");

$('.visited li a').on("click", function (e) {
	$('.visited li').removeClass('active');
	var $parent = $(this).parent();
	if (!$parent.hasClass('active')) {
		$parent.addClass('active');
	}
	e.preventDefault();
});
$(".navbar-toggle").on("click", function () {
	$(".navbar-toggle i").toggleClass("ti-menu").addClass("ti-close");
});
*/