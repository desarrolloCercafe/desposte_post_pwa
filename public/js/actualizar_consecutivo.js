document.getElementById('btn_update_consecutivo').addEventListener('click', function(){

	var input = document.getElementById('input_update_consecutivo');

	objeto = {
	consecutivo:input.value
	};

    var ajax = new XMLHttpRequest();
    ajax.open("GET","/update_consecutivo?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

		console.log("fljfglkj");
            //Informar que el consecutivo se actualizó correctamente

        }
    }
    ajax.send();

	input.value = "";
	document.getElementById('btn_cancelar_update').click();
	document.getElementById('textInfo').innerHTML = "Consecutivo generado con éxito";
	document.getElementById('DesplegarErrorModal').click();

});
