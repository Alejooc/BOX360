var modulo='supplydelivery';
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
				  return 	'<a href="#" onclick=formu("'+data[0]+'");return false;><i class="fas fa-pencil-alt text-info m-r-10"></i></a>'+
							'<a href="#" onclick=elim("'+data[0]+'");return false;><i class="fas fa-trash text-danger m-r-10"></i></a>'+
							'<a href="#" onclick=pdf("'+data[0]+'"); data-bs-toggle="modal" data-bs-target="#printModal" data-whatever="@getbootstrap"><i class="fas fa-file-alt text-success"></i></a>'
				}
			}
		],
		"columns": [
			null,
			{ "data": 1 },
			{ "data": 2 },
			{ "data": 3 },
			{ "data": 4 },
			
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
function pdf(id){
	$( ".areaprint").html("Cargando...");
	var data = new FormData();
	data.append('id', id);
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/pdf/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		contentType: false,
		processData: false,
		cahe: false,
		dataType: "text",
		data: data,
		success: function (txt) {		
			$( ".areaprint").html('<iframe width="100%" height="500px" src="data:application/pdf;base64,' + txt + '"></object>');	
		},
		error: function (err,err1){
			$( ".areaprint").html(err1);
		}
	});
}
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
		}
	});
}
function getItemsOp(){
	var op = $("#op").val();
	var data = new FormData();
	data.append('op', op);
			
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/getItemsOp/",
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
				$('#itemfd').html(dato.items);
			}else{
				$("#msgno").html(dato.msg);
				$(".alertbottom").fadeToggle(350);
				setTimeout(function(){
					$(".alertbottom").fadeToggle(350);
				}, 5000);
			}	
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
				}
			});
		}else{
			result.dismiss === Swal.DismissReason.cancel
		}
	});
}

function formu2(id,id2){
	$("#deliverydid").val(id2);
	if (id>0){
		var data = new FormData();
		data.append('id', id);
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
					$("#supply").val(dato.datos.supply);
					$("#supplyn").val(dato.datos.supplyn);
					$("#qty").val(dato.datos.qty);
					formu(dato.deliverydid);
				}
			}
		});
	}else{
		$("#id2").val(0);
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
			if(dato.tipo){
				formu(dato.deliverydid);
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
		}
	});
	return false;			
}
function buscainsumo(){
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/autocomplete/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},
		data:'keyword='+$('#supplyn').val(),
		success: function(data){
			$("#suggesstion-box").show();
			$("#suggesstion-box").html(data);
			$("#suppliesss").css("background","#FFF");
		}
	});
}
function selectInsumo(id,name){
	$("#supply").val(id);
	$("#supplyn").val(name);
	$("#suggesstion-box").hide();
}