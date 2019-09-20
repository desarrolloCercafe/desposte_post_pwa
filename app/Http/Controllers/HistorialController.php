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

	if($filtros->tipoPresentacion == 1){
		 if(isset($filtros->codCliente)){
			$codCliente = $filtros->codCliente;
	        }

        	if(isset($filtros->valorFecha)){
                	if($filtros->valorFecha == 1){
                        	$hoy = $filtros->FechaHoy;
	                }elseif($filtros->valorFecha == 2){
				$fechaAyerNeta = $filtros->FechaAyer;
        	                $ayer = strtotime('-1 day', strtotime($fechaAyerNeta));
				$ayer = date('Y-m-j', $ayer);
                	}else{
                        	$fechaInicioH = $filtros->fechaInicioH;
	                        $fechaFin = $filtros->fechaFin;
        	        }
        	}

	        if(isset($filtros->codCliente) && isset($filtros->valorFecha)){
        	        if($filtros->valorFecha == 1){
                                $datosFiltrados = DB::select(
                                        'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                        FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                                        AND ped.codCliente = ? AND ped.fechaEntrega = ? ',
                                    [$codCliente, $hoy]
                                );
	                }elseif($filtros->valorFecha == 2){
        	                $datosFiltrados = DB::select(
                    			'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
		                        FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                    			AND ped.codCliente = ? AND ped.fechaEntrega = ? ',
		                    [$codCliente, $ayer]
                		);
	                }else{
				$datosFiltrados = DB::select(
                  			  'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
			                  FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                  			  AND ped.codCliente = ? AND ped.fechaEntrega BETWEEN ? AND ? ',
		                    [$codCliente, $fechaInicioH, $fechaFin]
                		);
			}

        	}elseif(isset($filtros->valorFecha)){
                	if($filtros->valorFecha == 1){
                        	$datosFiltrados = DB::select(
                                        'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                        FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                                        AND ped.fechaEntrega = ? ',
                                    [$hoy]
                                );
	                }elseif($filtros->valorFecha == 2){
        	                $datosFiltrados = DB::select(
                                        'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                        FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                                        AND ped.fechaEntrega = ? ',
                                    [$ayer]
                                );
                	}else{
                        	$datosFiltrados = DB::select(
                                          'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                          FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                                          AND ped.fechaEntrega BETWEEN ? AND ? ',
                                    [$fechaInicioH, $fechaFin]
                                );
                	}
	        }elseif(isset($filtros->codCliente)){
        	                $datosFiltrados = DB::select(
                                          'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado
                                          FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
                                          AND ped.codCliente = ?',
                                    [$codCliente]
                                );
	        }/**/

	}else{
		 if(isset($filtros->codCliente)){
			$codCliente = $filtros->codCliente;
	        }

        	if(isset($filtros->valorFecha)){
                	if($filtros->valorFecha == 1){
                        	$hoy = $filtros->FechaHoy;
	                }elseif($filtros->valorFecha == 2){
                                $fechaAyerNeta = $filtros->FechaAyer;
                                $ayer = strtotime('-1 day', strtotime($fechaAyerNeta));
                                $ayer = date('Y-m-j', $ayer);
//        	                $ayer = $filtros->FechaAyer;
                	}else{
                        	$fechaInicioH = $filtros->fechaInicioH;
	                        $fechaFin = $filtros->fechaFin;
        	        }
	        }

        	if(isset($filtros->codCliente) && isset($filtros->valorFecha)){
                	if($filtros->valorFecha == 1){
                        	$datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                                            sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            cl.id = ? AND ped.fechaEntrega = ? AND ped.estado = 2 ',
                                            [$codCliente, $hoy]
                                );
	                }elseif($filtros->valorFecha == 2){
  	                       $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                                            sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            cl.id = ? AND ped.fechaEntrega = ? AND ped.estado = 2 ',
                                            [$codCliente, $ayer]
                                );
	                }else{
		               $datosFiltrados = DB::select(
                			    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
			                    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
			                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
			                    INNER JOIN pedido as ped ON ped.id = sol.codPedido
			                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
			                    cl.id = ? AND ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 2 ',
	        	                   [$codCliente, $fechaInicioH, $fechaFin]
		                );
			}

        	}elseif(isset($filtros->valorFecha)){
                	if($filtros->valorFecha == 1){
                        	$datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                                            sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            ped.fechaEntrega = ? AND ped.estado = 2 ',
                                            [$hoy]
                                );

	                }elseif($filtros->valorFecha == 2){
        	                $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                                            sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            ped.fechaEntrega = ? AND ped.estado = 2 ',
                                            [$ayer]
                                );

                	}else{
                        	 $datosFiltrados = DB::select(
                                            'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                                            sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
                                            INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
                                            INNER JOIN pedido as ped ON ped.id = sol.codPedido
                                            INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
                                            ped.fechaEntrega BETWEEN ? AND ? AND ped.estado = 2 ',
                                           [$fechaInicioH, $fechaFin]
                                );

	                }
        	}elseif(isset($filtros->codCliente)){
                	$datosFiltrados = DB::select(
		                    'SELECT prod.codigo, prod.nombre as NombreProd, sol.cantidadSolicitada, sol.cantidadDespachada,
                		    sol.unidadMedida, cl.razonSocial as NombreCl, ped.id FROM producto as prod
		                    INNER JOIN solicitud as sol ON sol.codProducto = prod.codigo
		                    INNER JOIN pedido as ped ON ped.id = sol.codPedido
		                    INNER JOIN cliente as cl ON cl.id = ped.codCliente WHERE
		                    cl.id = ? AND ped.estado = 2  ',
		                    [$codCliente]
			);

     		}/**/

	}
