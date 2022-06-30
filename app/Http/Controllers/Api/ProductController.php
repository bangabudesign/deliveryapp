<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required',
            'price' => 'required|numeric',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'date_format' => 'Format :attribute tidak sesuai.',
        ], [
            'restaurant_id' => 'id restaurant',
            'name' => 'Nama Menu',
            'price' => 'Harga',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'restaurant_id' => $request->restaurant_id,
            'name' => $request->name,
            'price' => $request->price,
        ];

        if ($request->image) {
            $data['image'] = $request->image;
        }

        $product = Product::create($data);
        $product->save();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $product
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required',
            'price' => 'required|numeric',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'date_format' => 'Format :attribute tidak sesuai.',
        ], [
            'restaurant_id' => 'id restaurant',
            'name' => 'Nama Menu',
            'price' => 'Harga',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $product = Product::findOrFail($id);

        $data = [
            'restaurant_id' => $request->restaurant_id,
            'name' => $request->name,
            'price' => $request->price,
        ];

        if ($request->image) {
            $data['image'] = $request->image;
        }

        $product->update($data);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $product
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
