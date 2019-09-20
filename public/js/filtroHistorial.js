window.onload = VerificarDespliegueHistorial;

document.getElementById('rangoFechasHistorial').addEventListener('change',function(){
	var select = document.getElementById('rangoFechasHistorial');
	value = select.value;

	if(value != 0){
		document.getElementById('GlobalGenerarCSV').disabled = false;
		document.getElementById('GlobalGenerarPDF').disabled = false;
	}else{
		document.getElementById('GlobalGenerarCSV').disabled = true;
		document.getElementById('GlobalGenerarPDF').disabled = true;
	}

	if(value == 3){
		var btnModal = document.getElementById('btnDespliegueRangoFechas');
		btnModal.click();
	}

});

function VerificarDespliegueHistorial(){

    if(!localStorage.getItem('nombre')){
        window.location.href = "https://www.desposte.tk/";
    }
    
    if(localStorage.getItem('despliegueModalHistorial')){
        document.getElementById('botonHistorial').click();
        localStorage.removeItem("despliegueModalHistorial");
    }
}

//Generamos el evento de escucha del boton
document.getElementById('GlobalGenerarCSV').addEventListener('click', function () {
//	alert('flkjgldkjg');
    if (verificarFiltros()) {
        var data = SaveData();
        SendQueryHistorialCSV(data);
    }else{
        var messageError = document.getElementById('textInfo'); 
        messageError.innerHTML = "No haz seleccionado ningún tipo de filtro";
        document.getElementById('DesplegarErrorModal').click();
    }
});

//Generamos el evento de escucha del boton
document.getElementById('GlobalGenerarPDF').addEventListener('click', function () {
    if (verificarFiltros()) {
        var data = SaveData();
        SendQueryHistorialPDF(data);
    }else{
        var messageError = document.getElementById('textInfo');

        messageError.innerHTML = "No haz seleccionado ningún tipo de filtro";
        document.getElementById('DesplegarErrorModal').click();
    }
});

//Método de verificación de los filtros 
function verificarFiltros() {

    document.getElementById('footerHistorial').innerHTML = "";

    fechaHistorialFiltro = document.getElementById('rangoFechasHistorial');
    codCliente = document.getElementById('codClienteHistorial');
    tipoPresentacion = document.getElementById('tipoPresentacionHistorial');

    if (fechaHistorialFiltro.value == 0 &&
        codCliente.value.length == 0 &&
        tipoPresentacion.value == 1) {
        return false;
    } else {
        return true;
    }

}

function SaveData(){
    var selectFecha, selectCliente, tipoPresentacion;
    var valorFecha;
    var valorFecha1 = 0;
    var valorFecha2 = 0;
    var FechaHoy = 0;

    var FechaAyer = 0;

    selectFecha = document.getElementById('rangoFechasHistorial').value;
    selectCliente = document.getElementById('codClienteHistorial');
    tipoPresentacion = document.getElementById('tipoPresentacionHistorial').value;

        if(selectFecha == 0 && selectCliente.value.length == 0 && tipoPresentacion == 1){
                localStorage.setItem('despliegueModalHistorial', 1);
                location.reload();
        }

    var fecha = new Date();
                dia = fecha.getDate();
                mes = fecha.getMonth()+1;
                year = fecha.getFullYear();

    if(selectFecha != 0){
        if(selectFecha == 1){
            valorFecha = 1;
            FechaHoy = year+"-"+mes+"-"+dia;
        }else if(selectFecha == 2){
            valorFecha = 2;
            FechaAyer = year+"-"+mes+"-"+dia;
        }else if(selectFecha == 3){
            valorFecha = 3;
            valorFecha1 = document.getElementById('fechaInicio').value;
            valorFecha2 = document.getElementById('fechaFin').value;
        }
    }

  if(selectCliente.value.length > 0){
        var data = document.querySelector('#dataClientesHistorial2 > option[value="'+selectCliente.value+'"]');
        selectCliente.value = data.innerHTML;
    }

    var objeto = {
        codCliente: selectCliente.value,
        tipoPresentacion: tipoPresentacion,
        valorFecha: valorFecha,
        FechaHoy: FechaHoy,
        FechaAyer: FechaAyer,
        fechaInicioH: valorFecha1,
        fechaFin: valorFecha2
    };

    if(objeto.codCliente.length <= 0){
        delete objeto.codCliente;
    }

    if(objeto.valorFecha == 0){
        delete objeto.valorFecha;
    }

    if(objeto.FechaHoy == 0){
        delete objeto.FechaHoy;
    }

    if(objeto.FechaAyer == 0){
        delete objeto.FechaAyer;
    }

    if(objeto.valorFecha == 1 || objeto.valorFecha == 2){
        delete objeto.fechaInicioH;
        delete objeto.fechaFin;
    }

    document.getElementById('rangoFechasHistorial').value = 0;
    document.getElementById('codClienteHistorial').value = "";
    document.getElementById('tipoPresentacionHistorial').value = 1;

    return objeto;
}

