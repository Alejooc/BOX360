var modulo='order';
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
				  return 	'<a href="#" onclick=formu("'+data[0]+'");return false;><i class="fas fa-pencil-alt text-info m-r-10"></i></a>'+
							'<a href="#" onclick=elim("'+data[0]+'");return false;><i class="fas fa-trash text-danger m-r-10"></i></a>' +
							'<a href="#" onclick=tareas("'+data[0]+'");return false;><i class="fas fa-users text-warning m-r-10"></i></a>'+
							'<a href="#" onclick=corte("'+data[0]+'");return false; data-bs-toggle="modal" data-bs-target="#cortesModal" data-whatever="@getbootstrap"><i class="fas fa-cut text-dark  m-r-10"></i></a>'+
							'<a href="#" onclick=clasificacion("'+data[0]+'");return false; data-bs-toggle="modal" data-bs-target="#clasificacionModal" data-whatever="@getbootstrap"><i class="fas fa-check text-success m-r-10"></i></a>'+
							'<a href="#" onclick=estado("'+data[0]+'");return false; data-bs-toggle="modal" data-bs-target="#estadoModal" data-whatever="@getbootstrap"><i class="fas fa-industry text-success"></i></a>';
				}
			}
		],
		"columns": [
			null,
			{ "data": 1 },
			{ "data": 2 },
			{ "data": 3 },
			{ "data": 4 },
			{ "data": 5 }
		],
		"order": [[ 1, "desc" ]]
	});
	$('.ColumFilter').on( 'keyup', function () {
		$("#busca").val('');
		tabla			
			.column( this.id.match(/\d+/) )
			.search( this.value )
			.draw();		
	});
	get_states();
});
function buscaet(){
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/autocomplete/employee",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		data:'keyword='+$('#employeesT').val(),
		success: function(data){
			$("#suggesstion-boxT").show();
			$("#suggesstion-boxT").html(data);
			$("#employeessT").css("background","#FFF");
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
function selectEmployeet(id,name){
	$("#employeessT").val(id);
	$("#employeesT").val(name);
	$("#suggesstion-boxT").hide();
}
function selectItem(id,name){
	$("#item").val(id);
	$("#itemn").val(name);
	$("#suggesstion-box-item").hide();
}
function Buscar(){
	$('.ColumFilter').val('');
	tabla.search('').columns().search('').draw();
	
	var b = $("#busca").val();
	tabla.search( b ).draw();
}
function cerrarmodal(){
	$('body').removeClass('modal-open');
	$('.modal-backdrop').remove();
}
function get_states(){
	var data = new FormData();
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_states/",
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
				//console.log(dato.tipo);
				$(".selectEstate").html(dato.estados);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
/*************INICIA CRUD************/
function formu(id){
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
				// myform.reset();
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				formu(dato.opid);
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
/*************INICIA CRUD DETALLE************/
function formu2(id,id2){
	if (id>0){
		var data = new FormData();
		data.append('id', id);
		data.append('id2', id2);		
		
		var urls = $(this).attr('href');
		$.ajax({
			type: "POST",
			url: urlserver+"index.php/"+modulo+"/get_form2/",
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
					// console.log(dato.datos);
					$("#id2").val(dato.datos.id);
					$("#orderid").val(dato.datos.order);
					$("#item").val(dato.datos.item);
					$("#rolls").val(dato.datos.rolls);
					$("#idp").val(dato.idp);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
				$(".alertbottom").show();
			} 
		});
	}else{
		$("#item").val('');
		$("#rolls").val('');
		$("#id2").val(0);
		$("#idp").val(id2);
	}
	$('#MyModalDetalle').modal('show');
}
function guardaFormInterno(){
	var myform = document.getElementById("forminterno1");
	var data = new FormData(myform );
	if ( $("#id2").val() > 0 ) {
		var type=2;
	}else{
		var type=1;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/form_send2/"+type,
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			$('#MyModalDetalle').modal('hide');
			cerrarmodal();
			if(dato.tipo){
				formu(dato.idp);
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
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
}
function elim2(id){
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
				url: urlserver+"index.php/"+modulo+"/del_form2/",
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
							// $('#index-table').DataTable().clear().draw();
							formu(dato.id);
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
// function tipos(id){
	// var data = new FormData();
	// data.append('id', id);	
	
	// var urls = $(this).attr('href');
	// $.ajax({
		// type: "POST",
		// url: urlserver+"index.php/"+modulo+"/get_form2/",
		// beforeSend: function(request){
			// request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		// },
		// contentType: false,
		// processData: false,
		// cahe: false,
		// dataType: "JSON",
		// data: data,
		// success: function (dato) {
			// if(dato.tipo==-1){
				// sessionStorage.mysession="";
				// sessionStorage.clear();
				// window.location="login.html"; 
			// }else{
				// $("#ppal").html(dato.msg);
				// $("#main").css("display","block");
				// $("#exampleModal").css("display","block");
				// $("#ppal").css("display","none");
			// }
		// }
	// });
// }
/*************INICIA CORTE************/
function corte(id){
	$('#idc').val(id);
	var data = new FormData();
	data.append('id', id);	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_corte/",
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
				$("#cortesData").html(dato.table);
				$("#itemlistcorte").html(dato.select);
				
			}else{
				//$('#cortesModal').modal('toggle')
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
function corteSave(){
	$('#cortesModal').modal('toggle')	
	var myform = document.getElementById("cortesForm");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/save_corte/"+type,
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
}	
/*************INICIA CALIDAD************/
function calidad(id){
	$('#idca').val(id);
	var data = new FormData();
	data.append('id', id);	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_calidad/",
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
				$("#calidadData").html(dato.table);
			}else{
				$('#calidadModal').modal('toggle')
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(350);
				});
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
function calidadSave(){
	$('#calidadModal').modal('toggle')	
	var myform = document.getElementById("calidadForm");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/save_calidad/"+type,
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
}
function del_calidad(id,id2){
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
			data.append('id2', id2);				
			
			var urls = $(this).attr('href');
			$.ajax({
				type: "POST",
				url: urlserver+"index.php/"+modulo+"/del_calidad/",
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
							calidad(dato.id);
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
/*************INICIA CLASIFICACION************/
function clasificacion(id){
	$('#idcl').val(id);
	var data = new FormData();
	data.append('id', id);	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_clasificacion/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {
			if(dato.tipo==1){
				$('#audit').val(dato.dato.audit);
				$('#type_a').val(dato.dato.auditA);
				$('#type_b').val(dato.dato.auditB);
				$('#type_c').val(dato.dato.auditC);
				// $("#clasificacionData").html(dato.table);
			}else{
				$('#clasificacionModal').modal('toggle')
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(350);
				});
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
function clasificacionSave(){
	$('#clasificacionModal').modal('toggle')	
	var myform = document.getElementById("clasificacionForm");
	var data = new FormData(myform );
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/save_clasificacion/"+type,
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
				myform.reset();
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
}
function del_clasificacion(id,id2){
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
			data.append('id2', id2);				
			
			var urls = $(this).attr('href');
			$.ajax({
				type: "POST",
				url: urlserver+"index.php/"+modulo+"/del_clasificacion/",
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
							clasificacion(dato.id);
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
/*************INICIA CAMBIO ESTADO************/
function estado(id){
	$("#estadoData").html('');
	$('#idEs').val(id);
	var data = new FormData();
	data.append('id', id);	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_estado/",
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
				$("#estadoData").html(dato.table);
			}else{
				$('#estadoModal').modal('toggle')
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(350);
				});
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}
function estadoSave(){
	$('#estadoModal').modal('toggle')	
	var myform = document.getElementById("estadoForm");
	var data = new FormData();
	var id = $('#idEs').val();
	var state = $('#state').val();
	data.append('idEs', id)
	data.append('state', state)
	
	if ( $( this ).hasClass( "accs" ) ) {
		var type=1;
	}else{
		var type=2;
	}
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/save_estado/"+type,
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
				myform.reset();
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
}
function del_estado(id,id2){
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
			data.append('id2', id2);				
			
			var urls = $(this).attr('href');
			$.ajax({
				type: "POST",
				url: urlserver+"index.php/"+modulo+"/del_estado/",
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
							clasificacion(dato.id);
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
function tareas(id,tipo=0,busca=''){
	var data = new FormData();
	data.append('id', id);
	data.append('tipo', tipo);
	data.append('busca', busca);
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/tareas/",
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
				$(".selectEmpleadoTask").html(dato.empleados);
				$(".selectProccessTask").html(dato.procesos);
				$(".selectItemTask").html(dato.items);
				
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
function BuscarTask(){
	var tipo = $("#buscaTipoTask").val();
	var busca = $("#buscaDatoTask").val();
	var id = $("#tareaid").val();
	tareas(id,tipo,busca);
}
function editTask(id,goal){
	$("#ordentarea").val(id);
	//$("#meta").val(goal);
}
function CreaTask(id){
	$("#ordentareac").val(id);
}
function saveTaskCreate(id){
	$('#taskCreateModal').modal('toggle')	
	var myform = document.getElementById("tareaCreateForm");
	var data = new FormData(myform );
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/saveTaskCreate/",
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
				myform.reset();
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
}
function saveTaskEdit(id,goal){
	$('#taskEditModal').modal('toggle')	
	var myform = document.getElementById("tareaEditForm");
	var data = new FormData(myform );
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/saveTaskEdit/",
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
				myform.reset();
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
}
function delTask(id){
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
				url: urlserver+"index.php/"+modulo+"/delTask/",
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
							var id2 = $("#tareaid").val();
							tareas(id2);
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