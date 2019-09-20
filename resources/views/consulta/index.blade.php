@extends('layout.layout')
    <nav id="navStyle">
        <section class="BtnsRedireccion">
            <a href="{{url('https://www.desposte.tk/solicitud')}}" class="btn btn-outline-light mt-2" id="back">Ir a Solicitud</a>
            <a href="#" class="btn btn-light mt-2 ml-3" id="back">Consulta</a>
        </section>
        <section class="BtnCerrarSesion">
	    <button class="btn btn-outline-light mr-3" id="btn_caba">CABA</button>

		<button type="button" class="btn btn-outline-light mr-3" data-toggle="modal" data-target="#modal_consecutivo">
		  Consecutivo
		</button>

            <a class="btn btn-light my-2 my-sm-0" type="button" href="{{url('https://www.desposte.tk/')}}" id="btnCerrarSession">
                <img src="{{asset('svg/powerOff.svg')}}" alt="Cerrar Sesión" width="15px" height="15px">
            </a>
        </section>
    </nav>
@section('content')
            <div id="headRow">
                    <article class="headerCard">
                        <h3 id="head" class="card-header">Consulta</h3>
                    </article>
                    <button type="button" id="botonHistorial" data-toggle="modal" data-target=".bd-example-modal-xl">
                        <img src="{{asset('svg/history2.svg')}}" class="iconoHistorial">
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">

			<div class="col-md-2">
				<input type="number" class="form-control" placeholder="Consecutivo" id="consecutivo_a_buscar">
			</div>

                        <div class="col-md-3 mb-1">
                            <!--<input type="date" class="form-control" id="fechaInicio">-->
                            <select name="rangoFechas" id="rangoFechas" class="form-control">
                                <option value="0" selected>Seleccionar una fecha de búsqueda</option>
                                <option value="1">Hoy</option>
                                <option value="2">Ayer</option>
                                <option value="3" onclick="DespliegueModal()">Otro</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <input type="text" id="codCliente" name="codCliente" list="dataCliente" class="form-control" placeholder="Seleccionar cliente">

                            <datalist id="dataCliente">
                                <option selected value="Seleccionar cliente">Seleccionar cliente</option>
                                @foreach ($clientes as $item)
                                    <option value="{{$item->razonSocial}}">{{$item->razonSocial}}</option>
                                @endforeach
                            </datalist>

                            <datalist id="dataCliente2">
                                <option selected value="Seleccionar cliente">0</option>
                                @foreach ($clientes as $item)
                                    <option value="{{$item->razonSocial}}">{{$item->id}}</option>
                                @endforeach
                            </datalist>
                        </div>
    
                        <div class="col-md-2 mb-3">

                            <select class="custom-select d-block w-100" id="tipoPresentacion">
                                <option selected value="1">Filtrar por:</option>
                                <option value="1">Pedido</option>
                                <option value="2">Producto</option>
                            </select>
                        </div>
    
                        <div class="col-md-2 mb-3">
                            <button type="button" class="btn btn-success" id="filtrarPedidos">Buscar</button>
                        </div>
                    </div>
                </div>
    
                <div class="table-responsive-xl">
                    <table class="table table-bordered table-hover text-center generarBorde" id="generarBorde">

                        <thead class="thead-dark" id="CambiarColor">
                            <tr>
                                <th scope="col">Consecutivo</th>
                                <th scope="col">Fecha Entrega</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Estado</th>
				<th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="BodyTablePedidos">
                            <?php $i = 0; ?>
                            @if ($pedidos[0] != null)
                                @foreach ($pedidos as $pedido)
                                    @if ($pedido->estado == 1)
                                        <tr>
                                            <th scope="row">
                                                <button type="button" id="botonHistorial2<?php echo $i; ?>" data-toggle="modal"
                                            data-target=".bd-modal-example-xl" value="{{$pedido->id}}" onclick="SendQuery(this)">
                                                    <p style="color: rgb(223, 1, 1);"><b>PD{{$pedido->id}}</b></p>
                                                </button>
                                            </th>
                                            <td id="fecha<?php echo $i; ?>">{{$pedido->fechaEntrega}}
                                                <!--<script type="text/javascript">
                                                    var fecha = '<?php //echo $pedido->fechaSolicitud; ?>';
                                                    var contador = '<?php //echo $i; ?>';
                                                    var fechaCortada = fecha.substring(0, 10);
                                                    document.getElementById("fecha"+contador).innerHTML = fechaCortada;
                                                </script>-->

                                            </td>
                                            <td>{{$pedido->razonSocial}}</td>
                                            <td>Por listar</td>
					    <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                          Acciones
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item" href="{{ url('/editPedido', ['ConsecutivoPedido' => $pedido->id]) }}">Editar pedido</a>
                                                        </div>
                                                    </div>
					    </td>
                                        </tr>
                                        <?php $i++; ?>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                    
                    <div id="livesearch">
                        
                    </div>

                    <div class="d-flex justify-content-end mr-2" id="PaginacionConsulta">
                        {{$pedidos->links()}}
                    </div>
                        <button type="button" id="DesplegarErrorModal" class="btn btn-danger" data-target="#errorModal" data-toggle="modal">
                                Error
                        </button>
                </div>

    <!--Modal de descripción del pedido Por alistar-->

    <div class="modal fade bd-modal-example-xl" id="exampleModal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Confirmar Pedido</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                    <div class="table-responsive-xl">
                        <table class="table table-bordered table-hover text-center generarBorde" id="generarBorde">
                            <thead class="thead-dark" id="CambiarColor">
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad Solicitada</th>
                                    <th scope="col">Unidad de medida</th>
                                    <th scope="col">Cantidad Disponible</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos">

                            </tbody>
                        </table>
                    </div>
            </div>


            <div class="modal-footer">
                <form action="{{route('updateSolicitud')}}" method="post" id="updateData">
                    @csrf
                </form>
                <button type="button" class="btn btn-success" id="ProductoAlistado">Alistado</button>
            </div>
        </div>
    </div>
