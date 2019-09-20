<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\DB;
use App\Cliente;
use App\Pedido;
use App\Solicitud;
use DB;
use PDF;
use DateTime;
use File;
class ConsultaController extends Controller
{
    /**/

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientes = Cliente::all();
        //$pedidos = Pedido::all();
        $pedidos = DB::table('pedido')
        ->join('cliente','pedido.codCliente','=', 'cliente.id')
        ->select('pedido.id', 'pedido.fechaEntrega', 'cliente.razonSocial', 'pedido.estado', 'pedido.codCliente')
        ->where('pedido.estado','=', 1)
        ->orderBy('pedido.id', 'DESC')
        ->paginate(5, ['*'], 'pedidos');

        $pedidos2 = DB::table('pedido')
        ->join('cliente','pedido.codCliente','=', 'cliente.id')
        ->select('pedido.id', 'pedido.fechaSolicitud', 'pedido.fechaEntrega', 'cliente.razonSocial', 'pedido.estado', 'pedido.codCliente')
        ->where('pedido.estado','=', 2)
        ->orderBy('pedido.id', 'DESC')
        ->paginate(5, ['*'], 'historial');

        return view('consulta.index', compact('clientes', 'pedidos', 'pedidos2'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function consulta(Request $request)
    {
        if($request->ajax()){

            /*$solicitud = Solicitud::where('CodPedido', $request->search)->get();*/

            $solicitud = "";

            $ProductosSolicitados = DB::select('SELECT 
                                        solicitud.codProducto,
                                        producto.nombre,
                                        solicitud.cantidadSolicitada,
                                        solicitud.unidadMedida
                                        FROM solicitud, producto 
                                        WHERE producto.codigo = solicitud.codProducto
                                        AND solicitud.codPedido = ? ', [$request->search]);

            if($ProductosSolicitados){

                $i = 0;

                foreach ($ProductosSolicitados as $ProductoSolicitado) {

                    $solicitud.='<tr>'.
            
                    '<th scope="row" value='.$ProductoSolicitado->codProducto.' id="codProducto'.$i.'">'.$ProductoSolicitado->codProducto.'</th>'.
                    
                    '<td>'.$ProductoSolicitado->nombre.'</td>'.
                    
                    '<td>'.$ProductoSolicitado->cantidadSolicitada.'</td>'.
                    
                    '<td>'.$ProductoSolicitado->unidadMedida.'</td>'.

                    '<td><input type="number" id="cantidadDespachada'.$i.'" name="cantidadDespachada'.$i.'" class="form-control" step="0.01"></td>'.
                    
                    '</tr>';

                    $i++;
                }

            }

            return $solicitud; 
        }
    }

	public function data_offline(Request $request){

                header("Content-Type: application/json");
                $filtros = json_decode(stripslashes(file_get_contents("php://input")));
                // build a PHP variable from JSON sent using GET method
                $filtros = json_decode(stripslashes($request->data));
                $datos = "";

		$generales = DB::select('SELECT pedido.id, pedido.fechaEntrega, cliente.razonSocial, pedido.estado FROM pedido INNER JOIN cliente ON pedido.codCliente = cliente.id WHERE pedido.estado = 1', []);

		$especifico = DB::select('SELECT solicitud.codPedido, producto.nombre, solicitud.cantidadSolicitada, solicitud.unidadMedida FROM solicitud INNER JOIN producto ON solicitud.codProducto = producto.codigo INNER JOIN pedido ON pedido.id = solicitud.codPedido WHERE pedido.estado = 1',[]);

		$datos = [$generales, $especifico];

		return json_encode($datos);
	}

	public function update_consecutivo(Request $request){

	        header("Content-Type: application/json");
	        $filtros = json_decode(stripslashes(file_get_contents("php://input")));
        	// build a PHP variable from JSON sent using GET method
	        $filtros = json_decode(stripslashes($request->data));
	        $datosFiltrados = "";

		DB::update('UPDATE contador SET valor = ? WHERE id = ?', [$filtros->consecutivo, 2]);

	}

	public function update_offline(Request $request){

                header("Content-Type: application/json");
                $filtros = json_decode(stripslashes(file_get_contents("php://input")));
                // build a PHP variable from JSON sent using GET method
			$filtros = json_decode(stripslashes($request->data));
			if($filtros == "no_data"){
				return json_encode("no_data");
			}
        	        $cantidadUpdates = Count($filtros);

	                for($i = 0; $i < $cantidadUpdates; $i++){

                        	$cantidadProductos = Count($filtros[$i]->productos);

                	        for($j = 0; $j < $cantidadProductos; $j++){
        	                        $producto = trim($filtros[$i]->productos[$j], 'n');
	                                $codigo = DB::select('SELECT codigo FROM producto WHERE nombre LIKE "%'.$producto.'%" ', []);
                                	DB::update('UPDATE solicitud SET cantidadDespachada = ? WHERE codPedido = ? AND codProducto = ?', [$filtros[$i]->despachado[$j], $filtros[$i]->pedido, $codigo[0]->codigo]);
                        	}

                	        DB::update('UPDATE pedido SET estado = ? WHERE id = ?', [2, $filtros[$i]->pedido]);
        	        }

	                return json_encode("actualizado");
		/*$filtros = json_decode(stripslashes($request->data));

		$cantidadUpdates = Count($filtros);

		for($i = 0; $i < $cantidadUpdates; $i++){

			$cantidadProductos = Count($filtros[$i]->productos);

			for($j = 0; $j < $cantidadProductos; $j++){
				$producto = trim($filtros[$i]->productos[$j], 'n');
				$codigo = DB::select('SELECT codigo FROM producto WHERE nombre LIKE "%'.$producto.'%" ', []);
				//return json_encode($codigo[0]->codigo);
				DB::update('UPDATE solicitud SET cantidadDespachada = ? WHERE codPedido = ? AND codProducto = ?', [$filtros[$i]->despachado[$j], $filtros[$i]->pedido, $codigo[0]->codigo]);
			}

			DB::update('UPDATE pedido SET estado = ? WHERE id = ?', [2, $filtros[$i]->pedido]);
		}

		return json_encode("actualizado");*/
	}

    public function filtrar_por_consecutivo(Request $request){
        header("Content-type: application/json");
        $filtros = json_decode(stripslashes(file_get_contents("php://input")));
        $filtros = json_decode(stripslashes($request->consecutivo));

        $response = "";

	$response = DB::select(
		'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
		FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
		AND ped.id = ?',
         	[$filtros]
  		);
        /**/

	if(Count($response)){
		return json_encode($response);
	}else{
		$response = "no hay resultados";
		return json_encode($response);
	}

        //return json_encode($response);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProductHistorial(Request $request)
    {
        if($request->ajax()){

            /*$solicitud = Solicitud::where('CodPedido', $request->search)->get();*/

            $solicitud = "";
            
            $ProductosSolicitados = DB::select('SELECT 
                                        solicitud.codProducto,
                                        producto.nombre,
                                        solicitud.cantidadSolicitada,
                                        solicitud.unidadMedida,
                                        solicitud.cantidadDespachada
                                        FROM solicitud, producto 
                                        WHERE producto.codigo = solicitud.codProducto
                                        AND solicitud.codPedido = ? ', [$request->search]);

            if($ProductosSolicitados){

                $i = 0;

                foreach ($ProductosSolicitados as $ProductoSolicitado) {

                    $solicitud.='<tr>'.
            
                    '<th scope="row" value='.$ProductoSolicitado->codProducto.' id="codProducto'.$i.'">'.$ProductoSolicitado->codProducto.'</th>'.
                    
                    '<td>'.$ProductoSolicitado->nombre.'</td>'.
                    
                    '<td>'.$ProductoSolicitado->cantidadSolicitada.'</td>'.
                    
                    '<td>'.$ProductoSolicitado->unidadMedida.'</td>'.

                    '<td>'.$ProductoSolicitado->cantidadDespachada.'</td>'.
                    
                    '</tr>';

                    $i++;
                }

            }

            return $solicitud;
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Solicitud  $solicitud
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $solicitud = new Solicitud();

        $solicitud->despacho = json_decode(request('DespachoCantidad'));
        $json = $solicitud->despacho;
        $solicitud->pedido = request('CodPedido');

        $cantidad = Count($solicitud->despacho);

        for ($i=0; $i < $cantidad; $i++) { 
            DB::update(
                'UPDATE solicitud SET cantidadDespachada = ? WHERE codPedido = ? AND codProducto = ?', 
                [$json[$i]->cantidad, $solicitud->pedido, $json[$i]->codigo]
            );
        }

        DB::update(
            'UPDATE pedido SET estado = 2 where id = ?',
            [$solicitud->pedido]
        );

        return redirect()->route('consulta.index');
    }


    public function FiltrarTabla(Request $request){

        header("Content-Type: application/json");

        $filtros = json_decode(stripslashes(file_get_contents("php://input")));
        // build a PHP variable from JSON sent using GET method
        $filtros = json_decode(stripslashes($request->data));
        //$tipoBusqueda = $request->opcion;

        $datosFiltrados = "";

	if($filtros->tipoPresentacion == 1){

		if(isset($filtros->codCliente)){
			$codCliente = $filtros->codCliente;
		}

		if(isset($filtros->validarFecha)){

			if($filtros->validarFecha == 1){
				$hoy = $filtros->FechaHoy;
			}elseif($filtros->validarFecha == 2){
                                $fechaAyerNeta = $filtros->FechaAyer;
                                $ayer = strtotime('-1 day', strtotime($fechaAyerNeta));
                                $ayer = date('Y-m-j', $ayer);
			}else{
				$fechaInicio = $filtros->fechaInicio;
				$fechaFin = $filtros->fechaFin;
			}

		}

		if(isset($filtros->valorFecha) && isset($filtros->codCliente)){
                        if($filtros->validarFecha == 1){
                           $datosFiltrados = DB::select(
                                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                                    AND ped.codCliente = ? AND ped.fechaEntrega = ? ',
                                    [$codCliente, $hoy]
                            );

                        }elseif($filtros->validarFecha == 2){
                           $datosFiltrados = DB::select(
                                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                                    AND ped.codCliente = ? AND ped.fechaEntrega = ? ',
                                    [$codCliente, $ayer]
                            );
 
                        }else{
                            $datosFiltrados = DB::select(
		                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
		                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                		    AND ped.codCliente = ? AND ped.fechaEntrega BETWEEN ? AND ? ',
        	        	    [$codCliente, $fechaInicio, $fechaFin]
	                    );
                        }
		}elseif(isset($filtros->valorFecha)){
                        if($filtros->validarFecha == 1){
                            $datosFiltrados = DB::select(
                                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                                    AND ped.fechaEntrega = ? ',
                                    [$hoy]
                            );

                        }elseif($filtros->validarFecha == 2){
                            $datosFiltrados = DB::select(
                                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                                    AND ped.fechaEntrega = ? ',
                                    [$ayer]
                            );

                        }else{
                            $datosFiltrados = DB::select(
                                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                                    AND ped.fechaEntrega BETWEEN ? AND ? ',
                                    [$fechaInicio, $fechaFin]
                            );

                        }
		}elseif(isset($filtros->codCliente)){
                           $datosFiltrados = DB::select(
                                    'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                    FROM pedido as ped, cliente as cl WHERE ped.estado = 1 AND ped.codCliente = cl.id
                                    AND ped.codCliente = ?',
                                    [$codCliente]
                            );
		}

	}else{
                if(isset($filtros->codCliente)){
                        $codCliente = $filtros->codCliente;
                }

                if(isset($filtros->validarFecha)){

                        if($filtros->validarFecha == 1){
                                $hoy = $filtros->FechaHoy;
                        }elseif($filtros->validarFecha == 2){
                                $fechaAyerNeta = $filtros->FechaAyer;
                                $ayer = strtotime('-1 day', strtotime($fechaAyerNeta));
                                $ayer = date('Y-m-j', $ayer);
                        }else{
                                $fechaInicio = $filtros->fechaInicio;
                                $fechaFin = $filtros->fechaFin;
                        }

                }

                if(isset($filtros->valorFecha) && isset($filtros->codCliente)){

                        if($filtros->validarFecha == 1){
                                $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
                                            cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            cl.id = ? AND ped.fechaEntrega = ? AND ped.estado = 1 ',
                                            [$codCliente, $hoy]
                                );

                        }elseif($filtros->validarFecha == 2){
                                $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
                                            cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            cl.id = ? AND ped.fechaEntrega = ? AND ped.estado = 1 ',
                                            [$codCliente, $ayer]
                                );

                        }else{
                              $datosFiltrados = DB::select(
			                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
        			            cl.razonSocial as NombreCl, ped.id FROM producto as prod
			                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
			                    INNER JOIN pedido as ped ON ped.id = sol.codPedido
			                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
			                    cl.id = ? AND ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 1 ',
			                    [$codCliente, $fechaInicio, $fechaFin]
		                );
	      		}
                }elseif(isset($filtros->valorFecha)){

                        if($filtros->validarFecha == 1){
                                $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
                                            cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            ped.fechaEntrega = ? AND ped.estado = 1 ',
                                            [$hoy]
                                );

                        }elseif($filtros->validarFecha == 2){
                                $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
                                            cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            ped.fechaEntrega = ? AND ped.estado = 1 ',
                                            [$ayer]
                                );

                        }else{
                                $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
                                            cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 1 ',
                                            [$fechaInicio, $fechaFin]
                                );

                        }

                }elseif(isset($filtros->codCliente)){
                              $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.unidadMedida,
                                            cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            cl.id = ? AND ped.estado = 1 ',
                                            [$codCliente]
                                );
                }

	}
/**/

        if(Count($datosFiltrados) == 0){
            $datosFiltrados = "no hay resultados";
        }

        return json_encode($datosFiltrados);
    }

