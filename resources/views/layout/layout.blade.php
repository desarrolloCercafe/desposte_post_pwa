<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{asset('css/solicitud.css')}}">
    <link rel="stylesheet" href="{{asset('css/consulta.css')}}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('node_modules/bootstrap/dist/css/bootstrap.min.css')}}">

    <title>Desposte</title>
  </head>
  <body>
      
    <div class="container-fluid">
        <div class="card mt-3">
            @yield('content')
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="{{asset('node_modules/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('node_modules/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('node_modules/bootstrap/dist/js/bootstrap.min.js')}}"></script>

    <script>    

      //window.onload = verificacionDeSessionStorage()

      document.getElementById('btnCerrarSession').addEventListener('click', function(){

        localStorage.removeItem("nombre");
        localStorage.removeItem("productos");
        
        if(localStorage.getItem("PedidoSeleccionado")){
            localStorage.removeItem("PedidoSeleccionado");
        }

      });

    </script>


  </body>
</html>
