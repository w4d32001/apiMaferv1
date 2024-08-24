<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Exception;
use Illuminate\Http\Request;

class PaymentMethodController extends BaseController
{
    public function index(){
        try {
            $payments = PaymentMethod::all();
            return $this->sendResponse($payments, "Lista de metodos de pago");
        } catch(Exception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }
}
