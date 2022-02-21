document.addEventListener('deviceready', onDeviceReady, false);
function onDeviceReady() {
// var db = window.sqlitePlugin.openDatabase({name: "aris.db", location: 'default'});
    // db.transaction(function(tx) {
		// tx.executeSql("DELETE FROM aris", [], function(tx, res){
			// console.log(res);
		// });
	// });
	// var db = window.sqlitePlugin.openDatabase({name: "aris.db", location: 'default'});
    // db.transaction(function(tx) {
        // tx.executeSql("CREATE TABLE IF NOT EXISTS aris (id integer primary key, ip text)", [], function(tx, res){
			// tx.executeSql("SELECT * FROM aris WHERE id = ?", [1], function(tx, res){
				// if (res.rows.length > 0) {
						// $("#ip").val(res.rows.item(0).ip);
						// storage.setItem('IpServer', res.rows.item(0).ip);
				// }else{
					// tx.executeSql("INSERT INTO aris VALUES (?,?)", [1,0], function(tx,res){
					// });
				// }
			// });
        // });
    // }, function(err){
        // alert("Error: " + err.message)

    // });
}
//---------------------------------------------------------------- Plugin sql
function guardaIP(){
	var key = $("#ip").val();
	storage.setItem('IpServer', key);
	$("#msgok").html('Registro actualizado');
	$(".successbottom").fadeToggle(350);	
}
function guardaIP1(){
	var key = $("#ip").val();
	var uagent = navigator.userAgent.toLowerCase();
	if (uagent.search("mobile") > -1){
		var db = window.sqlitePlugin.openDatabase({name: "aris.db", location: 'default'});
		db.transaction(function(tx) {
			tx.executeSql("UPDATE aris SET ip=? WHERE id=?", [key,1], function(tx,res){
				// console.log('Actualiza');
				// console.log(res.rowsAffected); 
				storage.setItem('IpServer', key);
				$("#msgok").html('Registro actualizado');
				$(".successbottom").fadeToggle(350);	
			});
		}, function(err){
			//errors for all transactions are reported here
			// alert("Error: " + err.message)
			$("#msgno").html('Error al actualizar' + err.messag);
			$(".alertbottom").show();
		});
	}else{
		storage.setItem('IpServer', key);
		$("#msgok").html('Registro actualizado');
		$(".successbottom").fadeToggle(350);	
	}
}
//---------------------------------------------------------------- Plugin toma de foto
function TakePicture () { 
	var options = {
        // Some common settings are 20, 50, and 100
        quality: 70,
        // destinationType: Camera.DestinationType.DATA_URL,
		destinationType: Camera.DestinationType.FILE_URI,
        // In this app, dynamically set the picture source, Camera or photo gallery
        sourceType: Camera.PictureSourceType.CAMERA,
        encodingType: Camera.EncodingType.PNG,
        mediaType: Camera.MediaType.PICTURE,
		targetWidth: 500,
		targetHeight:667
    }
    navigator.camera.getPicture(photoSuccess,photoFail,options);

}
function photoSuccess(imageData) {
	// var image = document.getElementById('photo2');
	// image.src = "data:image/png;base64," + imageData;
	var image = document.getElementById('photo2');
	image.src = imageData;
	upload(imageData);
}  

function photoFail(message) { 
  alert('Failed because: ' + message); 
}
//---------------------------------------------------------------- Plugin FILE TRANSFER
function upload(fileURL) {
	var uri = encodeURI(urlserver+"index.php/employee/uploadpicture/");
	var options = new FileUploadOptions();
	var headers={'Token':sessionStorage.getItem("mysession")};
	var ft = new FileTransfer();
	var progressValue = 0;
	
	options.fileKey="file1";
	options.fileName=fileURL.substr(fileURL.lastIndexOf('/')+1);
	options.mimeType="image/png";
	options.headers = headers;
	
	var params = {};
	params.cedfoto = $("#cedfoto").val();
	params.tipo = "M";
	options.params = params;
	
	// ft.onprogress = function(progressEvent) {
		// if (progressEvent.lengthComputable) {
			// progressValue = progressEvent.loaded / progressEvent.total;
		// } else {
			// progressValue++;
		// }
		// document.getElementByID('progress').innerHTML = progressValue;
	// };
	ft.upload(fileURL, uri, uploadSuccess, uploadfail, options);
}
function uploadSuccess(r) {
    // console.log("Code = " + r.responseCode);
    dato = JSON.parse(r.response);
    // console.log("Sent = " + r.bytesSent);
	if(dato.tipo){
		$("#msgok").html(dato.msg);
		$(".successbottom").fadeToggle(350);
		setTimeout(function(){
			$(".successbottom").fadeToggle(350);
		}, 5000);
		$('#photoModal').modal('toggle');
		var image = document.getElementById('photo2');
		image.src = "assets/images/noimage.png";
	}else{
		$("#msgno2").html(dato.msg);
		$(".alertbottom2").fadeToggle(350);
		setTimeout(function(){
			$(".alertbottom2").fadeToggle(350);
		}, 5000);
	}
}

function uploadfail(error) {
    alert("An error has occurred: Code = " + error.code);
    console.log("upload error source " + error.source);
    console.log("upload error target " + error.target);
}
//---------------------------------------------------------------- Plugin QR
function Escanear(tipo){
	//dato='https://aristextil.com/empleados/?e=eyJjdCI6InJvU1JjWVVYSWRHaDNBVElzOW9zdXJhXC9KYmtGV1VHXC84cERcL1l3TkJGNnVxckRQQWZvU2tsTGlXZ1BEeGVcL0xRIiwiaXYiOiI1NWQ4NThkOGZmMGYwMDIwMmExMzQyZDM1ZDliMGMxMSIsInMiOiJmZWFmOGMxNDU2NmE4OTkwIn0=';
	cordova.plugins.barcodeScanner.scan(
		function (result) {
			var dato = result.text;
			// var res = dato.split('&&');
			var res = dato.split('e=');
			var encrypted = atob(res[1]);
			var decrypted = CryptoJSAesJson.decrypt(encrypted, keyjs)
			var res = decrypted.split('&&');
		
			$("#employeess").val(res[0]);
			$("#employees").val(res[1]);
			$("#suggesstion-box").hide();
			if(tipo==1){
				ConsultaTareas();
			}
			if(tipo==2){
				registerAccess();
			}
		},
		function (error) {
			alert("Scanning failed: " + error);
		},
		{
			preferFrontCamera : false, // iOS and Android
			showFlipCameraButton : true, // iOS and Android
			showTorchButton : true, // iOS and Android
			torchOn: false, // Android, launch with the torch switched on (if available)
			saveHistory: true, // Android, save scan history (default false)
			prompt : "Por favor escanea el qr dentro del Área", // Android
			resultDisplayDuration: 500, // Android, display scanned text for X ms. 0 suppresses it entirely, default 1500
			formats : "QR_CODE,PDF_417", // default: all but PDF_417 and RSS_EXPANDED
			orientation : "portrait", // Android only (portrait|landscape), default unset so it rotates with the device
			disableAnimations : true, // iOS
			disableSuccessBeep: false // iOS and Android
		}
	);
}