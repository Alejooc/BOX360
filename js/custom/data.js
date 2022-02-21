var modulo='data';
$( window ).on( "load", function() {
	inicio();
});
function inicio(){
	var data = new FormData();
	data.append('id', 1);	
	
	
	var urls = $(this).attr('href');
	// var uagent = navigator.userAgent.toLowerCase();
	// if (uagent.search("mobile") > -1){
		 
	// }else{
		// $('#index-table thead th').each( function () {
			// var title = $('#index-table tfoot td').eq( $(this).index() ).text();		
			// if(title!=""){
				// var act = $(this).html();
				// $(this).html( '<input id="colum'+$(this).index()+'" style="width:100%;" class="ColumFilter" type="text" placeholder="Buscar" /><br>'+act );
			// }
		// });
	// }
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
		data: data,
		success: function (dato) {
			if(dato.tipo==-1){
				sessionStorage.mysession="";
				sessionStorage.clear();
				window.location="login.html"; 
			}else{
				// alert(dato.dato1);
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
function reporte(id,tipo=1){
	var data = new FormData();
	data.append('id', id);
	data.append('tipo', tipo);
	
	$.ajax({
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/reporte/",
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
function Buscar(){
	$('.ColumFilter').val('');
	tabla.search('').columns().search('').draw();
	var b = $("#busca").val();
	tabla.search( b ).draw();
}
function filtro4(){
	var filtro = $("#filtro").val();
	reporte(4,filtro);
}
function exportar(id){
	var data = new FormData();
	var tipo = $("#filtro").val();
	data.append('id', id);
	data.append('tipo', tipo);
	
	$.ajax({  
		type: "POST",
		url: urlserver+"index.php/"+modulo+"/exportar/",
		beforeSend: function(request){
			request.setRequestHeader('Token', sessionStorage.getItem("mysession"));
		},		
		dataType: "TEXT",
		data: "id="+id +'&' + "tipo="+tipo,
		
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
		}
	});
}