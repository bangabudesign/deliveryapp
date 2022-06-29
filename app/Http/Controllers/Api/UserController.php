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
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
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
            'password' => 'nullable|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
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

        $user = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
            'lat' => $request->lat,
            'lng' => $request->lng,
        ];

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