//Mètodo para el filtrado de resultados del boton de Historial
document.getElementById('filtrarPedidosHistorial').addEventListener('click', function(){

    var selectFecha, selectCliente, tipoPresentacion;
    var valorFecha;
    var valorFecha1 = 0;
    var valorFecha2 = 0;
    var FechaHoy = 0;
    var FechaAyer = 0;

    selectFecha = document.getElementById('rangoFechasHistorial').value;
    selectCliente = document.getElementById('codClienteHistorial');
    tipoPresentacion = document.getElementById('tipoPresentacionHistorial').value;

	if(selectFecha == 0 && selectCliente.value.length == 0 && tipoPresentacion == 1){
		localStorage.setItem('despliegueModalHistorial', 1);
	        location.reload();
	}

    var fecha = new Date();
                dia = fecha.getDate();
                mes = fecha.getMonth()+1;
                year = fecha.getFullYear();

    if(selectFecha != 0){
        if(selectFecha == 1){
            valorFecha = 1;
	    FechaHoy = year+"-"+mes+"-"+dia;
        }else if(selectFecha == 2){
            valorFecha = 2;
            FechaAyer = year+"-"+mes+"-"+dia;
        }else if(selectFecha == 3){
            valorFecha = 3;
            valorFecha1 = document.getElementById('fechaInicio').value;
            valorFecha2 = document.getElementById('fechaFin').value;
        }
    }

    if(selectCliente.value.length > 0){
        var data = document.querySelector('#dataClientesHistorial2 > option[value="'+selectCliente.value+'"]');
        selectCliente.value = data.innerHTML;
    }

    var objeto = {
        codCliente: selectCliente.value,
        tipoPresentacion: tipoPresentacion,
        valorFecha: valorFecha,
	FechaHoy: FechaHoy,
	FechaAyer: FechaAyer,
        fechaInicioH: valorFecha1,
        fechaFin: valorFecha2
    };

    if(objeto.codCliente.length <= 0){
        delete objeto.codCliente;
    }

    if(objeto.valorFecha == 0){
        delete objeto.valorFecha;
    }

    if(objeto.FechaHoy == 0){
	delete objeto.FechaHoy;
    }

    if(objeto.FechaAyer == 0){
	delete objeto.FechaAyer;
    }

    if(objeto.valorFecha == 1 || objeto.valorFecha == 2){
        delete objeto.fechaInicioH;
        delete objeto.fechaFin;
    }

    document.getElementById('rangoFechasHistorial').value = 0;
    document.getElementById('codClienteHistorial').value = "";
    document.getElementById('tipoPresentacionHistorial').value = 1;

    sendQueryFiltrosHistorial(objeto);
})

