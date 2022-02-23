var modulo='client';
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
					return 	'<a href="#" onclick=formu("'+data[0]+'");><i class="fas fa-pencil-alt text-info m-r-10"></i></a>'+
							'<a href="#" onclick=elim("'+data[0]+'");><i class="fas fa-trash text-danger m-r-10"></i></a>'+
							'<a href="#" onclick=tjta("'+data[0]+'"); data-bs-toggle="modal" data-bs-target="#tjtatModal" data-whatever="@getbootstrap"><i class="fas fa-address-card text-success m-r-10"></i></a>'+
							'<a href="#" onclick=foto("'+data[0]+'"); data-bs-toggle="modal" data-bs-target="#photoModal" data-whatever="@getbootstrap"><i class="fas fa-image text-warning"></i></a>';
				}
			}
		],
		"columns": [
			null,
			{ "data": 0 },
			{ "data": 1 },
			{ "data": 2 },
			{ "data": 3 },
			{ "data": 4 }
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
function formu2(id,id2){
	console.log(id);
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
					 console.log(dato.datos);
					$("#id2").val(dato.datos.id);
					$("#destinyd").val(dato.datos.destinyd);
					$("#idp").val(dato.idp);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
				$(".alertbottom").show();
			} 
		});
	}else{
		$("#id2").val(0);
		$("#idp").val(id2);
	}
	$('#MyModalDetalle').modal('show');
}
function detailext(ev) {
	var p = ev.options[ev.selectedIndex];
	var itemVal= p.value
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/get_subscriptions/"+itemVal,
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: 'data',
		success: function (dato) {
			console.table(dato);
			$('#pland').html(`<h5>Total Accesos: ${dato.plans.access}</h5>`)
			$('#total').html(`<h5>Total a pagar: ${dato.plans.price}</h5>`)
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
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
function tjta(id,imp=1){
	$( ".carnetprint").html("Cargando...");
	var data = new FormData();
	data.append('id', id);
	data.append('imp', imp);
	
	var urls = $(this).attr('href');
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/tjta/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "text",
		data: data,
		success: function (txt) {		
			if(imp==1){
				$( ".carnetprint").html(txt);	
			}else{
				$( ".carnetprint").html('<iframe width="100%" height="500px" src="data:application/pdf;base64,' + txt + '"></object>');	
			}
		},
		error: function (err,err1){
			$( ".carnetprint").html(err1);
		}
	});
}
function foto(id){
	$("#cedfoto").val(id);
}
function uploadpicture(){
	var myform = document.getElementById("formfoto1");
	var data = new FormData(myform );
	data.append("tipo", "NM")
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/uploadpicture/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "JSON",
		data: data,
		success: function (dato) {	
			// console.log(dato);
			if(dato.tipo){
				$("#msgok").html(dato.msg);
				$(".successbottom").fadeToggle(350);
				setTimeout(function(){
					$(".successbottom").fadeToggle(350);
				}, 5000);
				myform.reset();
				$(".dropify-clear").trigger("click");
				$('#photoModal').modal('toggle');
			}else{
				$("#msgno2").html(dato.msg);
				$(".alertbottom2").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom2").fadeToggle(350);
				}, 5000);
			}
		},
		error: function (err,err1){
			$( ".carnetprint").html(err1);
		}
	});
}
function vermas(){
	if ($("#vermas").is(":visible")){
		$("#vermas").hide();
	}else{
		$("#vermas").show();
	}
}