window.onload=verificar_pedidos_offline;

(
  () => {
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', () =>
        navigator.serviceWorker.register('./sw.js')
          .then(registration => console.log('Service Worker registered'))
          .catch(err => 'SW registration failed'));
    }
  }
)();

function verificar_pedidos_offline(){
	if(localStorage.getItem("data_faltantes")){
		var data = JSON.parse(localStorage.getItem("data_faltantes"));
		var pedidos = data[0];
		var descripcion_pedidos = data[1];
	}

	rellenarTablaPedidos(pedidos);

        $(document).ready(function() {
            $('#generarBorde').DataTable( {
                "pagingType": "full_numbers",
		"order":[[ 0, "desc"]]
            } );
        } );

}

function rellenarTablaPedidos(objeto){
	var containerId = document.getElementById('BodyPedidosOffline');
	var number = 1;
	objeto.forEach(function(pedido){
		var content = `
		<tr id="pedido${number}">
			<th scope="row">
                                <button type="button" id="botonHistorial2${number}" data-toggle="modal"
                                  data-target=".bd-modal-example-xl" value="${pedido.id}" onclick="SendQuery_offline(this)">
                                     <p style="color: rgb(223, 1, 1);"><b>PD${pedido.id}</b></p>
                                </button>
			</th>
			<td id="fecha${pedido.id}">${pedido.fechaEntrega}</td>
			<td id="razon${pedido.id}">${pedido.razonSocial}</td>
			${pedido.estado == 2 ?
				`
				<style>
					#pedido${number}{
						background: #63f87c;
					}
				</style>
				<td>Alistado</td>
				`:`
				<td>Por listar</td>
				`
			}
		</tr>`;
		number++;
		containerId.innerHTML += content;
	});

}

function SendQuery_offline(pedido){

	var id = pedido.value;
	var concurrencia = 0;

	if(localStorage.getItem('editados')){

        	var editados = JSON.parse(localStorage.getItem('editados'));
	        var cant_despachadas = [];

        	editados.forEach(function (editado){
	                if(editado.pedido == id){
                        	cant_despachadas = editado.despachado;
                	        concurrencia = 1;
        	        }
	        });

	}

	var data_productos = JSON.parse(localStorage.getItem("data_faltantes"));
	var productos = data_productos[1];

	data = [];

	productos.forEach(function (producto){
		if(producto.codPedido == id){
			data.push(producto);
		}
	});

	document.getElementById('consecutivoPedido').value = id;

	var container = document.getElementById('tablaProductos_offline');
	container.innerHTML = "";
	number = 0;
	data.forEach(function(producto, index){
		if(concurrencia == 0){
	                content = `
                	        <tr>
        	                        <td id="nombre${number}">${producto.nombre}</td>
	                                <td id="cantidad${number}">${producto.cantidadSolicitada}</td>
                        	        <td id="unidad${number}">${producto.unidadMedida}</td>
                	                <td><input type="number" id="input${number}" step="0.01" class="form-control input_producto" min="0"></td>
        	                </tr>
	                `;

		}else{
        	        content = `
	                        <tr>
                                	<td id="nombre${number}">${producto.nombre}</td>
                        	        <td id="cantidad${number}">${producto.cantidadSolicitada}</td>
                	                <td id="unidad${number}">${producto.unidadMedida}</td>
        	                        <td><input type="number" id="input${number}" step="0.01" class="form-control input_producto" min="0" value="${cant_despachadas[index]}"></td>
        	                </tr>
	                `;

		}

		number++;
		container.innerHTML += content;
	});

	document.getElementById('cantidadFilas').value = number;

}

document.getElementById('ProductoAlistado').addEventListener('click', function(){

        var info_esencial = document.getElementsByClassName('input_producto');
        var array = [];
	var error = 0;
        var data = [].map.call(info_esencial, function(valor_input){
		if(valor_input.value == ""){
			document.getElementById('textInfo').innerHTML = "No pueden existir campos vacíos";
			document.getElementById('DesplegarErrorModal').click();
			error = 1;
		}else{
			return valor_input.value;
		}

        }).join('|');

	array = data.split('|');

	if(error == 0){

		var consecutivo = document.getElementById('consecutivoPedido');
		var concurrencia = 0;

		if(localStorage.getItem('editados')){

			var datos_editados = JSON.parse(localStorage.getItem('editados'));

			datos_editados.forEach(function (editado){
	                        if(editado.pedido == consecutivo.value){
                        	        editado.despachado = array;
                	                concurrencia = 1;
        	                }
	                });
		}

		if(concurrencia == 0){

			var editados = [];
			var cFilas = document.getElementById('cantidadFilas').value;
			elementos = [];

			for(var i=0; i<cFilas; i++){
				nombre = document.getElementById('nombre'+i).innerHTML;
				elementos.push(nombre);
			}

	                var objeto = {
                	        pedido: consecutivo.value,
        	                despachado:array,
				productos:elementos
	                };

                	if(localStorage.editados){
        	                editados = JSON.parse(localStorage.getItem('editados'));
	                }

                	editados.push(objeto);

        	        localStorage.setItem('editados', JSON.stringify(editados));

	                var fecha = document.getElementById('fecha'+consecutivo.value);
                	var razon = document.getElementById('razon'+consecutivo.value);

        	        var data = JSON.parse(localStorage.getItem('data_faltantes'));

	                data[0].forEach(function(pedido){
                	        if(pedido.id == consecutivo.value){
                	                pedido.estado = 2;
	                        }
        	        });

	                localStorage.setItem('data_faltantes', JSON.stringify(data));
		}else{

			 localStorage.setItem('editados', JSON.stringify(datos_editados));

		}

		location.reload();
	}
});


document.getElementById('btnRegresar').addEventListener('click', function(){
	document.getElementById('DesplegarNotificacion').click();
});

document.getElementById('saveData_offline').addEventListener('click', function(){

	document.getElementById('cerrarNotificacion').click();

	if(localStorage.getItem('editados')){
		var objeto = JSON.parse(localStorage.getItem('editados'));
	}else{
		var objeto = "no_data";
	}

	var ajax = new XMLHttpRequest();
	ajax.open("GET","/updatePedidos_offline?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
	ajax.setRequestHeader("Content-Type", "application/json");
	ajax.onreadystatechange=function() {
        	if (this.readyState==4 && this.status==200) {
			localStorage.removeItem('editados');
			localStorage.removeItem('data_faltantes');
        	    window.location.href="https://www.desposte.tk/consulta";
	        }else{
			document.getElementById('textInfo').innerHTML = "Ha ocurrido un error, verifica la conexión";
			document.getElementById('DesplegarErrorModal').click();
		}
    	}
    	ajax.send();

});