function sendQueryFiltrosHistorial(objeto) {

    //http://192.168.9.139/julioCercafe/despostes/public
	console.log(objeto);
    var ajax = new XMLHttpRequest();
    ajax.open("GET","https://www.desposte.tk/filtrarHistorial?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            var json = JSON.parse(ajax.responseText);
            if(json == "no hay resultados"){
                NotFoundResultsHistorial();
            }else if(Object.keys(json[0]).length == 5){
                TablePedidoHistorial(json);
            }else{
                TableProductoHistorial(json);
            }
        }
    }
    ajax.send();
}

//Función para lanzar los filtros obtenidos hacia laravel en forma oculta
function SendQueryHistorialCSV(objeto) {

	console.log(objeto);

    var ajax = new XMLHttpRequest();
    ajax.open("GET","https://www.desposte.tk/CSVGeneral?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            var json = ajax.responseText;
//		console.log(json);
            if(json == "no hay resultados"){
                var messageError = document.getElementById('textInfo'); 
                messageError.innerHTML = "No hay resultados.";
                document.getElementById('DesplegarErrorModal').click();
            }else{
	    var form = document.getElementById('CSVcreation');

 	   var contentForm = `
       		 ${objeto.hasOwnProperty('fechaInicioH') ?
	            `<input type="hidden" name="fechaInicioH" value="${objeto.fechaInicioH}">
        	    <input type="hidden" name="fechaFin" value="${objeto.fechaFin}">` : ''
	        }
        	${objeto.hasOwnProperty('codCliente') ?
	            `<input type="hidden" name="codCliente" value="${objeto.codCliente}">` : ""
        	}
	        ${objeto.hasOwnProperty('tipoPresentacion') ?
	            `<input type="hidden" name="tipoPresentacion" value="${objeto.tipoPresentacion}">` : ""
	        }
		${objeto.hasOwnProperty('valorFecha')?
		    `<input type="hidden" name="valorFecha" value="${objeto.valorFecha}">`:""
		}
		${objeto.hasOwnProperty('FechaHoy')?
		    `<input type="hidden" name="FechaHoy" value="${objeto.FechaHoy}">`:""
		}
		${objeto.hasOwnProperty('FechaAyer')?
		    `<input type="hidden" name="FechaAyer" value="$objeto.FechaAyer">`:""
		}
	    `;

	    form.innerHTML += contentForm;

	    form.submit();

	    document.querySelectorAll("#csvGeneral > input").forEach(e => e.parentNode.removeChild(e));
	    }
        }
    }
    ajax.send();
}

//Función para lanzar los filtros obtenidos hacia laravel en forma oculta
function SendQueryHistorialPDF(objeto) {

    var form = document.getElementById('pdfGeneral');

    var contentForm = `
         ${objeto.hasOwnProperty('fechaInicioH') ?
             `<input type="hidden" name="fechaInicioH" value="${objeto.fechaInicioH}">
              <input type="hidden" name="fechaFin" value="${objeto.fechaFin}">` : ''
         }
         ${objeto.hasOwnProperty('codCliente') ?
             `<input type="hidden" name="codCliente" value="${objeto.codCliente}">` : ""
         }
         ${objeto.hasOwnProperty('tipoPresentacion') ?
             `<input type="hidden" name="tipoPresentacion" value="${objeto.tipoPresentacion}">` : ""
         }
         ${objeto.hasOwnProperty('valorFecha')?
             `<input type="hidden" name="valorFecha" value="${objeto.valorFecha}">`:""
         }
         ${objeto.hasOwnProperty('FechaHoy')?
             `<input type="hidden" name="FechaHoy" value="${objeto.FechaHoy}">`:""
         }
         ${objeto.hasOwnProperty('FechaAyer')?
             `<input type="hidden" name="FechaAyer" value="$objeto.FechaAyer">`:""
         }

    `;

    form.innerHTML += contentForm;

    form.submit();

    document.querySelectorAll("#csvGeneral > input").forEach(e => e.parentNode.removeChild(e));

}

