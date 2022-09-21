<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Delivery;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $lat = explode(',', $request->get('near_by'))[0] ?? null;
        $lng = explode(',', $request->get('near_by'))[1] ?? null;

        $restaurants = Restaurant::when($request->get('q'), function ($query) use ($request) {
                $query->orWhere('name', 'like', '%'.$request->get('q').'%');
            })
            ->when($request->get('limit'), function ($query) use ($request) {
                $query->limit($request->get('limit'));
            })
            ->when($request->user()->role == 'MERCHANT', function ($query) use ($request) {
                $query->where('merchant_id', $request->user()->id);
            })
            ->when($request->get('near_by'), function ($query) use ($lat, $lng) {
                $query->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(restaurants.lat)) 
                    * cos(radians(restaurants.lng) - radians(" . $lng . ")) 
                    + sin(radians(" .$lat. ")) 
                    * sin(radians(restaurants.lat))) AS distance"))
                ->havingRaw('distance < 20')
                ->orderBy('distance');
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
            'opening_hours' => 'required|date_format:H:i',
            'closing_hours' => 'required|date_format:H:i',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'date_format' => 'Format :attribute tidak sesuai.',
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

        $data = [
            'merchant_id' => $request->user()->id,
            'name' => $request->name,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,
            'opening_hours' => $request->opening_hours,
            'closing_hours' => $request->closing_hours,
        ];

        if ($request->image) {
            $data['image'] = $request->image;
        }

        $restaurant = Restaurant::create($data);
        $restaurant->save();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil menambah resto baru',
            'data' => $restaurant
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id, Request $request)
    {
        $lat = $request->user()->lat ?? null;
        $lng = $request->user()->lng ?? null;
        
        $restaurant = Restaurant::with('products')
            ->when($lat && $lng, function ($query) use ($lat, $lng) {
                $query->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(restaurants.lat)) 
                    * cos(radians(restaurants.lng) - radians(" . $lng . ")) 
                    + sin(radians(" .$lat. ")) 
                    * sin(radians(restaurants.lat))) AS distance"));
            })
            ->findOrFail($id);
        
        $restaurant['delivery_fee'] = Delivery::getFee($lat, $lng, $restaurant->lat, $restaurant->lng);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Detail Restaurant',
            'data' => $restaurant
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'address' => 'required',
            'opening_hours' => 'required|date_format:H:i',
            'closing_hours' => 'required|date_format:H:i',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'date_format' => 'Format :attribute tidak sesuai.',
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

        $restaurant = Restaurant::findOrFail($id);

        $data = [
            'name' => $request->name,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'address' => $request->address,
            'opening_hours' => $request->opening_hours,
            'closing_hours' => $request->closing_hours,
        ];

        if ($request->image) {
            $data['image'] = $request->image;
        }

        $restaurant->update($data);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil menambah resto baru',
            'data' => $restaurant
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
