<?php

namespace App\Http\Controllers;

use App\Http\Requests\Inventory\StoreRequest;
use App\Http\Requests\Inventory\UpdateRequest;
use App\Models\Category;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;

class InventoryController extends BaseController
{
    public function index()
    {
        try {
            $inventories = Inventory::with('detailedProduct', 'detailedProduct.product', 'detailedProduct.provider', 'detailedProduct.category')
            ->where('status', 1) 
            ->get();
            $transformedInventories = $inventories->map(function ($inventory) {
                return [
                    'id' => $inventory->id,
                    'stock' => $inventory->stock,
                    'status' => $inventory->status,
                    'detailed_product_id' => $inventory->detailed_product_id,
                    'created_at' => $inventory->created_at,
                    'updated_at' => $inventory->updated_at,
                    'product' => $inventory->detailedProduct->product,
                    'provider' => $inventory->detailedProduct->provider,
                    'category' => $inventory->detailedProduct->category,
                ];
            });

            $categories = Category::all();

            return $this->sendResponse(['inventories' => $transformedInventories, 'categories' => $categories], 'Lista de inventarios');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['location'] = 'En el almacen';
            $validated['expiration_date'] = Carbon::now()->addMonths(6)->toDateString();
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();
            $inventory = Inventory::create($validated);
            return $this->sendResponse($inventory, 'Inventario creado exitosamente.', 'success', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function show(Inventory $inventory)
    {
        try {
            $inventory->load('detailedProduct');
            return $this->sendResponse($inventory, 'Inventario encontrado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(UpdateRequest $request, Inventory $inventory)
    {
        try {
            $validated = $request->validated();
            $validated['updated_by'] = Auth::id();
            $inventory->update($validated);
            return $this->sendResponse($inventory, 'Inventario actualizado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Inventory $inventory)
    {
        try {
            $inventory->delete();
            return $this->sendResponse([], 'Invetario eliminado exitosamente');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    public function amount()
    {
        try {
            $amount = Inventory::count();
            return response()->json($amount);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function updateStock(int $id, int $stock)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return $this->sendError('Inventario no encontrado.', 404);
        }

        $inventory->stock += $stock;

        if ($inventory->stock < 0) {
            return $this->sendError('El stock no puede ser negativo.', 400);
        }

        try {
            $inventory->save();
        } catch (Exception $e) {
            return $this->sendError('Error al actualizar el stock.', 500);
        }

        return $this->sendResponse($inventory, 'Stock actualizado');
    }

    public function updateStatus(int $id){

        $inventory = Inventory::find($id);

        $inventory->update([
            'status' => 0
        ]);

        return $this->sendResponse($inventory, "Estado actualizado");

    }
}
