var modulo='main';
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
		data: {},
		success: function (dato) {
			if(dato.tipo==-1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}else{
				$('#rp1').html(dato.Top);
				$('#rp2').html(dato.Tit);
				$('#rp3').html(dato.Tem);
				$('#rp4').html(dato.Trp);
				$('#pdp').html(dato.pdp);
				$('#aop').html(dato.aop);
				Morris.Donut({
					element: 'chart-estados',
					data: dato.ops,
					resize: true
				});
				Morris.Donut({
					element: 'chart-maquinas',
					data: dato.machines,
					resize: true
				});
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});	
});
function exporta(id){
	var data = new FormData();
	data.append('id', id);
	
	$.ajax({  
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/exportar/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},		
		dataType: "TEXT",
		data: "id="+id,
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
function detalleop (id,itemid){
	var data = new FormData();
	data.append('id', id);
	data.append('itemid', itemid);
	
	$(".opdetail").hide();
	if ($("#detalle"+id+itemid).is(":visible")){
		$("#detalle"+id+itemid).hide();
	}else{
		$("#detalle"+id+itemid).show();
	}
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/detalleop/",
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
				$("#detalle"+id+itemid).html(dato.tabla);
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			$("#msgno").html( 'Error de conexión:' + textStatus +' '+ errorThrown);
			$(".alertbottom").show();
		} 
	});
}