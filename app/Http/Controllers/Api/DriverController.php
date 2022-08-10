<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $lat = explode(',', $request->get('near_by'))[0] ?? null;
        $lng = explode(',', $request->get('near_by'))[1] ?? null;

        $drivers = User::where('role', 'DRIVER')
            ->when($request->get('q'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request->get('q').'%');
            })
            ->when($request->get('limit'), function ($query) use ($request) {
                $query->limit($request->get('limit'));
            })
            ->when($request->get('is_working'), function ($query) use ($request) {
                $query->where('is_working', $request->get('is_working'));
                $query->where('total_balance', '>=', 15000);
                $query->whereDoesntHave('orders', function (Builder $q) {
                    $q->where('status', 'RECEIVED');
                    $q->orWhere('status', 'TAKEN');
                });
            })
            ->when($request->get('near_by'), function ($query) use ($lat, $lng) {
                $query->select(
                    "*",
                    DB::raw("6371 * acos(cos(radians(" . $lat . "))
                    * cos(radians(users.lat)) 
                    * cos(radians(users.lng) - radians(" . $lng . ")) 
                    + sin(radians(" .$lat. ")) 
                    * sin(radians(users.lat))) AS distance"))
                ->havingRaw('distance < 10')
                ->orderBy('distance');
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
            'phone' => 'required|numeric|unique:users,phone',
            'motor_type' => 'required',
            'vehicle_number' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'password' => 'required|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
            'unique' => ':attribute sudah digunakan coba yang lain.',
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
            'is_working' => $request->is_working,
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
            'is_working' => $request->is_working,
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

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_working' => 'required|integer',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'integer' => ':attribute harus berupa angka.',
        ], [
            'is_working' => 'Status',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $driver = $request->user()->update(['is_working' => $request->is_working]);
        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $driver
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
