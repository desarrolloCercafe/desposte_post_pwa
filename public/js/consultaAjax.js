window.onload = inicio;

function inicio(){

    if(localStorage.getItem("productos")){
        if(document.getElementById('tbodyEditProductos')){

            var tbodyEdit = document.getElementById('tbodyEditProductos');
            tbodyEdit.innerHTML = "";
            generarTablaEditarPedido(JSON.parse(localStorage.getItem("productos")));

        }
    }else{
        var datos = SaveProductsFirst();
        console.log(datos);
        var tbodyEdit = document.getElementById('tbodyEditProductos');
        tbodyEdit.innerHTML = "";
        localStorage.setItem("productos", JSON.stringify(datos));
        location.reload();
    }

}

if(document.getElementById('addProductoEdit')){

    document.getElementById('addProductoEdit').addEventListener('click', function(){

        var selectProducto = document.getElementById('selectProducto');
        var btnAgregar = document.getElementById('addProductoEdit');
        var messageError = document.getElementById('MessageValidation');
    
        if(selectProducto.value == 0){
            messageError.innerHTML = "No haz seleccionado ningún producto";
            btnAgregar.setAttribute("data-toggle", "modal");
            btnAgregar.dataset.target = "#ValidationModal";
        }else{
    
            btnAgregar.removeAttribute("data-toggle");
            btnAgregar.dataset.target = "";
    
            var tbodyEdit = document.getElementById('tbodyEditProductos');
        
            var array = [];
        
		var estadoPedido = document.getElementById('estado_Pedido').value;

            for (let index = 0; index < tbodyEdit.rows.length; index++) {
                
                var ConsecutivoProducto = document.getElementById('codigo'+index);
                var NombreProducto = document.getElementById('nombre'+index);
                var CantidadSolicitada = document.getElementById('CantidadSolicitada'+index);
		if(estadoPedido == 2){
			var CantidadDespachada = document.getElementById('CantidadDespachada'+index);
		}
                //var CantidadDespachada = document.getElementById('CantidadDespachada'+index);
                var unidadSeleccionada = document.getElementsByName('unidad'+index);
    
                if(CantidadSolicitada.value < 0){
                    CantidadSolicitada.value = 0;
                }

		if(estadoPedido == 2){
	                if(CantidadDespachada.value < 0){
        	            CantidadDespachada.value = 0;
	                }
		}

                /*if(CantidadDespachada.value < 0){
                    CantidadDespachada.value = 0;
                }*/
    
                var valueRadio;
    
                if(unidadSeleccionada[0].checked){
                    valueRadio = unidadSeleccionada[0].value;
                }else if(unidadSeleccionada[1].checked){
                    valueRadio = unidadSeleccionada[1].value;
                }else{
                    valueRadio = 0;
                }
    
		if(estadoPedido == 2){
	                var objeto = {
                	    codigoProducto: ConsecutivoProducto.innerHTML,
        	            NombreProducto: NombreProducto.innerHTML,
	                    CantidadSolicitada: CantidadSolicitada.value,
                	    CantidadDespachada: CantidadDespachada.value,
        	            unidadSeleccionada: valueRadio
	                };

		}else{
	                var objeto = {
                	    codigoProducto: ConsecutivoProducto.innerHTML,
        	            NombreProducto: NombreProducto.innerHTML,
	                    CantidadSolicitada: CantidadSolicitada.value,
                	    CantidadDespachada: 0,
        	            unidadSeleccionada: valueRadio
	                };

		}
                /*var objeto = {
                    codigoProducto: ConsecutivoProducto.innerHTML,
                    NombreProducto: NombreProducto.innerHTML,
                    CantidadSolicitada: CantidadSolicitada.value,
                    CantidadDespachada: CantidadDespachada.value,
                    unidadSeleccionada: valueRadio
                };*/

                array.push(objeto);

            }

            var textSelect = document.getElementById('selectProducto').value;
            var value = textSelect.split(",");


            var productoSeleccionado = {
                codigoProducto: value[0],
                NombreProducto: value[1],
                CantidadSolicitada: 0,
                CantidadDespachada: 0,
                unidadSeleccionada: 0
            }
/**/

            array.push(productoSeleccionado);

            localStorage.setItem("productos", JSON.stringify(array));

            generarTablaEditarPedido(JSON.parse(localStorage.getItem("productos")));

            document.getElementById('cerrarAgregarProductoEditar').click();
        }
    });
}


