<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Cliente;
use App\Pedido;
use App\Solicitud;
use App\Producto;

class EditPedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index($ConsecutivoPedido)
    {
        $productos = Producto::all();
        $pedidos = DB::select('SELECT ped.fechaEntrega, cl.razonSocial, ped.codCliente, ped.fechaSolicitud
                                FROM pedido AS ped, cliente AS cl 
                                WHERE ped.id = ? AND ped.codCliente = cl.id', 
                                [$ConsecutivoPedido]
                            );
        $clientes = DB::select('SELECT cliente.razonSocial, cliente.id 
                                FROM cliente 
                                WHERE cliente.id != ? ', [$pedidos[0]->codCliente]);
        //$pedidoProductos = DB::select('select * from users where active = ?', [1])

        $productosPedidos = DB::select('SELECT prod.codigo, prod.nombre, sol.cantidadSolicitada,
                                        sol.cantidadDespachada, sol.unidadMedida, ped.estado 
                                        FROM producto as prod, solicitud as sol, pedido as ped 
                                        WHERE ped.id = sol.codPedido 
                                        AND sol.codProducto = prod.codigo AND ped.id = ?', 
                                        [$ConsecutivoPedido]);

        return view('consulta.editPedido', compact('ConsecutivoPedido', 'productos', 'pedidos','clientes', 'productosPedidos'));
    }

    public function UpdatePedido(Request $request){

        $pedido = new Pedido();

        $pedido->consecutivo = request('consecutivoPedido');
        $pedido->fechaEntrega = request('fechaEntrega');
        $pedido->productos = json_decode(request('productosPedido'));

        $productos = $pedido->productos;

        $cantidadCiclos = Count($pedido->productos);

        DB::delete('DELETE FROM solicitud WHERE codPedido = ?', [$pedido->consecutivo]);

        for ($i=0; $i < $cantidadCiclos; $i++) {
            DB::insert(
                'INSERT INTO solicitud (codPedido, codProducto, cantidadSolicitada,
                cantidadDespachada, unidadMedida) VALUES (?, ?, ?, ?, ?)', 
                [$pedido->consecutivo, $productos[$i]->consecutivoProducto,
                    $productos[$i]->cantidadSolicitada, $productos[$i]->cantidadDespachada, 
                    $productos[$i]->unidad
                ]);
        }

	DB::update('UPDATE pedido SET fechaEntrega = ? WHERE id  = ?',[$pedido->fechaEntrega, $pedido->consecutivo]);

        return redirect('/consulta');

    }
}
