//Evento de despliegue de la modal del rango de fechas; el evento se activa una vez seleccionado
//la opción "Otro" en el select de fechas
/**/

document.getElementById('rangoFechas').addEventListener('change', function(){
	var select = document.getElementById('rangoFechas');
	value = select.value;
	if(value == 3){
		var btnModal = document.getElementById('btnDespliegueRangoFechas');
		btnModal.click();
	}
})

//Evento listener para la validación del rango de fechas
document.getElementById('RangoFechasFiltroSuccess').addEventListener('click', function(){

    var fechaInicio = document.getElementById('fechaInicio');
    var fechaFin = document.getElementById('fechaFin');

    if(fechaInicio.value.length == 0 && fechaFin.value.length == 0){
        if(document.getElementById('alertErrorRangoFechas')){
            document.getElementById('contentRangoFechasModal').removeChild(document.getElementById('alertErrorRangoFechas'));
        }
        document.getElementById('contentRangoFechasModal').innerHTML += 
        `<p style="color:red; font-size:1rem; font-weight:bold;" id="alertErrorRangoFechas">Se debe de especificar el rango de fechas.</p>`;
    }else if(Date.parse(fechaFin.value) < fechaInicio.value){
        if(document.getElementById('alertErrorRangoFechas')){
            document.getElementById('contentRangoFechasModal').removeChild(document.getElementById('alertErrorRangoFechas'));
        }
        document.getElementById('contentRangoFechasModal').innerHTML += 
        `<p style="color:red; font-size:1rem; font-weight:bold;" id="alertErrorRangoFechas">La fecha de inicio no puede ser mayor a la fecha limite.</p>`; 
    }else{
        document.getElementById('closeModalRangoFechas').click();
    }
});

//Evento de escucha para la eliminación de la alerta una vez es cerrado el modal de rango de fechas
document.getElementById('closeModalRangoFechas').addEventListener('click', function(){
    if(document.getElementById('alertErrorRangoFechas')){
        document.getElementById('contentRangoFechasModal').removeChild(document.getElementById('alertErrorRangoFechas'));
    }
})

document.getElementById('filtrarPedidos').addEventListener('click', function(){

    var selectFecha, selectCliente, tipoPresentacion;
    var valorFecha;
    var valorFecha1 = 0;
    var valorFecha2 = 0;
    var FechaHoy = 0;

    var FechaAyer = 0;

    selectFecha = document.getElementById('rangoFechas').value;
    selectCliente = document.getElementById('codCliente');
    tipoPresentacion = document.getElementById('tipoPresentacion').value;

        if(selectFecha == 0 && selectCliente.value.length == 0 && tipoPresentacion == 1){
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

    document.getElementById('rangoFechas').value = 0;
    document.getElementById('codCliente').value = "";
    document.getElementById('tipoPresentacion').value = 1;

    sendQueryFiltrosConsulta(objeto);

/**/
})

function sendQueryFiltrosConsulta(objeto) {

    var ajax = new XMLHttpRequest();
    ajax.open("GET","/filtrarTable?data="+ encodeURIComponent(JSON.stringify(objeto)),true);
    ajax.setRequestHeader("Content-Type", "application/json");
    ajax.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            var json = JSON.parse(ajax.responseText);
            if(json == "no hay resultados"){
                NotFoundResults();
            }else if(Object.keys(json[0]).length == 5){
                TablePedido(json);
            }else{
                TableProducto(json);
            }
        }
    }
    ajax.send();
}

function NotFoundResults(){

    var tableHistorial = document.getElementById('generarBorde');
    tableHistorial.innerHTML = "";

    tableContent = `
    <thead class="thead-dark" id="CambiarColor">
        <tr>
            <th scope="col">Código</th>
            <th scope="col">Fecha Entrega</th>
            <th scope="col">Cliente</th>
            <th scope="col">Estado</th>
        </tr>
    </thead>
    <tbody id="BodyTablePedidos">
        <td colspan="6"><p style="font-weight: bold; font-size: 1.5rem;">No hay resultados.</p></td>
    </tbody>
`;

    tableHistorial.innerHTML += tableContent;

/**/
}

function TableProducto(objeto) {

    //alert("llegó");

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
    thCod.innerHTML = "Código Producto";

    var thNombreProd = document.createElement("th");
    thNombreProd.scope = "col";
    thNombreProd.innerHTML = "Nombre Producto";

    var thCantidad = document.createElement("th");
    thCantidad.scope = "col";
    thCantidad.innerHTML = "Cant. Solicitada";

    var thUnidad = document.createElement("th");
    thUnidad.scope = "col";
    thUnidad.innerHTML = "Unidad/Medida";

    var thNombreCliente = document.createElement("th");
    thNombreCliente.scope = "col";
    thNombreCliente.innerHTML = "Nombre cliente";

    var thConsecutivo = document.createElement("th");
    thConsecutivo.scope = "col";
    thConsecutivo.innerHTML = "Consec. Pedido";

    tr.appendChild(thCod);
    tr.appendChild(thNombreProd);
    tr.appendChild(thCantidad);
    tr.appendChild(thUnidad);
    tr.appendChild(thNombreCliente);
    tr.appendChild(thConsecutivo);

    var tbody = document.createElement("tbody");
    tbody.id = "BodyTablePedidos";

    table.appendChild(tbody);

    objeto.forEach(function (item) {

        var contenidoBody = `
                <tr>
                    <th scope="row">${item.codigo}</th>
                    <td id="fecha">${item.NombreProd}</td>
                    <td>${item.cantidadSolicitada}</td>
                    <td>${item.unidadMedida}</td>
                    <td>${item.NombreCl}</td>
                    <td>${item.id}</td>
                </tr>
            </tbody>`;

        tbody.innerHTML += contenidoBody;
    });

}

function TablePedido(objeto) {

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