</div>


<!--Modal del historial de pedidos-->

<div class="modal fade bd-example-modal-xl" id="exampleModal" tabindex="-1" role="dialog" style="overflow-y:scroll;" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Historial</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div class="row">

			<div class="col-md-2">
				<input type="number" placeholder="Consecutivo" id="consecutivo_a_buscar_historial" class="form-control">
			</div>

                    <div class="col-md-4 mb-1">
                        <select name="rangoFechasHistorial" id="rangoFechasHistorial" class="form-control">
                            <option value="0" selected>Seleccionar una fecha de búsqueda</option>
                            <option value="1">Hoy</option>
                            <option value="2">Ayer</option>
                            <option value="3">Otro</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">

                        <input type="text" id="codClienteHistorial" name="codCliente" list="dataClientesHistorial" class="form-control" placeholder="Seleccionar cliente">

                        <datalist id="dataClientesHistorial">
                            <option value="Seleccionar cliente">Seleccionar cliente</option>
                            @foreach ($clientes as $item)
                                <option value="{{$item->razonSocial}}">{{$item->razonSocial}}</option>
                            @endforeach
                        </datalist>

                        <datalist id="dataClientesHistorial2">
                                <option value="Seleccionar cliente">Seleccionar cliente</option>
                                @foreach ($clientes as $item)
                                    <option value="{{$item->razonSocial}}">{{$item->id}}</option>
                                @endforeach
                            </datalist>
                    </div>


                    <div class="col-md-2 mb-3">

                        <select class="custom-select d-block w-100" id="tipoPresentacionHistorial">
                            <option selected value="1">Filtrar por:</option>
                            <option value="1">Pedido</option>
                            <option value="2">Producto</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button type="button" class="btn btn-success" id="filtrarPedidosHistorial">Buscar</button>
                    </div>
                </div>

                    <div class="table-responsive-xl">
                        <table class="table table-bordered table-hover text-center generarBorde" id="HistorialTable">
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
                            <tbody id="tBodyHistorial">
                                @if ($pedidos2[0] != null)
                                        @foreach ($pedidos2 as $pedido2)
                                        @if ($pedido2->estado == 2)
                                            <tr>
                                                <th scope="row">
                                                    <button type="button" id="botonHistorial2<?php echo $i; ?>" data-toggle="modal"
                                                    data-target="#descipcionHistorial" value="{{$pedido2->id}}" onclick="ShowInfoHistorial(this)">
                                                        <p style="color: rgb(223, 1, 1)"><b>PD{{$pedido2->id}}</b></p>
                                                    </button>
                                                </th>
                                                <td id="fecha<?php echo $i; ?>">{{$pedido2->fechaSolicitud}}
                                                    <script type="text/javascript">
                                                        var fecha = '<?php echo $pedido2->fechaSolicitud; ?>';
                                                        var contador = '<?php echo $i; ?>';
                                                        var fechaCortada = fecha.substring(0, 10);
                                                        document.getElementById("fecha"+contador).innerHTML = fechaCortada;
                                                    </script>

                                                </td>
                                                <td>{{$pedido2->fechaEntrega}}</td>
                                                <td>{{$pedido2->razonSocial}}</td>
                                                <td>Alistado</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                          Acciones
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item" href="{{ url('/editPedido', ['ConsecutivoPedido' => $pedido2->id]) }}">Editar pedido</a>

							   <a class="dropdown-item" href="{{ url('/GenerarCSV', ['ConsecutivoPedido' => $pedido2->id]) }}">Generar CSV</a>

                                                            <a class="dropdown-item" href="{{ url('/GenerarPDF', ['ConsecutivoPedido' => $pedido2->id]) }}" target="_blank">Generar PDF</a>

                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php $i++; ?>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mr-2" id="footerHistorial">
                            {{$pedidos2->links()}}
                        </div>

                        <div class="row d-flex justify-content-end mr-1">
                            <div class="ml-3 mb-2">

                                <form action="{{route('PDFGeneral')}}" method="post" id="pdfGeneral">
                                    {{ csrf_field() }}
                                </form>
                                <form action="{{route('CSVcreation')}}" method="post" id="CSVcreation">
                                    {{ csrf_field() }}
                                </form>
                                <button type="button" class="btn btn-secondary btn-lg" id="GlobalGenerarPDF" disabled>Generar PDF</button>
                                <button type="button" class="btn btn-secondary btn-lg" id="GlobalGenerarCSV" disabled>Generar CSV</button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón que activa la modal de fechas -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalRangoFechas" id="btnDespliegueRangoFechas">
    Launch demo modal
  </button>
  
  <!-- Modal de rango de fechas -->
  <div class="modal fade" id="modalRangoFechas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Selecciona un rango de fechas</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="contentRangoFechasModal">
            <p><b>Fecha de inicio:</b></p>
            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control">
            <p><b>Fecha limite:</b></p>
            <input type="date" name="fechaFin" id="fechaFin" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal" id="closeModalRangoFechas">Cancelar</button>
          <button type="button" class="btn btn-success" id="RangoFechasFiltroSuccess">Filtrar</button>
        </div>
      </div>
    </div>
  </div>

    <!--Modal de descripción del pedido Alistado-->

