<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Notifications\DriverOrderCreated;
use App\Notifications\OrderCreated;
use App\Notifications\OrderSuccess;
use App\Notifications\OrderTaken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        $orders = Order::with('user', 'driver', 'restaurant')
            ->when($request->user()->role == 'DRIVER', function ($query) use ($request) {
                $query->where('driver_id', $request->user()->id);
            })
            ->when($request->user()->role == 'USER', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan data pesanan',
            'data' => $orders,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:users,id',
            'restaurant_id' => 'required|exists:restaurants,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'delivery_address' => 'required',
            'delivery_fee' => 'required|numeric|min:1',
            'service_fee' => 'required|numeric|min:1',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
            'exists' => ':attribute tidak terdaftar'
        ], [
            'driver_id' => 'Driver',
            'restaurant_id' => 'Restaurant',
            'lat' => 'Latitude',
            'lng' => 'Longtitude',
            'delivery_address' => 'Alamat Pengiriman',
            'delivery_fee' => 'Ongkos Kirim',
            'service_fee' => 'Biaya Layanan',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        // generating invoice number
        $alphanum = '0123456789ABCDEF';
        $invoice = substr(str_shuffle($alphanum), 0, 6);
        
        $data = [
            'invoice' => $invoice,
            'user_id' => $request->user()->id,
            'driver_id' => $request->driver_id,
            'restaurant_id' => $request->restaurant_id,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'delivery_address' => $request->delivery_address,
            'delivery_fee' => $request->delivery_fee,
            'service_fee' => $request->service_fee,
        ];

        $order = Order::create($data);
        $order->save();

        Notification::send($order->user, new OrderCreated($order));
        Notification::send($order->driver, new DriverOrderCreated($order));

        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('restaurant_id', $request->restaurant_id)
                        ->get();
        
        foreach ($cartItem as $cart) {
            $item = [
                'product_id' => $cart->product_id,
                'name' => $cart->name,
                'price' => $cart->price,
                'qty' => $cart->qty,
                'note' => $cart->note,
            ];

            $order->items()->create($item);
            $cart->delete();
        }

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Pesanan berhasil dibuat',
            'data' => $order
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.'
        ], [
            'status' => 'Status Pesanan',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Gagal memperbarui status',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $order = Order::findOrFail($id);

        $order->update(['status' => $request->status]);

        if ($order->status == 'TAKEN') {
            Notification::send($order->user, new OrderTaken($order));
        }

        if ($order->status == 'PAID') {
            Notification::send($order->user, new OrderSuccess($order));
        }

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Status pesanan berhasil diperbarui',
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
