document.getElementById('botonGenerarPedido').addEventListener('click', function(){
    
    if(validaciones()){
        //alert("todo correcto");
        var datos;
        datos = SaveData();
        //console.log(datos);
        SendData(datos);
    }
    //validaciones();
})

//Función Errores, sirve para la notificación del error mediante la modal ValidationModal
function Errores(error){

    //Capturamos al botón por su id
    var BtnGenerarPedido = document.getElementById('botonGenerarPedido');

    //Capturamos el h2 donde escribiremos el mensaje
    var ValidationMessage = document.getElementById('MessageValidation');

    switch (error) {
        case 'selectCliente':
            ValidationMessage.innerHTML = "No haz seleccionado un cliente";
            BtnGenerarPedido.setAttribute("data-toggle", "modal");
            BtnGenerarPedido.dataset.target = "#ValidationModal";
            BtnGenerarPedido.click();
            break;
        case 'fechaEntrega':
            ValidationMessage.innerHTML = "No haz seleccionado una fecha de entrega";
            BtnGenerarPedido.setAttribute("data-toggle", "modal");
            BtnGenerarPedido.dataset.target = "#ValidationModal";
            BtnGenerarPedido.click();
            break;
        case 'numFilas':
            ValidationMessage.innerHTML = "No haz Ingresado ningún producto";
            BtnGenerarPedido.setAttribute("data-toggle", "modal");
            BtnGenerarPedido.dataset.target = "#ValidationModal";
            BtnGenerarPedido.click();
            break;
        case 'input':
            ValidationMessage.innerHTML = "Existen cantidades vacías.";
            BtnGenerarPedido.setAttribute("data-toggle", "modal");
            BtnGenerarPedido.dataset.target = "#ValidationModal";
            BtnGenerarPedido.click();
            break;
        case 'radio':
            ValidationMessage.innerHTML = "Existen unidades no seleccionadas.";
            BtnGenerarPedido.setAttribute("data-toggle", "modal");
            BtnGenerarPedido.dataset.target = "#ValidationModal";
            BtnGenerarPedido.click();
            break;
    }
}

function validaciones(){

    //capturar el boton de Generar pedido
    var BtnGenerarPedido = document.getElementById('botonGenerarPedido');
    
    //capturar el valor del select en la selección del cliente
    var SelectCliente = document.getElementById('selectCliente');

    //capturar el valor del input de fechaEntrega
    var fechaEntrega = document.getElementById('fechaEntrega');

    //capturar a la tabla dinámica por su id
    var filas = document.getElementById('borde');

    //Comprobación de errores
    if(SelectCliente.value == 0){

        Errores('selectCliente');

    }else if(fechaEntrega.value.length == 0){

        Errores('fechaEntrega');

    }else if(filas.rows.length <= 1){

        Errores('numFilas');

    }else{
        
        var error = 0;

        //Borrar propiedades al boton de Generar pedido
        BtnGenerarPedido.removeAttribute("data-toggle");
        BtnGenerarPedido.dataset.target = "";

        //Número de veces del ciclo
        var CantidadCiclos =  (filas.rows.length - 1);

        for (let index = 0; index < CantidadCiclos; index++) {

            //capturar variables
            var input =  document.getElementById("CantidadSolicitada"+index);
            
            //comprobación de los input
            if(input.value <= 0  || input.value.length == 0){

                Errores('input');
                error = 1;
                break;

            }

            //capturar radio botones
            var radio = document.getElementsByName("unidad"+index);
            
            //comprobación de los radio botones
            if(radio[0].checked){
                continue;
            }else if(radio[1].checked){
                continue;
            }else{
                Errores('radio');
                error = 1;
                break;
            }//fin del else para la comprobación de los radio botones

            error = 0;

        }//fin del ciclo for

        //Comprobación de la variable errores
        if(error == 0){
            return true;
        }//fin del if comprobando a la variable error

    }//fin del else

}//fin de la función validaciones()


function SaveData(){

    //Obtenemos los elementos de la tabla por su id
    var filas = document.getElementById("borde");

    //Número de veces del ciclo
    var CantidadCiclos =  (filas.rows.length - 1);

    var array = [];

    for (let index = 0; index < CantidadCiclos; index++) {
        
        var codigo = document.getElementById("codigo"+index);
        var nombre = document.getElementById("nombreProducto"+index);
        var cantidad = document.getElementById("CantidadSolicitada"+index);
        var radio = document.getElementsByName("unidad"+index);

        var valueRadio;

        if(radio[0].checked){
            valueRadio = radio[0].value;
        }else if(radio[1].checked){
            valueRadio = radio[1].value;
        }

        var producto = {
            codigo: codigo.innerHTML,
            nombre: nombre.innerHTML,
            cantidad: cantidad.value,
            radio: valueRadio
        };

        array.push(producto);

    }

    return array;

}

function SendData(obj){

    var form = document.getElementById("formInsert");

    var inputProductos = document.createElement("input");
    inputProductos.type = "hidden";
    inputProductos.name = "productos";
    inputProductos.value = JSON.stringify(obj);


    var selectCliente = document.getElementById("selectCliente").value;
    var inputCliente = document.createElement("input");
        inputCliente.type = "hidden";
        inputCliente.name = "codCliente";
        inputCliente.value = selectCliente;

    
    var fechaEntrega = document.getElementById("fechaEntrega").value;
    var inputFechaEntrega = document.createElement("input");
        inputFechaEntrega.type = "hidden";
        inputFechaEntrega.name = "fechaEntrega";
        inputFechaEntrega.value = fechaEntrega;

    var fecha = new Date();
    var fechaSolicitud = fecha.getFullYear()+"-"+
                            (fecha.getMonth() + 1)+"-"+
                            fecha.getDate()+" "+
                            fecha.getHours()+":"+
                            fecha.getMinutes()+":"+
                            fecha.getSeconds();
    
    var inputSolicitud = document.createElement("input");
        inputSolicitud.type = "hidden";
        inputSolicitud.name = "fechaSolicitud";
        inputSolicitud.value = fechaSolicitud;

    var nombreVendedor = document.createElement("input");
        nombreVendedor.type = "hidden";
        nombreVendedor.name = "nombreVendedor";
        nombreVendedor.value = document.getElementById('vendedor').value;

    form.appendChild(inputProductos);
    form.appendChild(inputCliente);
    form.appendChild(inputFechaEntrega);
    form.appendChild(inputSolicitud);
    form.appendChild(nombreVendedor);

    localStorage.removeItem("productos");

    //document.getElementById('desplegar_modal_registro_pedido').click();

    //setTimeout(function(){ form.submit(); }, 3000);
    form.submit();
}