/**/

/*        if(Count($datosFiltrados) == 0){
            $datosFiltrados = "no hay resultados";
        }*/

        return json_encode($datosFiltrados);
    }

    public function filtrar_por_consecutivo(Request $request){
	header("Content-type: application/json");
	$filtros = json_decode(stripslashes(file_get_contents("php://input")));
	$filtros = json_decode(stripslashes($request->consecutivo));

	$response = "";

       	$response = DB::select(
      		'SELECT ped.id, ped.fechaSolicitud, ped.fechaEntrega, cl.razonSocial, ped.estado, ped.idVendedor
	            FROM pedido as ped, cliente as cl WHERE ped.estado = 2 AND ped.codCliente = cl.id
       		    AND ped.id = ?',
	           [$filtros]
       	);

	if(Count($response)){
		return json_encode($response);
	}else{
		$response = "no hay resultados";
		return json_encode($response);
	}
//	return json_encode($response);

    }

    public function CSVGeneral(Request $request){

        header("Content-Type: application/json");
        $filtros = json_decode(stripslashes(file_get_contents("php://input")));
        // build a PHP variable from JSON sent using GET method
        $filtros = json_decode(stripslashes($request->data));
        // encode the PHP variable to JSON and send it back on client-side

        if(isset($filtros->codCliente)){
            $codCliente = $filtros->codCliente;
        }

	if(isset($filtros->valorFecha)){
		if($filtros->valorFecha == 1){
			$hoy = $filtros->FechaHoy;
		}elseif($filtros->valorFecha == 2){
                        $fechaAyerNeta = $filtros->FechaAyer;
                        $ayer = strtotime('-1 day', strtotime($fechaAyerNeta));
                        $ayer = date('Y-m-j', $ayer);
		}else{
			$fechaInicioH = $filtros->fechaInicioH;
			$fechaFin = $filtros->fechaFin;
		}
	}


        $datosFiltrados = "";

        if(isset($filtros->valorFecha) && isset($filtros->codCliente)){

		if($filtros->valorFecha == 1){

                    $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud,
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE cl.id = ? AND
                            ped.fechaEntrega = ? AND
                            ped.estado = 2 ',
                    [$codCliente, $hoy]
                    );

		}elseif($filtros->valorFecha == 2){

                    $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud,
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE cl.id = ? AND
                            ped.fechaEntrega = ? AND
                            ped.estado = 2 ',
                    [$codCliente, $ayer]
                    );


		}else{
                    $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud,
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE cl.id = ? AND
                            ped.fechaEntrega BETWEEN ? AND ? AND
                            ped.estado = 2 ',
                    [$codCliente, $fechaInicioH, $fechaFin]
                    );

		}

        /**/

        }elseif(isset($filtros->valorFecha)){

		if($filtros->valorFecha == 1){
                    $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud,
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE ped.fechaEntrega = ? AND
                            ped.estado = 2 ',
                    [$hoy]
                    );

		}elseif($filtros->valorFecha == 2){

                    $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud,
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE ped.fechaEntrega = ? AND
                            ped.estado = 2 ',
                    [$ayer]
                    );

		}else{
                    $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud,
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE ped.fechaEntrega BETWEEN ? AND ? AND
                            ped.estado = 2 ',
                    [$fechaInicioH, $fechaFin]
                    );

		}
/**/

        }elseif(isset($filtros->codCliente)){
 
                $datosFiltrados = DB::select(
                    'SELECT ped.fechaSolicitud, 
                            tipoCl.patronContable,
                            ped.id,
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
                    WHERE cl.id = ? AND
                            ped.estado = 2 ', 
                    [$codCliente]
                );
        }

        $cantidadResultados = Count($datosFiltrados);

        if($cantidadResultados){

            return json_encode("Ok");

        }else{
            return json_encode("no hay resultados");
        }
    }

