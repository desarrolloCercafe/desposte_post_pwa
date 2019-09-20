@extends('layout.layout')
    <nav id="navStyle">
        <section class="BtnsRedireccion">
            <a href="#" class="btn btn-light mt-2" id="back">Solicitud</a>
            <a href="{{url('/consulta')}}" class="btn btn-outline-light mt-2 ml-3" id="back">Ir a Consulta</a>
        </section>
        <section class="BtnCerrarSesion">
            <a class="btn btn-light my-2 my-sm-0" id="btnCerrarSession" href="{{url('/')}}">
                <img src="{{asset('svg/powerOff.svg')}}" alt="Cerrar Sesión" width="15px" height="15px">
            </a>
        </section>
    </nav>
@section('content')


<!--Modal para la agregación de productos-->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Producto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2>Selecciona El producto que deseas añadir:</h2>
                        <input type="text" class="form-control" placeholder="Seleccionar Producto" list="datalistProductos" id="selectProducto">
                        <datalist id="datalistProductos">
                            @foreach ($productos as $producto)
                                <option value="{{$producto->codigo}},{{$producto->nombre}}">{{$producto->nombre}}</option>
                            @endforeach
                        </datalist>
                </div>
                <div id="form">
                    <form action="{{route('solicitud.store')}}" method="post" id="formInsert">
                        @csrf
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal" id="cerrar">Cerrar</button>
                    <button type="button" class="btn btn-success" id="addProducto" >Agregar</button>
                </div>
            </div>
        </div>
    </div>
<!--Interfaz general del sistema-->
<h3 class="card-header" id="head">Solicitud</h3>
<input type="hidden" id="vendedor">
<div class="card-body">
    <div class="row">

        <div class="col-md-3 mb-3" id="contentInput">
            <input type="text" class="form-control" placeholder="Seleccionar cliente" list="datalistClientes" id="selectCliente">


            <datalist id="datalistClientes">
                @foreach ($clientes as $item)
                    <option value="{{$item->razonSocial}}">{{$item->razonSocial}}</option>
                @endforeach
            </datalist>

        </div>

        <div class="col-md-3 mb-3">
            <input type="date" class="form-control" id="fechaEntrega">
        </div>

        <div class="col-md-3 mb-3">
            <button type="button" class="btn btn-success" data-toggle="modal"
            data-target="#exampleModal" id="addDesplegable" ><b>+</b>Agregar Producto</button>
        </div>
    </div>
</div>
<div class="table-responsive-xl">
    <table class="table table-bordered table-hover text-center" id="borde" >
        <thead class="thead-dark" id="color">
            <tr>
                <th scope="col">Código</th>
                <th scope="col">Producto</th>
                <th scope="col">Cantidad</th>
                <th scope="col">Unidad</th>
                <th scope="col">Acción</th>
            </tr>
        </thead>
            <tbody id="tbodyProductos">
            </tbody>
    </table>
</div>

<div class="float-sm-right" id="PieDePagina">
    <button type="button" class="btn btn-warning float-sm-right mr-2 mb-2 ml-2" id="botonGenerarPedido"><b>Generar Pedido</b></button>

</div>


<!--Modal de la eliminación de un producto-->
<div class="modal fade" id="ModalDelete" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Eliminar</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="bodyDelete">
          <h2>¿Deseas eliminar el producto?</h2>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal" id="cerrarDelete">Cancelar</button>
            <button type="submit" class="btn btn-danger" id="deleteButton">Eliminar</button>
        </div>
      </div>
    </div>
</div>

<!--Modal de creacion de pedidos-->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_pedido_enviado" style="display:none;" id="desplegar_modal_registro_pedido">
  Pedido Enviado
</button>

<!-- Modal -->
<div class="modal fade" id="modal_pedido_enviado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registro de Pedido</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <h3 class="font-weight-bold">Pedido Generado con éxito</h3>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>


<!--Modal de notificaciones sobre errores en las validaciones-->
<div class="modal fade" id="ValidationModal" tabindex="-1" role="dialog" aria-labelledby="ModalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Error en el pedido</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="bodyDelete">
          <h2 id="MessageValidation"></h2>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" id="BtnNotificacion" data-dismiss="modal">Ok</button>
        </div>
      </div>
    </div>
</div>

    <script src="{{asset('js/script.js')}}"></script>
    <script src="{{asset('js/snippet.js')}}"></script>
@endsection
