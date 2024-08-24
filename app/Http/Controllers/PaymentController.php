<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private $endpoint;
    private $username;
    private $password;
    private $publickey;
    private $hmacSecret;

    public function __construct()
    {
        $this->endpoint = config('services.payment.endpoint');
        $this->username = config('services.payment.username');
        $this->password = config('services.payment.password');
        $this->publickey = config('services.payment.publickey');
        $this->hmacSecret = config('services.payment.hmac_secret');
    }

    public function apiCheckout(Request $request)
{
    $auth = 'Basic ' . base64_encode('51262675' . ':' . 'testpassword_nhfXWQ7ZZLI4YG6C3ZWeXllaCyUanqZbrQt4oliDDonAv');

    $order = $request->all();
    $secretKey = '25Vr4PGIUterNBQBKPDWWaVelIE4GgMuxPo5mKxtXaY2Q'; // Debe ser la misma clave en ambos métodos

    try {
        $response = Http::withHeaders([
            'Authorization' => $auth,
            'Content-Type' => 'application/json'
        ])->timeout(60)->post('https://api.micuentaweb.pe/api-payment/V4/Charge/CreatePayment', $order);

        $body = $response->json();

        if ($body['status'] === 'SUCCESS') {
            $formToken = $body['answer']['formToken'];
            $rawClientAnswer = json_encode($body['answer']);
            $hash = hash_hmac('sha256', $rawClientAnswer, $secretKey); // Genera el hash
            Log::info($rawClientAnswer);

            // Almacenar rawClientAnswer en la sesión
            session(['rawClientAnswer' => $rawClientAnswer]);

            return response()->json([
                'formToken' => $formToken,
                'publickey' => "51262675:testpublickey_y0FaoDYdXT2AzdVunYmKyMtvu92vTgADMRzmxftzM8xZO",
                'endpoint' => "https://api.micuentaweb.pe",
                'hash' => $hash, 
                'rawClientAnswer' => $rawClientAnswer 
            ]);
        } else {
            Log::error($body);
            return response()->json(['error' => 'Error en la creación del formToken'], 500);
        }
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['error' => 'Error en la solicitud', 'message' => $e->getMessage()], 500);
    }
}



public function apiValidate(Request $request)
{
    // Decodifica la respuesta del cliente
    $answer = json_decode($request->input('rawClientAnswer'), true);
    $hash = $request->input('hash');

    if ($hash) {
        if (isset($answer['orderStatus']) && isset($answer['orderDetails'])) {
            return response()->json([
                'response' => $answer['orderStatus'],
                'details' => $answer['orderDetails']
            ]);
        } else {
            Log::error('Estructura de respuesta incorrecta', ['answer' => $answer]);
            return response()->json(['error' => 'Estructura de respuesta incorrecta'], 400);
        }
    } else {
        Log::error('Hash no proporcionado', ['hash' => $hash]);
        return response()->json(['error' => 'Hash no proporcionado'], 400);
    }
}





    public function paid(Request $request)
    {
        $answer = json_decode($request->input('kr-answer'), true);
        $hash = $request->input('kr-hash');

        $answerHash = hash_hmac('sha256', json_encode($answer), '25Vr4PGIUterNBQBKPDWWaVelIE4GgMuxPo5mKxtXaY2Q');

        if ($hash === $answerHash) {
            return response()->json([
                'response' => $answer['orderStatus'],
                'details' => $answer['orderDetails']
            ]);
        } else {
            return response()->json(['error' => 'Error catastrófico'], 500);
        }
    }

    public function ipn(Request $request)
    {
        $answer = json_decode($request->input('kr-answer'), true);
        $hash = $request->input('kr-hash');

        $answerHash = hash_hmac('sha256', json_encode($answer), '25Vr4PGIUterNBQBKPDWWaVelIE4GgMuxPo5mKxtXaY2Q');

        if ($hash === $answerHash) {
            return response()->json(['response' => $answer['orderStatus']], 200);
        } else {
            return response()->json(['error' => 'Error catastrófico, posible intento de fraude'], 500);
        }
    }
}
