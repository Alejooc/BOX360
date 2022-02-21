var modulo='access';
var tabla
$( window ).on( "load", function() {
	if("mysession" in sessionStorage){		
		var sess = sessionStorage.getItem("mysession");
		sess = jwt_decode(sess);
		if(sess.info.isLoggedIn){
			$(".preloader").fadeOut();
		}else{
			window.location="login.html";
		}	
	}else{
		window.location="login.html";	
	}
	var uagent = navigator.userAgent.toLowerCase();
	if (uagent.search("mobile") > -1){
		 
	}else{
		$('#index-table thead th').each( function () {
			var title = $('#index-table tfoot td').eq( $(this).index() ).text();		
			if(title!=""){
				var act = $(this).html();
				$(this).html( '<input id="colum'+$(this).index()+'" style="width:100%;" class="ColumFilter" type="text" placeholder="Buscar" /><br>'+act );
			}
		});
	}
	$("tfoot").css("display","none");
	tabla=$('#index-table').DataTable({
		"sDom": '<"top"l>rt<"bottom"ip><"clear">',
		"language": {
			"url": "js/Spanish.json"
		},		
		"destroy": true,
		"processing": true,		
		"serverSide": true,
		"responsive": {
			"details": true
		},
		"pageLength" : 20,
		"ajax": {
			url : urlserver+"index.php/"+modulo+"/index/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			type : 'POST',
			'data': {
			   test: 'afscpMcn',
			},
			"dataSrc": function ( json ) {
               if(json.tipo==-1){
					sessionStorage.mysession="";
					sessionStorage.clear();
					window.location="login.html";
					return false;
				}else if(json.tipo==0){
					$("#msgno").html(json.msg);
					$(".alertbottom").fadeToggle(350);
					setTimeout(function(){
						$(".alertbottom").fadeToggle(350);
					}, 5000);
				}else{
					return json.data;
				}
            }			
		},				
		"columnDefs": [ 
			{
				"targets": 0,
				"data": null,
				"orderable": false,
				"render": function ( data, type, row, meta ) {
				  return '<a href="#" onclick=elim("'+data[0]+'");return false;><i class="fas fa-trash text-danger"></a>';
				}
			}
		],
		"columns": [
			null,
			{ "data": 1 },
			{ "data": 2 },
			{ "data": 3 }
		]
	});
	$('.ColumFilter').on( 'keyup', function () {
		$("#busca").val('');		
		tabla			
			.column( this.id.match(/\d+/) )
			.search( this.value )
			.draw();		
	});
});
function formu(id){
	// console.log('aaaaa');
	var data = new FormData();
	data.append('id', id);	
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_form/",
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
				$("#ppal").html(dato.msg);
				$("#main").css("display","none");
				$("#ppal").css("display","block");
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		}
	});
}
function Buscar(){
	$('.ColumFilter').val('');
	tabla.search('').columns().search('').draw();
	
	var b = $("#busca").val();
	tabla.search( b ).draw();
}
$(document).on("submit", "#forminterno", function (e) {
	e.preventDefault();									
	var formid = $(this).attr("id");
	
	var myform = document.getElementById("forminterno");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/form_send/"+type,
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
				$("#msgscan").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				myform.reset();
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
function elim(id){
	sessionStorage.delid=id;
	Swal.fire({   
		title: lang.deltitle,   
		text: lang.deltxt,   
		type: "warning",   
		showCancelButton: true,   
		confirmButtonColor: "#DD6B55",   
		confirmButtonText: lang.delyes,
		cancelButtonText: "Cancel",
	}).then((result) => {
		if (result.value) {
			var data = new FormData();
			data.append('id', id);				
			
			var urls = $(this).attr('href');
			$.ajax({
				type: "POST",
				url: urlserver+"index.php/"+modulo+"/del_form/",
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
							swal.fire(lang.delconf, dato.msg, "success");
							$('#index-table').DataTable().clear().draw();
						}else{								
							swal.fire(lang.delerr, dato.msg, "error");
						}
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
					$(".alertbottom").show();
				} 
			});
		}else{
			result.dismiss === Swal.DismissReason.cancel
		}
	});
}
//<!----------------------------------------------------->
function registerAccess(){
	var myform = document.getElementById("forminterno");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/form_send/"+type,
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
				$("#msgscan").html(dato.msg);
				
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				myform.reset();
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
}
function buscaEmpleado(){
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/autocomplete/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		data:'keyword='+$('#employees').val(),
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#employees").css("background","#FFF");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
function selectEmploye(id,name){
	$("#employeess").val(id);
	$("#employees").val(name);
	$("#suggesstion-box").hide();
}
function selectEmployeQR(){
	var dato=31480053+"&&MUÑOS MARLEN";
	var res = dato.split('&&');
	$("#employeess").val(res[0]);
	$("#employees").val(res[1]);
	$("#suggesstion-box").hide();
}
function buscaDepartamento(){
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/autocomplete/depar",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		data:'keyword='+$('#depar').val(),
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#employees").css("background","#FFF");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
function selectDepar(id,name){
	$("#deparid").val(id);
	$("#depar").val(name);
	$("#suggesstion-box").hide();
}
function buscarr(){
	var data = new FormData();
	var fechai = $('#fechai').val();
	var fechaf = $('#fechaf').val();
	var deparid = $('#deparid').val();
	var hcompara = $('#hcompara').val();

	data.append('fechai', fechai);
	data.append('fechaf', fechaf);
	data.append('deparid', deparid);
	data.append('hcompara', hcompara);
	data.append('expo', 0);	
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/buscarr/",
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
					$("#tablar").html(dato.tablar);
				}else{
					$("#msgno").html(dato.msg);
					$(".alertbottom").fadeToggle(350);
					setTimeout(function(){
						$(".alertbottom").fadeToggle(350);
					}, 5000);
				}
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		}
	});		
}
function exportar(){
	var data = new FormData();
	var fechai = $('#fechai').val();
	var fechaf = $('#fechaf').val();
	var deparid = $('#deparid').val();
	var hcompara = $('#hcompara').val();

	data.append('fechai', fechai);
	data.append('fechaf', fechaf);
	data.append('deparid', deparid);
	data.append('hcompara', hcompara);
	data.append('expo', 1);	
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/buscarr/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "TEXT",
		data: data,
		success: function (csv) {
			if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > 0) {
				var IEwindow = window.open("", "", "Width=0px; Height=0px");
				IEwindow.document.write('sep=,\r\n' + csv);
				IEwindow.document.close();
				IEwindow.document.execCommand('SaveAs', true, "reporte.xls");
				IEwindow.close();
			}
			else { 
				var aLink = document.createElement('a');
				var evt = document.createEvent("MouseEvents");
				evt.initMouseEvent("click", true, true, window,
					0, 0, 0, 0, 0, false, false, false, false, 0, null);
				aLink.download = "reporte.xls";
				aLink.href = 'data:application/ms-excel;charset=UTF-8,' + encodeURIComponent(csv);
				aLink.dispatchEvent(evt);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		}
	});		
}