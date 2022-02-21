var modulo='report';
var tabla
$(document).ready(function () {
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
	
	$("tfoot").css("display","none");
	$("#main").css("display","block");
	$("#ppal").css("display","none");
	$('.ColumFilter').on( 'keyup', function () {
		$("#busca").val('');
		tabla			
			.column( this.id.match(/\d+/) )
			.search( this.value )
			.draw();	
			
	});
	$('#index-table thead th').each( function () {
		var title = $('#index-table tfoot td').eq( $(this).index() ).text();		
		if(title!=""){
			var act = $(this).html();
			$(this).html( '<input id="colum'+$(this).index()+'" style="width:100%;" class="ColumFilter" type="text" placeholder="Buscar" /><br>'+act );
		}
	});
	$("#employees").keyup(function(){
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/"+modulo+"/autocomplete/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			data:'keyword='+$(this).val()+'&op='+$('#op').val()+'&item='+$('#item').val(),
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
	});
	ordenes();
});
function formu(id){
	$('#taskide').val(id);
	var data = new FormData();
	data.append('id', id);
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/find/",
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
				$('#cantidade').val(dato.cant);				
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	})
}
function edicionSave(){
	var id = $('#taskide').val();
	var cant = $('#cantidade').val();
	var data = new FormData();
	data.append('id', id);
	data.append('icantd', cant);
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/edicionSave/",
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
				$('#edicionModal').modal('toggle');
				$('#cantidade').val('');
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);	
				reportes();
			}else{
				$(".msgno2").html(dato.msg);
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
				myform.reset();
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				redraw();
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
function Buscar(){
	$('.ColumFilter').val('');
	tabla.search('').columns().search('').draw();
	
	var b = $("#busca").val();
	tabla.search( b ).draw();
}
function reportes(){
	var uagent = navigator.userAgent.toLowerCase();
	if (uagent.search("mobile") > -1){
		 
	}else{
		
	}
	$('#btnO').removeClass('btn-warning');
	$('#btnO').addClass('btn-outline-warning');
	$('#btnR').removeClass('btn-outline-warning');
	$('#btnR').addClass('btn-warning');
	
	$('#idca').val('aaa');
	$("#main").css("display","block");
	$("#ppal").css("display","none");
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
			url : urlserver+"index.php/"+modulo+"/reportes/",
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
				  return 	'<a href="#" onclick=formu("'+data[0]+'");return false; data-bs-toggle="modal" data-bs-target="#edicionModal" data-whatever="@getbootstrap"><i class="fas fa-pencil-alt text-info m-r-10"></i></a>'+
							'<a href="#" onclick=elim("'+data[0]+'");return false;><i class="fas fa-trash text-danger m-r-10"></i>	</a>'+
							'<a href="#" onclick=ok("'+data[0]+'");return false; data-bs-toggle="modal" data-bs-target="#calidadModal" data-whatever="@getbootstrap"><i class="fas fa-check text-success"></i></a>';
				}
			}
		],
		"columns": [
			null,
			{ "data": 1 },
			{ "data": 2 },
			{ "data": 3 },
			{ "data": 4 },
			{ "data": 5 },
			{ "data": 6 },
		],
		"order": [[ 1, "desc" ]]
		
	});
	
}
function ordenes(){
	$('#btnR').removeClass('btn-warning');
	$('#btnR').addClass('btn-outline-warning');
	$('#btnO').removeClass('btn-outline-warning');
	$('#btnO').addClass('btn-warning');
	
	// var urls = $(this).attr('href');
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/"+modulo+"/index/",
			beforeSend: function(request){
				request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
			},
			contentType: false,
			processData: false,
			cahe: false,
			dataType: "JSON",

			success: function (dato) {
				if(dato.tipo==-1){
					sessionStorage.mysession="";
					sessionStorage.clear();
					window.location="login.html"; 
				}else{
					$("#ppal").html(dato.table);
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

function saveReport(){
	$('#btnReport').prop('disabled', true);
	 
	var myform = document.getElementById("cantidadForm");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/saveReport/"+type,
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			$('#btnReport').prop('disabled', false);
			if(dato.tipo){
				$('#reporteModal').modal('toggle');
				myform.reset();
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
					ordenes();
				}, 5000);	
			}else{
				$(".msgno2").html(dato.msg);
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
}
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
function ok(id){
	$('#taskid').val(id);
	var data = new FormData();
	data.append('id', id);
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/find/",
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
				$('#conforme').val(dato.ok);
				$('#noconforme').val(dato.nok);				
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	})
}
function calidadTSave(){
	var myform = document.getElementById("calidadForm");
	var data = new FormData(myform );
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/save_calidadr/",
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
				$('#calidadModal').modal('toggle')	
				$("#msgok").html(dato.msg);
				myform.reset();
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				redraw();
			}else{
				$(".msgno2").html(dato.msg);
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
}
function itemsmodal(op,item){
	$('#op').val(op);
	$('#item').val(item);
	$("#employeess").val('');
	$("#employees").val('');
	$("#cant").val('');
	$("#tareaList").html('');
}
function selectEmployeQR(){
	var dato=31480053+"&&MUÑOS MARLEN";
	var res = dato.split('&&');
	$("#employeess").val(res[0]);
	$("#employees").val(res[1]);
	$("#suggesstion-box").hide();
	ConsultaTareas();
}
function selectEmploye(id,name){
	$("#employeess").val(id);
	$("#employees").val(name);
	$("#suggesstion-box").hide();
	ConsultaTareas();
}
function ConsultaTareas(){
	var op = $('#op').val();
	var item = $('#item').val();
	var employee = $("#employeess").val();
	
	var data = new FormData();
	data.append('op', op);
	data.append('item', item);
	data.append('employee', employee);
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/ConsultaTareas/",
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
					// console.log(dato);
					$("#tareaList").html(dato.tareas);
				}else{								
					$("#msgno2").html(dato.msg);
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