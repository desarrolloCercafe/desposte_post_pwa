<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Cliente;
use App\Producto;
use App\Solicitud;
//use DB;

class SolicitudController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clientes = Cliente::all();
        $productos = Producto::all();
        return view('solicitud.index', compact('clientes','productos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $solicitud = new Solicitud();

        $solicitud->productos = json_decode(request('productos'));
        $solicitud->cliente = request('codCliente');
        $vendedor = request('nombreVendedor');

        $idVendedor = DB::select('select id from usuario where nombre = ?', [$vendedor]);

        $codCliente = DB::select('SELECT id FROM cliente WHERE razonSocial = ?', [$solicitud->cliente]);

        $solicitud->fechaEntrega = request('fechaEntrega');
        $solicitud->fechaSolicitud = request('fechaSolicitud');

	if($codCliente[0]->id == 69 || $codCliente[0]->id == 77 || $codCliente[0]->id == 150 || $codCliente[0]->id == 295){
		$data = DB::select("SELECT valor FROM contador WHERE id = 1");
		$consecutivo = "FE3-".$data[0]->valor;
		$sumaConsecutivo = ($data[0]->valor + 1);
		DB::update('UPDATE contador SET valor = ? WHERE id = 1',[$sumaConsecutivo]);
	}else{
		$data = DB::select("SELECT valor FROM contador WHERE id = 2");
		$consecutivo = "FV2-".$data[0]->valor;
		$sumaConsecutivo = ($data[0]->valor + 1);
		DB::update('UPDATE contador SET valor = ? WHERE id = 2',[$sumaConsecutivo]);
	}

        DB::insert(
            'insert into pedido (consecutivo, codCliente, fechaSolicitud, fechaEntrega, idVendedor, estado) values (?, ?, ?, ?, ?, ?)',
            [$consecutivo, $codCliente[0]->id, $solicitud->fechaSolicitud, $solicitud->fechaEntrega, $idVendedor[0]->id, 1]
        );

        $consecutivo = DB::select(
                        'select id from pedido where fechaSolicitud = ? AND codCliente = ?',
                        [$solicitud->fechaSolicitud,$codCliente[0]->id]);

        //echo $consecutivo[0]->Consecutivo;

        $cantidad = Count($solicitud->productos);

        //echo $cantidad;

        for ($i=0; $i < $cantidad; $i++) { 
            DB::insert(
                    'insert into solicitud (codPedido, codProducto, cantidadSolicitada, unidadMedida) values (?, ?, ?, ?)',
                    [$consecutivo[0]->id, $solicitud->productos[$i]->codigo, $solicitud->productos[$i]->cantidad, $solicitud->productos[$i]->radio]);
        }

        return redirect()->route('solicitud.index');

        
    }

    public function ChangeInput(Request $request){

        $usuario = $request->data;

        $filtrado = strtolower($usuario);

        $username = DB::select("select razonSocial from cliente where razonSocial LIKE '%".$filtrado."%'");

        return json_encode($username);
    }
}
