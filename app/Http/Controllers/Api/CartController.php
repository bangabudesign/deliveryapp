<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with('restaurant')
            ->when($request->get('restaurant_id'), function ($query) use ($request) {
                $query->where('restaurant_id', $request->get('restaurant_id'));
            })
            ->where('user_id', $request->user()->id)
            ->get();
        
        $summary = [
            'sub_total' => $carts->sum('total'),
            'delivery_fee' => 7000,
            'service_fee' => 5000,
        ];

        $summary['total'] = $summary['sub_total'] + $summary['delivery_fee'] + $summary['service_fee'];

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Data Cart',
            'data' => $carts,
            'summary' => $summary,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|numeric',
            'product_id' => 'required|numeric',
            'name' => 'required',
            'price' => 'required|numeric',
            'qty' => 'required|numeric|min:1',
            'note' => 'nullable',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'restaurant_id' => 'Restaurant',
            'product_id' => 'Produk',
            'name' => 'Nama Menu',
            'price' => 'Harga',
            'qty' => 'Jumlah',
            'note' => 'Catatan',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('restaurant_id', $request->restaurant_id)
                        ->where('product_id', $request->product_id)
                        ->first();
        
        if ($cartItem) {
            $data = [
                'price' => $request->price,
                'qty' => $request->qty,
                'note' => $request->note,
            ];
            $cart = $cartItem->update($data);
        } else {
            $data = [
                'user_id' => $request->user()->id,
                'restaurant_id' => $request->restaurant_id,
                'product_id' => $request->product_id,
                'name' => $request->name,
                'price' => $request->price,
                'qty' => $request->qty,
                'note' => $request->note,
            ];
            $cart = Cart::create($data);
            $cart->save();
        }

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Memperbarui Data',
            'data' => $cart
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id)
    {
        $cart = Cart::with('menus')->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Detail Cart',
            'data' => $cart
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function destroy(Cart $cart)
    {
        $cart->delete();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Menghapus Data',
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
