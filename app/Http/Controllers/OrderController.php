<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $orders = Order::with(['sale.inventory.detailedProduct.product', 'sale.customer'])->get();
            return $this->sendResponse($orders, "Lista de pedidos");
        }  catch(Exception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order, Customer $customer)
    {
        $order = Order::with(['sale.inventory.detailedProduct.product', 'sale.customer'])  
        ->where('customer_id', $customer->id)
        ->get();

    return $this->sendResponse($order, 'Ventas por cliente');
    }

    public function showCustomer(Customer $customer)
{
    $orders = Order::with(['sale.inventory.detailedProduct.product', 'sale.customer'])
        ->whereHas('sale', function($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        })
        ->get();

    return $this->sendResponse($orders, 'Ventas por cliente');
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function orderPay(int $id){
        try {
            $order = Order::find($id);
            if (!$order) {
                return $this->sendError('Orden no encontrada', 404);
            }
            $order->update(['status' => 1]);
            return $this->sendResponse($order, 'Estado de la orden actualizado con éxito');
        } catch (Exception $e) {
            return $this->sendError('Error al actualizar el estado de la orden: ' . $e->getMessage(), 500);
        }
    }


}
