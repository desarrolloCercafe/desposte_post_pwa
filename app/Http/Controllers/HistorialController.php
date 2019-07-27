<?php

namespace App\Http\Controllers;

//use App\Http\Controllers\DateTime;
use Illuminate\Http\Request;
use App\Cliente;
use App\Pedido;
use App\Solicitud;
use DB;
use PDF;
use DateTime;

class HistorialController extends Controller
{
    public function FiltrarHistorial(Request $request){

        header("Content-Type: application/json");
        $filtros = json_decode(stripslashes(file_get_contents("php://input")));
        // build a PHP variable from JSON sent using GET method
        $filtros = json_decode(stripslashes($request->data));
        // encode the PHP variable to JSON and send it back on client-side
        $datosFiltrados = "";

        
        if(isset($filtros->fechaInicioH) && isset($filtros->codCliente) && isset($filtros->tipoPresentacion)){

            //Consulta ejecutada normalmente
            if($filtros->tipoPresentacion == 1){
                $datosFiltrados = DB::select(
                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                    FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id 
                    AND ped.codCliente = ? AND ped.fechaEntrega BETWEEN ? AND ? ', 
                    [$filtros->codCliente, $filtros->fechaInicioH, $filtros->fechaFin]
                );
            }else{
                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo 
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido 
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE 
                    cl.id = ? AND ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 2 ', 
                    [$filtros->codCliente, $filtros->fechaInicioH, $filtros->fechaFin]
                );
            }

        }elseif(isset($filtros->fechaInicioH) && isset($filtros->codCliente)){

            $datosFiltrados = DB::select(
                'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id 
                AND ped.codCliente = ? AND ped.fechaEntrega BETWEEN ? AND ? ', 
                [$filtros->codCliente, $filtros->fechaInicioH, $filtros->fechaFin]
            );

        }elseif(isset($filtros->fechaInicioH) && isset($filtros->tipoPresentacion)){

            //Consulta ejecutada normalmente
            if($filtros->tipoPresentacion == 1){
                $datosFiltrados = DB::select(
                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                    FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id 
                    AND ped.fechaEntrega BETWEEN ? AND ? ', 
                    [$filtros->fechaInicioH, $filtros->fechaFin]
                );
            }else{
                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                    ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 2 ', 
                    [$filtros->fechaInicioH, $filtros->fechaFin]
                );
            }

        }elseif(isset($filtros->codCliente) && isset($filtros->tipoPresentacion)){
            
            //Consulta ejecutada normalmente
            if($filtros->tipoPresentacion == 1){
                $datosFiltrados = DB::select(
                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                    FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id 
                    AND ped.codCliente = ?', 
                    [$filtros->codCliente]
                );
            }else{
                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo 
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido 
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE 
                    cl.Nit = ? AND ped.estado = 2  ', 
                    [$filtros->codCliente]
                );
            }


        }elseif(isset($filtros->fechaInicioH)){

            $datosFiltrados = DB::select(
                'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id 
                AND ped.fechaEntrega BETWEEN ? AND ? ', 
                [$filtros->fechaInicioH, $filtros->fechaFin]
            );

        }elseif(isset($filtros->codCliente)){

            $datosFiltrados = DB::select(
                'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.Nit 
                AND ped.codCliente = ?', 
                [$filtros->codCliente]
            );

        }elseif(isset($filtros->tipoPresentacion)){

            //Consulta ejecutada normalmente
            if($filtros->tipoPresentacion == 1){
                $datosFiltrados = DB::select(
                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                    FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id ', 
                    []
                );
            }else{
                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod 
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo 
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido 
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                    ped.estado = 2 ', 
                    []
                );
            }
        }

        if(Count($datosFiltrados) == 0){
            $datosFiltrados = "no hay resultados";
        }

        return json_encode($datosFiltrados);
    }

    public function CSVGeneral(Request $request){

        if(request('fechaInicioH')){
            $fechaInicioH = request('fechaInicioH');
            $fechaFin = request('fechaFin');
        }

        if(request('codCliente')){
            $codCliente = request('codCliente');
        }

        $datosFiltrados = "nada";

        if(isset($fechaInicioH) && isset($codCliente)){

                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo 
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido 
                    INNER JOIN cliente as cl ON cl.Nit = ped.codCliente WHERE 
                    cl.id = ? AND ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 2 ', 
                    [$codCliente, $fechaInicioH, $fechaFin]
                );

        }elseif(isset($fechaInicioH)){

                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                    ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 2 ', 
                    [$fechaInicioH, $fechaFin]
                );

        }elseif(isset($codCliente)){
            
                $datosFiltrados = DB::select(
                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo 
                    INNER JOIN pedido as ped ON ped.id = sol.codPedido 
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE 
                    cl.id = ? AND ped.estado = 2  ', 
                    [$codCliente]
                );
        }

            $salida = fopen('php://output', 'w');

            $separador = ";";

            header('Content-Type:text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="Reporte_Fechas_Ingreso.csv"');

            foreach ($datosFiltrados as $key) {
                fputcsv($salida, array($key->codigo,
                                        $key->NombreProd,
                                        $key->cantidadSolicitada,
                                        $key->cantidadDespachada,
                                        $key->unidadMedida,
                                        $key->NombreCl,
                                        $key->id), $separador);
            }

        
    }

    public function PDFGeneral(Request $request){

        if(request('fechaInicioH')){
            $fechaInicioH = request('fechaInicioH');
            $fechaFin = request('fechaFin');
        }

        if(request('codCliente')){
            $codCliente = request('codCliente');
        }

        //datosFiltrados = "nada";

        if(isset($fechaInicioH) && isset($codCliente)){

            $pdf = \App::make('dompdf.wrapper');

            $pdf->loadHTML($this->LoadTableGeneral($fechaInicioH, $fechaFin, $codCliente, 0));
    
            return $pdf->stream();

        }elseif(isset($fechaInicioH)){

            $pdf = \App::make('dompdf.wrapper');

            $pdf->loadHTML($this->LoadTableGeneral($fechaInicioH, $fechaFin, null, 1));
    
            return $pdf->stream();

        }elseif(isset($codCliente)){
            
            $pdf = \App::make('dompdf.wrapper');

            $pdf->loadHTML($this->LoadTableGeneral(null, null, $codCliente, 2));
    
            return $pdf->stream();
        }
    }

    public function LoadTableGeneral($fechaInicioH, $fechaFin, $codCliente, $tipoPDF){

        //Con código cliente y rango de fechas
        if($tipoPDF == 0){
            $DetallePedido = DB::select(
                'SELECT razonSocial FROM cliente WHERE id = ?', [$codCliente]);
    
            $DescripcionPedido = DB::select(
                        'SELECT sol.codPedido, prod.nombre, sol.cantidadSolicitada,
                        sol.cantidadDespachada, sol.unidadMedida, ped.fechaSolicitud, 
                        ped.fechaEntrega FROM pedido as ped 
                        INNER JOIN solicitud as sol ON ped.id = sol.codPedido 
                        INNER JOIN producto as prod ON prod.codigo = sol.codProducto 
                        WHERE ped.codCliente = ? AND ped.fechaEntrega 
                        BETWEEN ? AND ? AND ped.estado = 2 ORDER BY (ped.id)', [$codCliente, $fechaInicioH, $fechaFin]);
    
            $TotalesPedidos = DB::select(
                        'SELECT COUNT(solicitud.codProducto) as sumSolicitud, 
                        SUM(solicitud.cantidadSolicitada) as sumCantSol,
                        SUM(solicitud.cantidadDespachada) as sumCantDes
                        FROM solicitud, pedido WHERE pedido.codCliente = ?
                        AND pedido.fechaEntrega BETWEEN ? AND ?
                        AND pedido.estado = 2', [$codCliente, $fechaInicioH, $fechaFin]);
    
            $output = '
            <style>    
            .tittle{
                float: right;
                margin-top: 25px;
                margin-bottom: 35px;
            }
    
            .bottom{
                margin-top: 35px;
            }
    
            .img{
                float: left;
            }
    
            .content{
                margin-left: 210px;
            }
    
            .tableContent{
                width: 100%;
                border: 1px solid black;
                border-collapse: collapse;
            }
    
            th{
                text-align: left;
                border: 1px solid black;
                background-color: red;
                color: white;
                height: 25px;
            }
    
            td{
                border: 1px solid black;
                text-align: right;
            }
    
            .right{
                text-align:right;
            }
    
            .productName{
                text-align: left;
            }

            .centrar{
                text-align:center;
            }
            </style>
    
            <img src="svg/logo.png" width="250px" height="200px" class="img">
            <span class="tittle">
            <h1>Informe sobre Pedidos del desposte</h1>
            <p>-Cliente: <b> '.strtoupper($DetallePedido[0]->razonSocial).'</b></p>
            <p>-Desde: <b>'.$fechaInicioH.'</b> // Hasta: <b>'.$fechaFin.'</b></p>
            </span>

            <p>
            ----------------------------------------------------------------------------------------------------------------------------------------
            </p>
            <h4>Detalle pedidos discriminados:</h4>
    
            <table class="tableContent">
            <thead>
                <tr>
                <th>Cons. Ped.</th>
                <th>Nombre Producto</th>
                <th>Cant.Sol</th>
                <th>Cant.Des</th>
                <th>U/M</th>
                <th>F. Solicitud</th>
                <th>F. Entrega</th>
                </tr>
            </thead>
            <tbody>';
    
            foreach($DescripcionPedido as $itemPedido){

                $fecha = date_format(new DateTime($itemPedido->fechaSolicitud),'d/m/Y');
                $fecha2 = date_format(new DateTime($itemPedido->fechaEntrega),'d/m/Y');

            $output .= '
            <tr>
            <td class="centrar">'.$itemPedido->codPedido.'</td>
            <td class="productName">'.$itemPedido->nombre.'</td>
            <td>'.$itemPedido->cantidadSolicitada.'</td>
            <td>'.$itemPedido->cantidadDespachada.'</td>
            <td>'.$itemPedido->unidadMedida.'</td>
            <td>'.$fecha.'</td>
            <td>'.$fecha2.'</td>
            </tr>
            ';
            }
    
            $output .= '
            </tbody>
            </table>
    
            <span class="right">
            <p>Cantidad total de productos: <b>'.$TotalesPedidos[0]->sumSolicitud.'</b></p>
            <p>Total unidades solicitadas: <b>'.$TotalesPedidos[0]->sumCantSol.'</b></p>
            <p>Total unidades despachadas: <b>'.$TotalesPedidos[0]->sumCantDes.'</b></p>
            </span>';

        //Sólo con un rango de fechas
        }else if($tipoPDF == 1){
    
            $DescripcionPedido = DB::select(
                'SELECT sol.codPedido, cl.razonSocial AS NombreCliente, prod.nombre AS NombreProducto, 
                    sol.cantidadSolicitada, sol.cantidadDespachada, sol.unidadMedida, ped.fechaSolicitud, 
                    ped.fechaEntrega FROM pedido as ped 
                    INNER JOIN solicitud as sol ON ped.id = sol.codPedido 
                    INNER JOIN producto as prod ON prod.codigo = sol.codProducto 
                    INNER JOIN cliente as cl ON cl.id = ped.codCliente 
                    WHERE ped.fechaEntrega BETWEEN ? AND ? 
                    AND ped.estado = 2 ORDER BY (ped.id)', [$fechaInicioH, $fechaFin]);
    
            $TotalesPedidos = DB::select(
                            'SELECT COUNT(solicitud.id) as sumSolicitud, 
                            SUM(solicitud.cantidadSolicitada) as sumCantSol, 
                            SUM(solicitud.cantidadDespachada) as sumCantDes 
                            FROM solicitud, pedido WHERE pedido.fechaEntrega 
                            BETWEEN ? AND ? AND pedido.Estado = 2 
                            AND pedido.id = solicitud.codPedido', [$fechaInicioH, $fechaFin]);
    
            $output = '
            <style>    
            .tittle{
            float: right;
            margin-top: 25px;
            margin-bottom: 35px;
            }
    
            .bottom{
            margin-top: 35px;
            }
    
            .img{
            float: left;
            }
    
            .content{
            margin-left: 210px;
            }
    
            .tableContent{
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            }
    
            th{
            text-align: left;
            border: 1px solid black;
            background-color: red;
            color: white;
            height: 25px;
            }
    
            td{
            border: 1px solid black;
            text-align: right;
            }
    
            .right{
            text-align:right;
            }
    
            .productName{
            text-align: left;
            }

            .centrar{
                text-align:center;
            }
            </style>
    
            <img src="svg/logo.png" width="250px" height="170px" class="img">
            <span class="tittle">
            <h1>Informe sobre Pedidos del desposte</h1>
            <p>Desde: <b>'.$fechaInicioH.'</b> // Hasta: <b>'.$fechaFin.'</b></p>
            </span>

            <p>
            ----------------------------------------------------------------------------------------------------------------------------------------
            </p>

            <h4>Detalle pedidos discriminados:</h4>
    
            <table class="tableContent">
            <thead>
            <tr>
                <th class="centrar">Cons. Ped.</th>
                <th class="centrar">Nombre Cliente</th>
                <th class="centrar">Nombre Producto</th>
                <th class="centrar">Cant.Sol</th>
                <th class="centrar">Cant.Des</th>
                <th class="centrar">U/M</th>
                <th class="centrar">F. Solicitud</th>
                <th class="centrar">F. Entrega</th>
            </tr>
            </thead>
            <tbody>';
    
            foreach($DescripcionPedido as $itemPedido){
                
                $fecha = date_format(new DateTime($itemPedido->fechaSolicitud),'d/m/Y');
                $fecha2 = date_format(new DateTime($itemPedido->fechaEntrega),'d/m/Y');

            $output .= '
            <tr>
                <td class="centrar">'.$itemPedido->codPedido.'</td>
                <td class="productName">'.$itemPedido->NombreCliente.'</td>
                <td class="productName">'.$itemPedido->NombreProducto.'</td>
                <td>'.$itemPedido->cantidadSolicitada.'</td>
                <td>'.$itemPedido->cantidadDespachada.'</td>
                <td>'.$itemPedido->unidadMedida.'</td>
                <td>'.$fecha.'</td>
                <td>'.$fecha2.'</td>
            </tr>
            ';
            }
    
            $output .= '
            </tbody>
            </table>
    
            <span class="right">
            <p>Cantidad total de productos: <b>'.$TotalesPedidos[0]->sumSolicitud.'</b></p>
            <p>Total unidades solicitadas: <b>'.$TotalesPedidos[0]->sumCantSol.'</b></p>
            <p>Total unidades despachadas: <b>'.$TotalesPedidos[0]->sumCantDes.'</b></p>
            </span>';

        //Solamente con el código del cliente
        }else if($tipoPDF == 2){

            $DetallePedido = DB::select(
                'SELECT razonSocial FROM cliente WHERE id = ?', [$codCliente]);

            $DescripcionPedido = DB::select(
                        'SELECT sol.codPedido, prod.nombre, sol.cantidadSolicitada,
                        sol.cantidadDespachada, sol.unidadMedida, ped.fechaSolicitud, 
                        ped.fechaEntrega FROM pedido as ped 
                        INNER JOIN solicitud as sol ON ped.id = sol.codPedido 
                        INNER JOIN producto as prod ON prod.codigo = sol.codProducto 
                        WHERE ped.codCliente = ? AND ped.estado = 2 
                        ORDER BY (ped.id)', [$codCliente]);
    
            $TotalesPedidos = DB::select(
                        'SELECT COUNT(solicitud.codProducto) as sumSolicitud,
                        SUM(solicitud.cantidadSolicitada) as sumCantSol, 
                        SUM(solicitud.cantidadDespachada) as sumCantDes FROM solicitud, pedido 
                        WHERE pedido.codCliente = ? AND pedido.estado = 2 
                        AND solicitud.codPedido = pedido.id', [$codCliente]);
    
            $output = '
            <style>    
            .tittle{
            float: right;
            margin-top: 25px;
            margin-bottom: 35px;
            }
    
            .bottom{
            margin-top: 35px;
            }
    
            .img{
            float: left;
            }
    
            .content{
            margin-left: 210px;
            }
    
            .tableContent{
            width: 100%;
            border: 1px solid black;
            border-collapse: collapse;
            }
    
            th{
            text-align: left;
            border: 1px solid black;
            background-color: red;
            color: white;
            height: 25px;
            }
    
            td{
            border: 1px solid black;
            text-align: right;
            }
    
            .right{
            text-align:right;
            }
    
            .productName{
            text-align: left;
            }

            .centrar{
                text-align:center;
            }
            </style>
    
            <img src="svg/logo.png" width="250px" height="170px" class="img">
            <span class="tittle">
            <h1>Informe sobre Pedidos del desposte</h1>
            <p>Cliente: <b> '.strtoupper($DetallePedido[0]->razonSocial).'</b></p>
            </span>
               
            <p>
            ----------------------------------------------------------------------------------------------------------------------------------------
            </p>

            <h4>Detalle pedidos discriminados:</h4>

            <table class="tableContent">
            <thead>
            <tr>
                <th class="centrar">Cons.Ped.</th>
                <th class="centrar">Nombre Producto</th>
                <th class="centrar">Cant.Sol</th>
                <th class="centrar">Cant.Des</th>
                <th class="centrar">U/M</th>
                <th class="centrar">F. Solicitud</th>
                <th class="centrar">F. Entrega</th>
            </tr>
            </thead>
            <tbody>';
    
            foreach($DescripcionPedido as $itemPedido){
            
                $fecha = date_format(new DateTime($itemPedido->fechaSolicitud),'d/m/Y');
                $fecha2 = date_format(new DateTime($itemPedido->fechaEntrega),'d/m/Y');

                $output .= '
                <tr>
                <td class="centrar">'.$itemPedido->codPedido.'</td>
                <td class="productName">'.$itemPedido->nombre.'</td>
                <td>'.$itemPedido->cantidadSolicitada.'</td>
                <td>'.$itemPedido->cantidadDespachada.'</td>
                <td>'.$itemPedido->unidadMedida.'</td>
                <td>'.$fecha.'</td>
                <td>'.$fecha2.'</td>
                </tr>
                ';
            }
    
            $output .= '
            </tbody>
            </table>

            <p>
            ----------------------------------------------------------------------------------------------------------------------------------------
            </p>
    
            <span class="right">
            <p>Cantidad total de productos: <b>'.$TotalesPedidos[0]->sumSolicitud.'</b></p>
            <p>Total unidades solicitadas: <b>'.$TotalesPedidos[0]->sumCantSol.'</b></p>
            <p>Total unidades despachadas: <b>'.$TotalesPedidos[0]->sumCantDes.'</b></p>
            </span>';
        }

        return $output;
    }
}