    public function GenerarCSV($consecutivo){

        $datosFiltrados = DB::select(
            'SELECT ped.fechaSolicitud, 
                    tipoCl.patronContable,
                    ped.id,
		    ped.consecutivo,
                    cl.nit,
                    cl.razonSocial,
                    tipoCl.formaPago,
                    prod.codigo,
                    sol.cantidadDespachada,
                    lprod.valorMayorista,
                    lprod.valorCebarte,
                    lprod.valorValencia,
                    tipoCl.valorTipoCliente 
            FROM pedido as ped 
                    INNER JOIN cliente as cl ON ped.codCliente = cl.id 
                    INNER JOIN tipocliente as tipoCl ON cl.clienteTipo = tipoCl.id 
                    INNER JOIN solicitud as sol ON sol.codPedido = ped.id 
                    INNER JOIN producto as prod ON prod.codigo = sol.codProducto 
                    INNER JOIN listaproducto as lprod ON prod.codigo = lprod.idProducto 
            WHERE ped.estado = 2 AND ped.id = ?', 
            [$consecutivo]);

	$salida = fopen('php://output', 'wb');
        $separador = ",";

        header('Content-Type:text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename="Reporte_Pedido.csv"');

        foreach ($datosFiltrados as $key) {

		//$FechaActual = date()

	      $newFechaSolicitud = date("d/m/Y");
		$consecutivo = substr($key->consecutivo, 4);
            if($key->patronContable == "FV2"){
                if($key->valorTipoCliente == 1){
		$valor = array($newFechaSolicitud, $key->patronContable, "FV2", $consecutivo, $newFechaSolicitud, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                    fputs($salida, implode($valor, ','));
                }else{
		$valor = array($newFechaSolicitud, $key->patronContable, "FV2", $consecutivo, $newFechaSolicitud, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorValencia, "0", "07", $key->patronContable);
                    fputs($salida, implode($valor,','));
                }
            }else{
                if($key->valorTipoCliente == 1){
                    switch ($key->formaPago) {
                        case '03':
                            $nuevaFecha = strtotime('+1 day', strtotime($key->fechaSolicitud));
                            $nuevaFecha = date('j/m/Y', $nuevaFecha);
			    $valor = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                            fputs($salida, implode($valor, ','));
                            break;
                        case '04':
                            $nuevaFecha = strtotime('+8 day', strtotime($key->fechaSolicitud));
                            $nuevaFecha = date('j/m/Y', $nuevaFecha);
			    $valor = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                            fputs($salida, implode($valor, ','));
                            break;
                        case '05':
                            $nuevaFecha = strtotime('+15 day', strtotime($key->fechaSolicitud));
                            $nuevaFecha = date('j/m/Y', $nuevaFecha);
			    $valor = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                            fputs($salida, implode($valor, ','));
                            break;
                    }
                }else{
                    $nuevaFecha = strtotime('+8 day', strtotime($key->fechaSolicitud));
                    $nuevaFecha = date('j/m/Y', $nuevaFecha);
		    $valor = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorCebarte, "0", "07", $key->patronContable);
                    fputs($salida, implode($valor, ','));
                }
            }
		fwrite($salida, $separador);
		$eol = "\r\n";
		fwrite($salida, $eol);

        }

    }