public function CSVcreation(Request $request){

/*        if(request('fechaInicioH')){
            $fechaInicioH = request('fechaInicioH');
            $fechaFin = request('fechaFin');
        }*/
        if(request('codCliente')){
            $codCliente = request('codCliente');
        }

	if(request('valorFecha')){
		if(request('valorFecha') == 1){
			$hoy = request('FechaHoy');
		}elseif(request('valorFecha') == 2){
			$ayer = request('FechaAyer');
		}else{
	                $fechaInicioH = request('fechaInicioH');
		        $fechaFin = request('fechaFin');
		}
	}

        $datosFiltrados = "";

	if(request('valorFecha') && isset($codCliente)){

		if(request('valorFecha') == 1){
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
                    WHERE cl.id = ? AND
                            ped.fechaEntrega = ? AND
                            ped.estado = 2 ORDER BY cl.nit ORDER BY ped.consecutivo ASC',
                    [$codCliente, $hoy]
                    );

		}elseif(request('valorFecha') == 2){
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
                    WHERE cl.id = ? AND
                            ped.fechaEntrega = ? AND
                            ped.estado = 2 ORDER BY ped.consecutivo ASC',
                    [$codCliente, $ayer]
                    );
		}else{
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
                    WHERE cl.id = ? AND
                            ped.fechaEntrega BETWEEN ? AND ? AND
                            ped.estado = 2 ORDER BY ped.consecutivo ASC',
                    [$codCliente, $fechaInicioH, $fechaFin]
                    );

		}
                /**/

        }elseif(request('valorFecha')){

                if(request('valorFecha') == 1){
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
                    WHERE ped.fechaEntrega = ? AND
                            ped.estado = 2 ORDER BY ped.consecutivo ASC',
                    [$hoy]
                    );
		}elseif(request('valorFecha') == 2){
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
                    WHERE ped.fechaEntrega = ? AND
                            ped.estado = 2 ORDER BY ped.consecutivo ASC',
                    [$ayer]
                    );
		}else{
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
                    WHERE ped.fechaEntrega BETWEEN ? AND ? AND
                            ped.estado = 2 ORDER BY ped.consecutivo ASC',
                    [$fechaInicioH, $fechaFin]
                    );

		}
	/**/

        }elseif(isset($codCliente)){
            
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
                    WHERE cl.id = ? AND
                            ped.estado = 2 ORDER BY ped.consecutivo ASC', 
                    [$codCliente]
                );
        }
        $salida = fopen('php://output', 'w');
        $separador = ",";

        header('Content-Type:text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Reporte_Fechas_Ingreso.csv"');

        foreach ($datosFiltrados as $key) {

            $newFechaSolicitud = date("d/m/Y");
	    $consecutivo = substr($key->consecutivo, 4);

            if($key->patronContable == "FV2"){
               if($key->valorTipoCliente == 1){
		    $valores = array($newFechaSolicitud, $key->patronContable, "PV2", $consecutivo, $newFechaSolicitud, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                    fputs($salida, implode($valores, ','));
                }else{
		    $valores = array($newFechaSolicitud, $key->patronContable, "PV2", $consecutivo, $newFechaSolicitud, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorValencia, "0", "07", $key->patronContable);
                    fputs($salida, implode($valores, ','));
                }
            }else{
	           if($key->valorTipoCliente == 1){
                    switch ($key->formaPago) {
                        case '03':
                            $nuevaFecha = strtotime('+1 day', strtotime($key->fechaSolicitud));
                            $nuevaFecha = date('j/m/Y', $nuevaFecha);
                            $valores = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                            fputs($salida, implode($valores, ','));
                            break;
                        case '04':
                            $nuevaFecha = strtotime('+8 day', strtotime($key->fechaSolicitud));
                            $nuevaFecha = date('j/m/Y', $nuevaFecha);
			    $valores = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                            fputs($salida, implode($valores, ','));
                            break;
                        case '05':
                            $nuevaFecha = strtotime('+15 day', strtotime($key->fechaSolicitud));
                            $nuevaFecha = date('j/m/Y', $nuevaFecha);
			    $valores = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo,$key->cantidadDespachada, $key->valorMayorista, "0", "07", $key->patronContable);
                            fputs($salida, implode($valores, ','));
                            break;
                    }
                }else{
                    $nuevaFecha = strtotime('+8 day', strtotime($key->fechaSolicitud));
                    $nuevaFecha = date('j/m/Y', $nuevaFecha);
	      	    $valores = array($newFechaSolicitud, $key->patronContable, "EMAN", $consecutivo, $nuevaFecha, $key->nit, $key->razonSocial, "38", "Producto subido por archivo plano", $key->formaPago, "30", $key->codigo, $key->cantidadDespachada, $key->valorCebarte, "0", "07", $key->patronContable);
                    fputs($salida, implode($valores, ','));
                }
            }
		fwrite($salida, $separador);
		$eol = "\r\n";
		fwrite($salida, $eol);
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
