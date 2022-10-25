<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Transaction;
use App\Notifications\DriverOrderCreated;
use App\Notifications\OrderCanceled;
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
            ->orderBy('updated_at', 'DESC')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan data pesanan',
            'data' => $orders,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['restaurant', 'driver', 'user'])->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan data pesanan',
            'data' => $order,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:FOOD,BIKE',
            'driver_id' => 'required|exists:users,id',
            'restaurant_id' => 'required_if:type,FOOD|exists:restaurants,id',
            'origin_lat' => 'required|numeric',
            'origin_lng' => 'required|numeric',
            'origin_address' => 'required',
            'destination_lat' => 'required|numeric',
            'destination_lng' => 'required|numeric',
            'destination_address' => 'required',
            'delivery_fee' => 'required|numeric|min:1',
            'service_fee' => 'required|numeric|min:1',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'required_if' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
            'in' => ':attribute hanya boleh berisi :min',
            'exists' => ':attribute tidak terdaftar'
        ], [
            'type' => 'Tipe Order',
            'driver_id' => 'Driver',
            'restaurant_id' => 'Restaurant',
            'origin_lat' => 'Titik Jemput',
            'origin_lng' => 'Titik Jemput',
            'origin_address' => 'Alamat Penjemputan',
            'destination_lat' => 'Titik Tujuan',
            'destination_lng' => 'Titik Tujuan',
            'destination_address' => 'Alamat Tujuan',
            'delivery_fee' => 'Ongkos Perjalanan',
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
            'type' => $request->type,
            'driver_id' => $request->driver_id,
            'origin_lat' => $request->origin_lat,
            'origin_lng' => $request->origin_lng,
            'origin_address' => $request->origin_address,
            'destination_lat' => $request->destination_lat,
            'destination_lng' => $request->destination_lng,
            'destination_address' => $request->destination_address,
            'delivery_fee' => $request->delivery_fee,
            'service_fee' => $request->service_fee,
        ];

        if ($request->type == 'FOOD') {
            $data['restaurant_id'] = $request->restaurant_id;
        }

        $order = Order::create($data);
        $order->save();

        if ($order->type == 'FOOD') {
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
        }

        Notification::send($order->user, new OrderCreated($order));
        Notification::send($order->driver, new DriverOrderCreated($order));

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

        if ($order->status == 'CANCELED') {
            Notification::send($order->user, new OrderCanceled($order));
        }

        if ($order->status == 'TAKEN') {
            Notification::send($order->user, new OrderTaken($order));
        }

        if ($order->status == 'PAID') {
            Notification::send($order->user, new OrderSuccess($order));
            $delivery_fee = ($order->delivery_fee / 100) * 10;
            // Transaction::create([
            //     'order_id' => $order->id,
            //     'driver_id' => $order->driver_id,
            //     'amount' => $delivery_fee,
            //     'notes' => 'Potongan ongkir 10%',
            // ]);
            $service_fee = $order->service_fee;
            Transaction::create([
                'order_id' => $order->id,
                'driver_id' => $order->driver_id,
                'amount' => $service_fee,
                'notes' => 'Service fee 50%',
            ]);
            // Bonus::create([
            //     'order_id' => $order->id,
            //     'merchant_id' => $order->restaurant->merchant_id,
            //     'amount' => $delivery_fee / 2,
            //     'notes' => 'Delivery fee 50%',
            // ]);
            if ($order->type == 'FOOD') {
                Bonus::create([
                    'order_id' => $order->id,
                    'merchant_id' => $order->restaurant->merchant_id,
                    'amount' => $service_fee / 2,
                    'notes' => 'Service fee 50%',
                ]);
            }
        }

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Status pesanan berhasil diperbarui',
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