function generarTablaEditarPedido(objeto){
    //console.log(objeto);
    var tbodyEdit = document.getElementById('tbodyEditProductos');
        tbodyEdit.innerHTML = "";
    
    var tipoPedido = document.getElementById('estado_Pedido').value;

    var i = 0;

    objeto.forEach(function(producto){
        var contenidoTbody = `
            <tr>
                <th scope='row'>
                    <p id='codigo${i}'>${producto.codigoProducto}</p>
                </th>
                <td>
                    <p id='nombre${i}'>${producto.NombreProducto}</p>
                </td>
                <td>
                    <input type="number" name="CantidadSolicitada" ${producto.CantidadSolicitada == 0? "":`value="${producto.CantidadSolicitada}"`}" class="form-control" min="0" id="CantidadSolicitada${i}" step="0.01">
                </td>
		${tipoPedido == 2 ?
                `<td>
                    <input type="number" name="CantidadDespachada" ${producto.CantidadDespachada == 0? "":`value="${producto.CantidadDespachada}"`}" class="form-control" min="0" id="CantidadDespachada${i}" step="0.01"> 
                </td>`:``
		}
                <td class="text-left">
                    ${producto.unidadSeleccionada == 0 ? 
                        `
                        <input type="radio" name="unidad${i}" value="kg" class="mr-2"><span>KG</span>
                        <br>
                        <input type="radio" name="unidad${i}" value="un" class="mr-2"><span>UN</span>`:
                        `
                        <input type="radio" name="unidad${i}" value="kg" class="mr-2" ${producto.unidadSeleccionada == "kg"? `checked`:""}><span>KG</span>
                        <br>
                        <input type="radio" name="unidad${i}" value="un" class="mr-2" ${producto.unidadSeleccionada == "un"? `checked`:""}><span>UN</span>`
                    }
                </td>
                <td>
                    <button type="button" class="btn btn-danger" value='${i}' onclick='Eliminar(this)'>Eliminar</button>
                </td>
            </tr>
        `;
        tbodyEdit.innerHTML += contenidoTbody;
        i++;
    });
}

function Eliminar(objeto){
    
    if(!document.getElementById('deleteProductoLista')){
        var input = document.createElement("input");
        input.type = "hidden";    
        input.id = "deleteProductoLista";
        input.value = objeto.value;

        document.getElementById('bodyDeleteEditPedido').appendChild(input);
    }

    objeto.setAttribute("data-toggle", "modal");
    objeto.dataset.target = "#ModalDeleteEditPedido";

    objeto.click();

}

if(document.getElementById('deleteButtonEditPedido')){

    document.getElementById('deleteButtonEditPedido').addEventListener('click', function(){
    
        var posicion = document.getElementById('deleteProductoLista').value;
    
        var contenidoTable = [];
            contenidoTable = JSON.parse(localStorage.getItem("productos"));
            contenidoTable.splice(posicion, 1);
            localStorage.setItem("productos", JSON.stringify(contenidoTable));
        
        document.getElementById('cerrarDelete').click();
    
        generarTablaEditarPedido(JSON.parse(localStorage.getItem("productos")));
    
    });

}

function DropInputDeleteModal(){
    if(document.getElementById('deleteProductoLista')){
        document.getElementById('bodyDeleteEditPedido').removeChild(document.getElementById('deleteProductoLista'));
    }
}

function SaveProductsFirst(){
    
    

    if(document.getElementById('tbodyEditProductos')){

        var array = [];
        

    }
	var estado = document.getElementById('estado_Pedido').value;
    var contentTableEditar = document.getElementById('tbodyEditProductos');

    for (let index = 0; index < contentTableEditar.rows.length; index++) {
        
        var consecutivoProducto = document.getElementById('codigo'+index);
        var NombrePedido = document.getElementById('nombre'+index);
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
        	    codigoProducto: consecutivoProducto.innerHTML,
	            NombreProducto: NombrePedido.innerHTML,
        	    CantidadSolicitada: cantidadSolicitada.value,
	            CantidadDespachada: cantidadDespachada.value,
        	    unidadSeleccionada: valueRadio
	        }
	}else{
                var productosEditado = {
                    codigoProducto: consecutivoProducto.innerHTML,
                    NombreProducto: NombrePedido.innerHTML,
                    CantidadSolicitada: cantidadSolicitada.value,
                    CantidadDespachada: 0,
                    unidadSeleccionada: valueRadio
                }
	}

        array.push(productosEditado);
    }

    return array;

}
