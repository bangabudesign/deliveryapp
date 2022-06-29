<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $drivers = User::query()->with('orders')
            ->where('role', 'DRIVER')
            ->when($request->get('limit'), function ($query) use ($request) {
                $query->limit($request->get('limit'));
            })
            ->when($request->get('is_working'), function ($query) use ($request) {
                $query->where('is_working', $request->get('is_working'));
                $query->whereDoesntHave('orders', function (Builder $q) {
                    $q->where('status', 'RECEIVED');
                    $q->orWhere('status', 'TAKEN');
                });
            })
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $drivers
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if ($request->user()->role != 'ADMIN') {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'motor_type' => 'required',
            'vehicle_number' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'password' => 'required|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
            'motor_type' => 'Motor Type',
            'vehicle_number' => 'No Polisi',
            'lat' => 'Latitude',
            'lng' => 'Longtitude',
            'password' => 'Password',
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
            'name' => $request->name,
            'phone' => $request->phone,
            'motor_type' => $request->motor_type,
            'vehicle_number' => $request->vehicle_number,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'role' => 'DRIVER',
            'is_working' => 1,
            'password' => Hash::make($request->password),
        ];

        $driver = User::create($data);
        $driver->save();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $driver
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id)
    {
        $driver = User::where('role', 'DRIVER')->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $driver
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'motor_type' => 'required',
            'vehicle_number' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'password' => 'nullable|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
            'motor_type' => 'Motor Type',
            'vehicle_number' => 'No Polisi',
            'lat' => 'Latitude',
            'lng' => 'Longtitude',
            'password' => 'Password',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $driver = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'motor_type' => $request->motor_type,
            'vehicle_number' => $request->vehicle_number,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'is_working' => 1,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $driver->update($data);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $driver
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
