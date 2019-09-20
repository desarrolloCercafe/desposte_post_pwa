document.getElementById('btn_caba').addEventListener('click', function(){
	var objeto = {
		solicitud:"si"
	};

    var ajax = new XMLHttpRequest();
    ajax.open("GET","/data_offline?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            var json = ajax.responseText;
		console.log(json);
            localStorage.setItem('data_faltantes', json);
		window.location.href="../pwa/index.html";
        }
    }
    ajax.send();

});
