<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Delivery;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class DeliveryFeeController extends Controller
{
    public function fees(Request $request)
    {
        $delivery_fee = Delivery::getFee($request->origin_lat, $request->origin_lng, $request->destination_lat, $request->destination_lng, $request->type);
        
        $data = [
            'delivery_fee' => $delivery_fee,
            'service_fee' => 5000,
        ];

        $data['total'] = $data['delivery_fee'] + $data['service_fee'];

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Data Ongkos Kirim',
            'data' => $data,
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
