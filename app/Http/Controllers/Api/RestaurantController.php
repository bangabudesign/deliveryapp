<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = Restaurant::when($request->get('q'), function ($query) use ($request) {
                $query->orWhere('name', 'like', '%'.$request->get('q').'%');
            })
            ->when($request->get('limit'), function ($query) use ($request) {
                $query->limit($request->get('limit'));
            })
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Data Restaurant',
            'data' => $restaurants
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'required',
            'opening_hours' => 'nullable|datetime',
            'closing_hours' => 'nullable|datetime',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
        ], [
            'name' => 'Nama Resto',
            'lat' => 'Latitude',
            'lng' => 'Longtitude',
            'address' => 'Alamat',
            'opening_hours' => 'Jam Buka',
            'closing_hours' => 'Jam Tutup',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $request->user()->update([
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,
        ]);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Memperbarui Lokasi',
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('products')->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Detail Restaurant',
            'data' => $restaurant
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