    public function GenerarPDF($consecutivo){
        
        $pdf = \App::make('dompdf.wrapper');

        $pdf->loadHTML($this->loadTable($consecutivo));

        return $pdf->stream();
    }

    function loadTable($consecutivo){

        $DetallePedido = DB::select(
                                    'SELECT ped.fechaSolicitud, ped.fechaEntrega, cliente.razonSocial
                                    FROM pedido as ped, cliente WHERE cliente.id = ped.codCliente 
                                    AND ped.id = ?', [$consecutivo]);

        $DescripcionPedido = DB::select(
                                    'SELECT sol.codProducto, prod.nombre, sol.cantidadSolicitada,
                                    sol.cantidadDespachada, sol.unidadMedida FROM solicitud as sol,
                                    producto as prod WHERE sol.codProducto = prod.codigo 
                                    AND sol.codPedido = ? ', [$consecutivo]);

        $TotalesPedidos = DB::select(
                                    'SELECT COUNT(solicitud.codProducto) as sumSolicitud, 
                                    SUM(solicitud.cantidadSolicitada) as sumCantSol,
                                    SUM(solicitud.cantidadDespachada) as sumCantDes
                                    FROM solicitud WHERE solicitud.codPedido = ?', [$consecutivo]);

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
        
            .codProducto{
                width: 12%;
            }
        
            .NombreProducto{
                width: 65%;
            }
        
            .CantSolicitada, .CantDespachada{
                width: 9%;
            }
        
            .UnidadMedida{
                width: 7%;
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
        </style>
    
                <img src="svg/logo.png" width="250px" height="170px" class="img">
                <span class="tittle">
                    <h1>Informe sobre Pedidos del desposte</h1>
                    <p>Cliente: <b> '.strtoupper($DetallePedido[0]->razonSocial).'</b></p>
                </span>
            
            <p>Código Pedido: <b>'.$consecutivo.'</b></p>
            <p>Fecha de la solicitud: <b>'.$DetallePedido[0]->fechaSolicitud.'</b></p>
            <p>Fecha de la entrega: <b>'.$DetallePedido[0]->fechaEntrega.'</b></p>
    
            <table class="tableContent">
                <thead>
                    <tr>
                        <th class="codProducto">Cod.Prod.</th>
                        <th class="NombreProducto">Nombre Producto</th>
                        <th class="CantSolicitada">Cant.Sol</th>
                        <th class="CantDespachada">Cant.Des</th>
                        <th class="UnidadMedida">U/M</th>
                    </tr>
                </thead>
                <tbody>';

        foreach($DescripcionPedido as $itemPedido){
            $output .= '
                    <tr>
                        <td>'.$itemPedido->codProducto.'</td>
                        <td class="productName">'.$itemPedido->nombre.'</td>
                        <td>'.$itemPedido->cantidadSolicitada.'</td>
                        <td>'.$itemPedido->cantidadDespachada.'</td>
                        <td>'.$itemPedido->unidadMedida.'</td>
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
        
        return $output;
    }

}
