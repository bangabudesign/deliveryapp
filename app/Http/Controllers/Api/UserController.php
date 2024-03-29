<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->where('role', 'USER')
            ->when($request->get('q'), function ($query) use ($request) {
                $query->where('name', 'LIKE', '%'.$request->get('q').'%');
            })
            ->when($request->get('limit'), function ($query) use ($request) {
                $query->limit($request->get('limit'));
            })
            ->when($request->get('premium'), function ($query) use ($request) {
                $query->where('is_premium', 1);
            })
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $users
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
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'password' => 'required|min:8',
            'is_premium' => 'required|in:0,1',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
            'in' => ':attribute tidak valid.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
            'lat' => 'Latitude',
            'lng' => 'Longtitude',
            'password' => 'Password',
            'is_premium' => 'Premium Status',
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
            'lat' => $request->lat,
            'lng' => $request->lng,
            'role' => 'USER',
            'password' => Hash::make($request->password),
        ];

        $user = User::create($data);
        $user->save();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $user
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = User::where('role', 'DRIVER')->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $user
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'role' => 'required',
            'motor_type' => 'required_if:role,DRIVER',
            'vehicle_number' => 'required_if:role,DRIVER',
            'password' => 'nullable|min:8',
            'is_premium' => 'required|in:0,1',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'required_if' => ':attribute tidak boleh kosong jika role adalah :role.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
            'in' => ':attribute tidak valid.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
            'lat' => 'Latitude',
            'lng' => 'Longtitude',
            'role' => 'Role',
            'password' => 'Password',
            'is_premium' => 'Premium Status',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ];

        if ($request->user()->role == 'ADMIN') {
            $data['is_premium'] = $request->is_premium;
        }

        if ($request->avatar) {
            $data['avatar'] = $request->avatar;
        }

        if ($request->role == 'DRIVER') {
            $data['role'] = $request->role;
            $data['motor_type'] = $request->motor_type;
            $data['vehicle_number'] = $request->vehicle_number;
            $data['is_working'] = $request->is_working;
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $user
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
