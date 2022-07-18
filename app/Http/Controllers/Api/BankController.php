<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $banks = Bank::where('is_active', 1)->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $banks
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'is_active' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
        ], [
            'bank_name' => 'Nama Bank',
            'account_number' => 'Nomor Rekening',
            'account_name' => 'Atas Nama Rekening',
            'is_active' => 'status',
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
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_active' => $request->is_active,
        ];

        $banks = Bank::create($data);
        $banks->save();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $banks
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
            'is_active' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
        ], [
            'bank_name' => 'Nama Bank',
            'account_number' => 'Nomor Rekening',
            'account_name' => 'Atas Nama Rekening',
            'is_active' => 'status',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $bank = Bank::findOrFail($id);

        $data = [
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_active' => $request->is_active,
        ];

        $bank->update($data);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $bank
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
