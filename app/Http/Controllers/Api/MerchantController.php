<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{
    public function index(Request $request)
    {
        $merchants = User::query()
            ->where('role', 'MERCHANT')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $merchants
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
            'password' => 'required|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
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
            'role' => 'MERCHANT',
            'password' => Hash::make($request->password),
        ];

        $merchant = User::create($data);
        $merchant->save();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $merchant
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show($id)
    {
        $merchant = User::where('role', 'DRIVER')->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $merchant
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'password' => 'nullable|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min karakter.',
        ], [
            'name' => 'Nama',
            'phone' => 'WhatsApp',
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

        $merchant = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $merchant->update($data);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $merchant
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
