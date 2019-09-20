window.addEventListener('load', function(){

    if(!localStorage.getItem('nombre')){
        window.location.href = "https://www.desposte.tk/";
    }
    
    if(this.localStorage.getItem("despliegue")){
        this.document.getElementById('botonHistorial').click();
        this.localStorage.removeItem("despliegue");
    }
})
    if(document.getElementById('ProductoAlistado')){
        document.getElementById('ProductoAlistado').addEventListener('click', function(){
    
            var tFilas = (document.getElementById('tablaProductos').rows.length);
        
            //alert(tFilas);
            if(validar(tFilas)){
                var data = SaveElements(tFilas);
                SendInfo(data); 
            }
        })
    }


function validar(tFilas){

    var btnAlistado = document.getElementById('ProductoAlistado');
    var ErrorMessage = document.getElementById('textInfo');
    var error = 0;

    for (let index = 0; index < tFilas; index++) {
        
        var CantidadDespachada = document.getElementById('cantidadDespachada'+index);

        if(CantidadDespachada.value <= 0 || CantidadDespachada.value.length == 0){
            ErrorMessage.innerHTML = "Existen cantidades vacías";
            btnAlistado.setAttribute("data-toggle", "modal");
            btnAlistado.dataset.target = "#errorModal";
            btnAlistado.click();
            error = 1;
            break;
        }else{
            btnAlistado.removeAttribute("data-toggle");
            btnAlistado.dataset.target = "";
        }

        error = 0;
        
    }

    if(error == 0){
        btnAlistado.removeAttribute("data-toggle");
        btnAlistado.dataset.target = "";
        return true;
    }

}


function SaveElements(tFilas){

    var cantidad = [];

    for (let index = 0; index < tFilas; index++) {
        
        var CantidadDespachada = document.getElementById("cantidadDespachada"+index);

        var CodigoProducto = document.getElementById("codProducto"+index);
        
        var productos = {
            cantidad: CantidadDespachada.value,
            codigo: CodigoProducto.innerHTML
        };

        cantidad.push(productos);
    }

    return cantidad;

}

function SendInfo(obj){

    var form = document.getElementById("updateData");

    var inputDespacho = document.createElement("input");
    inputDespacho.type = "hidden";
    inputDespacho.name = "DespachoCantidad";
    inputDespacho.value = JSON.stringify(obj);

    form.appendChild(inputDespacho);

    var inputPedido = document.createElement("input");
    inputPedido.type = "hidden";
    inputPedido.name = "CodPedido";
    inputPedido.value = localStorage.getItem("PedidoSeleccionado");

    form.appendChild(inputPedido);

    localStorage.removeItem("PedidoSeleccionado");
    //localStorage.clear();
    form.submit();

}

if(document.getElementById('footerHistorial')){

    document.querySelector('#footerHistorial ul.pagination ').addEventListener('click', function(){
        localStorage.setItem("despliegue", 1);
    });

}
