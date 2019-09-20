document.getElementById('consecutivo_a_buscar').addEventListener('keyup', function(){

	var consecutivo = document.getElementById('consecutivo_a_buscar').value;
	buscar_pedido_por_consecutivo(consecutivo, "por_alistar");
});


document.getElementById('consecutivo_a_buscar_historial').addEventListener('keyup', function(){

	var consecutivo = document.getElementById('consecutivo_a_buscar_historial').value;
	buscar_pedido_por_consecutivo(consecutivo, "alistado");
});


function buscar_pedido_por_consecutivo(consecutivo, tabla_a_modificar){

/*	if(consecutivo == ""){
		localStorage.setItem("despliegueModalHistorial",1);
		location.reload();
	}*/

	var url="";

	if(tabla_a_modificar == "por_alistar"){
		url="/filtrar_pedido_por_alistar_consecutivo?consecutivo="+consecutivo;
	}else if(tabla_a_modificar == "alistado"){
		url="/filtrar_pedido_alistado_consecutivo?consecutivo="+consecutivo;
	}

	var ajax = new XMLHttpRequest();
	ajax.open("GET",url,true);
	ajax.setRequestHeader("Content-Type", "application/json");
	ajax.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
       	                var json = JSON.parse(ajax.responseText);
                        if(json == "no hay resultados"){
                       	        filtrar_tabla_por_consecutivo(tabla_a_modificar,json);
               	        }else if(Object.keys(json[0]).length == 5){
       	                        filtrar_tabla_por_consecutivo("por_alistar", json);
                        }else{
                               	filtrar_tabla_por_consecutivo("alistado", json);
                       	}
        	}
    }
    ajax.send();

}


function filtrar_tabla_por_consecutivo(tabla_a_modificar, datos){

	if(datos == "no hay resultados"){
		if(tabla_a_modificar == "por_alistar"){
			not_found_results_por_alistar();
		}else{
			not_found_results_historial();
		}
	}else if(tabla_a_modificar == "alistado"){
		table_pedido_historial_consecutivo(datos);
	}else if(tabla_a_modificar == "por_alistar"){
		table_pedido_por_alistar_consecutivo(datos);
	}

}

function not_found_results_historial(){

    document.querySelectorAll("#HistorialTable > tbody").forEach(e => e.parentNode.removeChild(e));
    var table = document.getElementById('HistorialTable');
    var contenidoBody = `
	<tbody id="tBodyHistorial">
        	<tr>
	            <td colspan="8"><p style="font-weight: bold; font-size: 1.5rem;">No hay resultados.</p></td>
        	</tr>
	</tbody>
    `;
    table.innerHTML += contenidoBody;
}

function not_found_results_por_alistar(){

    document.querySelectorAll(" table#generarBorde > tbody ").forEach(e => e.parentNode.removeChild(e));

    var table = document.getElementById('generarBode');

    var contenidoBody = `
	<tbody id="BodyTablePedidos">
	        <tr>
        	    <td colspan="6"><p style="font-weight: bold; font-size: 1.5rem;">No hay resultados.</p></td>
	        </tr>
	</tbody>
    `;

    table.innerHTML += contenidoBody;
}

function table_pedido_historial_consecutivo(objeto){
	var tableHistorial = document.getElementById('HistorialTable');
	    tableHistorial.innerHTML = "";

	    tableHeader = `
	    <thead class="thead-dark" id="CambiarColor">
        	<tr>
	            <th scope="col">Código</th>
	            <th scope="col">Fecha solicitud</th>
	            <th scope="col">Fecha Entrega</th>
	            <th scope="col">Cliente</th>
        	    <th scope="col">Estado</th>
	            <th scope="col">Acciones</th>
        	</tr>
	    </thead>
	    <tbody id="tBodyHistorial">`;

	    tableHistorial.innerHTML += tableHeader;
	    var i = 0;

	    objeto.forEach(function (item) {

        	tableContentHistorial = `
		    <tr>
		        <th scope="row">
		            <button type="button" id="botonHistorial2<?php echo $i; ?>" data-toggle="modal"
		            data-target="#descipcionHistorial" value="${item.id}" onclick="ShowInfoHistorial(this)" style="color:red; font-weight:bold;">
                		PD${item.id}
		            </button>
		        </th>
		        <td id="fecha${i}">${item.fechaSolicitud}

		            <script type="text/javascript">
                		var fecha = '<?php echo $pedido2->FechaSolicitud; ?>';
		                var contador = '<?php echo $i; ?>';

                		var fechaCortada = fecha.substring(0, 10);

		                document.getElementById("fecha"+contador).innerHTML = fechaCortada;
		            </script>
		        </td>
		        <td>${item.fechaEntrega}</td>
		        <td>${item.razonSocial}</td>
		        <td>Alistado</td>
		        <td>
		            <div class="dropdown">
                		<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                  Acciones
                		</button>
		                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                		    <a class="dropdown-item" href="/editPedido/${item.id}">Editar pedido</a>
		                    <form action="{{route('GenerarCSV')}}" method="post">
                		        <input type="hidden" name="consecutivo" value="${item.id}">
		                        <input type="submit" class="dropdown-item" value="Generar CSV">
                		    </form>
	                    	    <a class="dropdown-item" href="/GenerarPDF/${item.id}" target="_blank">Generar PDF</a>
	                	</div>
        		    </div>
		        </td>
		    </tr>
    		`;
	        tableHistorial.innerHTML += tableContentHistorial;
        	i++;
	    });
	    tableHistorial.innerHTML += `
        	</tbody>
	    </table>
	`;
}

function table_pedido_por_alistar_consecutivo(objeto){

    var table = document.getElementById('generarBorde');

    table.innerHTML = "";

    var thead = document.createElement("thead");
    thead.id = "CambiarColor";
    thead.className = "thead-dark";

    table.appendChild(thead);

    var tr = document.createElement("tr");

    thead.appendChild(tr);

    var thCod = document.createElement("th");
    thCod.scope = "col";
    thCod.innerHTML = "Consecutivo";

    var thFechaSolicitud = document.createElement("th");
    thFechaSolicitud.scope = "col";
    thFechaSolicitud.innerHTML = "Fecha Entrega";

    var thCliente = document.createElement("th");
    thCliente.scope = "col";
    thCliente.innerHTML = "Cliente";

    var thEstado = document.createElement("th");
    thEstado.scope = "col";
    thEstado.innerHTML = "Estado";

    tr.appendChild(thCod);
    tr.appendChild(thFechaSolicitud);
    tr.appendChild(thCliente);
    tr.appendChild(thEstado);

    var tbody = document.createElement("tbody");
    tbody.id = "BodyTablePedidos";

    table.appendChild(tbody);

    objeto.forEach(function (item) {

        var fechaCortada = item.fechaSolicitud.substring(0, 10);

        var contenidoBody = `
        <tr>
            <th scope="row">
                <button type="button" id="botonHistorial2<?php echo $i; ?>" data-toggle="modal"
            data-target=".bd-modal-example-xl" value="${item.id}" onclick="SendQuery(this)">
                    <p style="color: rgb(223, 1, 1);"><b>PD${item.id}</b></p>
                </button>
            </th>
            <td id="fecha<?php echo $i; ?>">${item.fechaEntrega}</td>
            <td>${item.razonSocial}</td>
            <td>${item.estado == 1 ? "Por listar" : "se escapo"}</td>
        </tr>`;

        tbody.innerHTML += contenidoBody;
    });
}
