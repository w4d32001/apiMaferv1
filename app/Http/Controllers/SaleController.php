<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sale\StoreRequest;
use App\Http\Requests\Sale\UpdateRequest;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Sale;
use Exception;
use Illuminate\Http\Request;

class SaleController extends BaseController
{

    public function index()
    {
        try {
            $sales = Sale::all();
            return $this->sendResponse($sales, "Lista de ventas");
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $quantity = $validated['total_quantity'];
            $idInventory = $validated['inventory_id'];

            $sale = Sale::create($validated);

            $inventory = Inventory::find($idInventory);

            if ($inventory) {
                if ($inventory->stock === null) {
                    return $this->sendError("Stock del inventario no definido", 400);
                }

                if ($inventory->stock < $quantity) {
                    return $this->sendError("Cantidad vendida mayor que el stock disponible", 400);
                }

                $inventory->stock = $inventory->stock - $quantity;
                $inventory->save(); 

                Order::create([
                    'sale_id' => $sale->id,
                    'status' => 0
                ]);

                return $this->sendResponse($sale, "Venta creada exitosamente", 'success', 201);
            } else {
                return $this->sendError("Inventario no encontrado", 404);
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        //
    }


}