<div class="modal fade bd-modal-example-xl" id="descipcionHistorial" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="exampleModalLabel">Detalles del pedido Historial</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                    <div class="table-responsive-xl">
                        <table class="table table-bordered table-hover text-center" id="generarBorde">
                            <thead class="thead-dark" id="CambiarColor">
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad Solicitada</th>
                                    <th scope="col">Unidad de medida</th>
                                    <th scope="col">Cantidad Despachada</th>
                                </tr>
                            </thead>
                            <tbody id="tablaHistorial">

                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!--Modal para errores-->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alerta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2 id="textInfo"></h2>
                </div>
                <div id="form"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cerrar">Ok</button>
                </div>
            </div>
        </div>
    </div>

<!--Modal cambio de consecutivo-->
<div class="modal fade" id="modal_consecutivo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background:rgb(231,1,1);">
        <h5 class="modal-title" id="exampleModalLabel" style="color:white;">Modificar Consecutivo</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="body_modal_consecutivo">
 	<input type="number" class="form-control" min="11700" id="input_update_consecutivo" placeholder="Ingresa el nuevo consecutivo">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" id="btn_cancelar_update">Cancelar</button>
        <button type="button" class="btn btn-success" id="btn_update_consecutivo">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script>


function SendQuery(obj){
    localStorage.setItem("PedidoSeleccionado", obj.value);
    getContent(obj);
}

function getContent(obj){
    
    var tableBody = document.getElementById('tablaProductos');

var searchValue = obj.value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET','{{route('sendConsulta')}}/?search=' + searchValue ,true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function() {
        
        if(xhr.readyState == 4 && xhr.status == 200)
        {   
            tableBody.innerHTML = xhr.responseText;
        }
    }
    xhr.send();
}

function ShowInfoHistorial(obj){
    getData(obj);
}

function getData(obj){
    
    var tableBody = document.getElementById('tablaHistorial');

var searchValue = obj.value;

    const xhr = new XMLHttpRequest();
    xhr.open('GET','{{route('showProductHistorial')}}/?search=' + searchValue ,true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function() {
        
        if(xhr.readyState == 4 && xhr.status == 200)
        {   
            tableBody.innerHTML = xhr.responseText;
        }
    }
    xhr.send();
}



</script>

<script src="{{asset('js/scriptConsulta.js')}}"></script>
<script src="{{asset('js/filtroConsultas.js')}}"></script>
<script src="{{asset('js/filtroHistorial.js')}}"></script>
<script src="{{asset('js/filtro_consecutivo.js')}}"></script>
<script src="{{asset('js/actualizar_consecutivo.js')}}"></script>
<script src="{{asset('js/destino_caba.js')}}"></script>
@endsection
