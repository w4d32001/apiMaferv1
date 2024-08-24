<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\StoreRequest;
use App\Http\Requests\Customer\UpdateRequest;
use App\Models\Customer;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;

class CustomerController extends BaseController
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.apis.net.pe',
            'verify' => false,
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('customerType')->get();
        return $this->sendResponse($customers, "Lista de clientes");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $validated = $request->validated();
            if (empty($validated['password'])) {
                if (!empty($validated['ruc'])) {
                    $validated['password'] = $validated['ruc'];
                } elseif (!empty($validated['dni'])) {
                    $validated['password'] = $validated['dni'];
                }
            }
    
            $validated['password'] = Hash::make($validated['password']);
    
            if (!empty($validated['ruc'])) {
                $validated['customer_type_id'] = 1;
            } else {
                $validated['customer_type_id'] = 2; 
            }
            $customer = Customer::create($validated);
            return $this->sendResponse($customer, 'Cliente creado con exito', 'succcess', 201);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return $this->sendResponse($customer, "Cliente encontrado con exito");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Customer $customer)
{
    try {
        $validated = $request->validated();
        
        $currentData = $customer->only(['image', 'ruc', 'reason']);
        
        $updatedData = array_merge($currentData, array_filter($validated, function ($value) {
            return $value !== null;
        }));
        $customer->update($updatedData);
        
        return $this->sendResponse($customer, 'Cliente actualizado con éxito');
    } catch (\Illuminate\Database\QueryException $e) {
        return $this->sendError('Error de base de datos: ' . $e->getMessage());
    } catch (Exception $e) {
        return $this->sendError('Error: ' . $e->getMessage());
    }
}
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            if($customer->sales()->exists()){
                return $this->sendError("Este cliente ya tiene registros asociados");
            }
            $customer->delete();
            return $this->sendResponse([], "Cliente eliminado exitosamente");
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function amount()
    {
        try {
            $amount = Customer::count();
            return response()->json($amount);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    public function consultarRuc($rucNumber)
    {
        $token = 'apis-token-9267.Cg5Z55wy2ggaaC9lqFdJnyheToq5KpEZ';

        try {
            $res = $this->client->request('GET', '/v2/sunat/ruc', [
                'http_errors' => false,
                'connect_timeout' => 5,
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Referer' => 'https://apis.net.pe/api-consulta-ruc',
                    'User-Agent' => 'laravel/guzzle',
                    'Accept' => 'application/json',
                ],
                'query' => ['numero' => $rucNumber],
            ]);

            if ($res->getStatusCode() == 200) {
                $response = json_decode($res->getBody()->getContents(), true);
                return response()->json($response);
            } else {
                $errorBody = json_decode($res->getBody()->getContents(), true);
                return response()->json([
                    'error' => 'Error al consultar el RUC',
                    'status' => $res->getStatusCode(),
                    'details' => $errorBody
                ], $res->getStatusCode());
            }
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'RequestException',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        } catch (ConnectException $e) {
            return response()->json([
                'error' => 'ConnectException',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'GeneralException',
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
    public function updateAddress(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'department' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'additional_info' => 'nullable|string|max:255',
        ]);
        
        try {
            // Inicializa una lista para almacenar las partes de la dirección
            $addressParts = [];
        
            // Agrega las partes a la lista si están presentes
            if (!empty($validated['address'])) {
                $addressParts[] = $validated['address'];
            }
            if (!empty($validated['district'])) {
                $addressParts[] = $validated['district'];
            }
            if (!empty($validated['province'])) {
                $addressParts[] = $validated['province'];
            }
            if (!empty($validated['department'])) {
                $addressParts[] = $validated['department'];
            }
            if (!empty($validated['additional_info'])) {
                $addressParts[] = $validated['additional_info'];
            }
        
            // Une las partes con comas
            $fullAddress = implode(', ', $addressParts);
        
            // Actualiza la dirección del cliente
            $customer->update(['address' => $fullAddress]);
            $customer->refresh();
        
            return $this->sendResponse($customer, 'Dirección actualizada con éxito');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        
    }
}