function NotFoundResultsHistorial(){
    var tableHistorial = document.getElementById('HistorialTable');
    tableHistorial.innerHTML = "";

    tableContent = `
    <thead class="thead-dark" id="CambiarColor">
        <tr>
            <th scope="col">Código</th>
            <th scope="col">Fecha Solicitud</th>
            <th scope="col">Fecha Entrega</th>
            <th scope="col">Cliente</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
        </tr>
    </thead>
    <tbody id="tBodyHistorial">
	<td colspan="8"><p style="font-weight: bold; font-size: 1.5rem;">No hay resultados.</p></td>
    </tbody>
`;

    tableHistorial.innerHTML += tableContent;
/**/
}

function TablePedidoHistorial(objeto) {


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
            data-target="#descipcionHistorial" value="${item.id}" onclick="ShowInfoHistorial(this)">
                <p style="color:red; font-weight:bold">PD${item.id}</p>
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
                    <a class="dropdown-item" href="https://www.desposte.tk/editPedido/${item.id}">Editar pedido</a>
		    <a class="dropdown-item" href="https://www.desposte.tk/GenerarCSV/${item.id}">Generar CSV</a>
                    <a class="dropdown-item" href="https://www.desposte.tk/GenerarPDF/${item.id}">Generar PDF</a>
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

function TableProductoHistorial(objeto) {

    var tableHistorial = document.getElementById('HistorialTable');
    tableHistorial.innerHTML = "";

    tableHeader = `
    <thead class="thead-dark" id="CambiarColor">
        <tr>
            <th scope="col">Código Producto</th>
            <th scope="col">Nombre Producto</th>
            <th scope="col">Cant. Solicitada</th>
            <th scope="col">Cant. Despachada</th>
            <th scope="col">Unidad/Medida</th>
            <th scope="col">Nombre Cliente</th>
            <th scope="col">Consec. Pedido</th>
            <th scope="col">Acciones</th>
        </tr>
    </thead>
    <tbody id="tBodyHistorial">`;

    tableHistorial.innerHTML += tableHeader;
    var i = 0;

    objeto.forEach(function (item) {

        tableContentHistorial = `
        <tr>
            <th scope="row">${item.codigo}</th>
            <td>${item.NombreProd}</td>
            <td>${item.cantidadSolicitada}</td>
            <td>${item.cantidadDespachada}</td>
            <td>${item.unidadMedida}</td>
            <td>${item.NombreCl}</td>
            <td>${item.id}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Acciones
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="https://www.desposte.tk/editPedido/${item.id}">Editar pedido</a>

                        <form action="{{route('GenerarCSV')}}" method="post">
                            <input type="hidden" name="consecutivo" value="${item.id}">
                            <input type="submit" class="dropdown-item" value="Generar CSV">
                        </form>

                        <a class="dropdown-item" href="https://www.desposte.tk/GenerarPDF/${item.id}">Generar PDF</a>

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

//Funciones para la activación de los botones generales sobre PDF y CSV
document.getElementById('codClienteHistorial').addEventListener('keyup', function(){
	var cliente = document.getElementById('codClienteHistorial').value;
	if(cliente.length > 0){
		document.getElementById('GlobalGenerarCSV').disabled = false;
		document.getElementById('GlobalGenerarPDF').disabled = false;
	}else{
		document.getElementById('GlobalGenerarCSV').disabled = true;
		document.getElementById('GlobalGenerarPDF').disabled = true;
	}
});

document.getElementById('tipoPresentacionHistorial').addEventListener('change',function(){
	var select = document.getElementById('tipoPresentacionHistorial');
	if(select.value != 1){
		document.getElementById('GlobalGenerarCSV').disabled = false;
		document.getElementById('GlobalGenerarPDF').disabled = false;
	}else{
		document.getElementById('GlobalGenerarCSV').disabled = true;
		document.getElementById('GlobalGenerarPDF').disabled = true;
	}
});
