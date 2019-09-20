if(document.getElementById('botonEditarPedido')){
    document.getElementById('botonEditarPedido').addEventListener('click', function(){

        document.getElementById('botonEditarPedido').removeAttribute("data-toggle");
        document.getElementById('botonEditarPedido').dataset.target = "";

        if(validarTableEditar()){
            var contenidoTable;
            contenidoTable = SaveProducts();
            SendQueryUpdate(contenidoTable);
        }

    });
}


function validarTableEditar(){

    var messageValidation = document.getElementById('MessageValidation');
    var botonEditarPedido = document.getElementById('botonEditarPedido');

	var estado = document.getElementById('estado_Pedido').value;

    if(fechaEntrega.value.length == 0){

        messageValidation.innerHTML = "No haz seleccionado una fecha de entrega";
        botonEditarPedido.setAttribute("data-toggle", "modal");
        botonEditarPedido.dataset.target = "#ValidationModal";
        botonEditarPedido.click();

    }else{

        var error = 0;
        var contentTableEditar = document.getElementById('tbodyEditProductos');

        for (let index = 0; index < contentTableEditar.rows.length; index++) {

            var cantidadSolicitada = document.getElementById('CantidadSolicitada'+index);
	    if(estado == 2){
		var cantidadDespachada = document.getElementById('CantidadDespachada'+index);
	    }

            var radio = document.getElementsByName("unidad"+index);

	    if(estado == 2){
        	    if(cantidadSolicitada.value <= 0 || cantidadDespachada.value <= 0){
	                ErroresEditarPedido('Cantidades');
                	error = 1;
        	        break;
	            }
	    }else{
		if(cantidadSolicitada.value <= 0){
			ErroresEditarPedido('Cantidades');
			error = 1;
			break;
		}
	    }

            if(radio[0].checked){
                continue;
            }else if(radio[1].checked){
                continue;
            }else{
                ErroresEditarPedido('radio');
                error = 1;
                break;
            }

            error = 0;

        }

        if(error == 0){
            return true;
        }
    }
}

function ErroresEditarPedido(error){

    var messageValidation = document.getElementById('MessageValidation');
    var botonEditarPedido = document.getElementById('botonEditarPedido');

    switch (error) {
        case 'Cantidades':
            messageValidation.innerHTML = "Existen Cantidades vacías";
            botonEditarPedido.setAttribute("data-toggle", "modal");
            botonEditarPedido.dataset.target = "#ValidationModal";
            botonEditarPedido.click();
            break;
        case 'radio':
            messageValidation.innerHTML = "Existen Unidades no seleccionadas";
            botonEditarPedido.setAttribute("data-toggle", "modal");
            botonEditarPedido.dataset.target = "#ValidationModal";
            botonEditarPedido.click();
            break;

    }
}

function SaveProducts(){

    var array = [];

    var contentTableEditar = document.getElementById('tbodyEditProductos');
    var estado = document.getElementById('estado_Pedido').value;

    for (let index = 0; index < contentTableEditar.rows.length; index++) {

        var consecutivoProducto = document.getElementById('codigo'+index);
        var cantidadSolicitada = document.getElementById('CantidadSolicitada'+index);
	if(estado == 2){
		var cantidadDespachada = document.getElementById('CantidadDespachada'+index);
	}
        //var cantidadDespachada = document.getElementById('CantidadDespachada'+index);
        var radio = document.getElementsByName("unidad"+index);

        var valueRadio;

        if(radio[0].checked){
            valueRadio = radio[0].value;
        }else if(radio[1].checked){
            valueRadio = radio[1].value;
        }

	if(estado == 2){
		var productosEditado = {
	            consecutivoProducto: consecutivoProducto.innerHTML,
	            cantidadSolicitada: cantidadSolicitada.value,
	            cantidadDespachada: cantidadDespachada.value,
        	    unidad: valueRadio
	        }
	}else{
		var productosEditado = {
	            consecutivoProducto: consecutivoProducto.innerHTML,
        	    cantidadSolicitada: cantidadSolicitada.value,
	            cantidadDespachada: 0,
        	    unidad: valueRadio
	        }
	}

        array.push(productosEditado);
    }

    return array;

}

function SendQueryUpdate(objeto){

    var consecutivoPedido = document.getElementById('ConsecutivoPedido').value;
    var fechaEntrega = document.getElementById('fechaEntrega').value;

    var formElements = `
        <textarea name="productosPedido" style="display:none;">${JSON.stringify(objeto)}</textarea>
        <input type="hidden" value="${consecutivoPedido}" name="consecutivoPedido">
        <input type="hidden" value="${fechaEntrega}" name="fechaEntrega">
    `;
    document.getElementById('formUpdatePedido').innerHTML += formElements;

    localStorage.removeItem("productos");

    document.getElementById('formUpdatePedido').submit();

}

document.getElementById('regresar_consulta').addEventListener('click', function(){

	if(localStorage.getItem('productos')){
		localStorage.removeItem('productos');
	}

	window.location.href = "https://www.desposte.tk/consulta";

});
