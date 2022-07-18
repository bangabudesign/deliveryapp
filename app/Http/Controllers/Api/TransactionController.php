<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::query()
            ->with('driver')
            ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                $query->where('driver_id', $request->user()->id);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $transactions
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required:exists,orders,id',
            'driver_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'notes' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'order_id' => 'Order',
            'driver_id' => 'Driver',
            'amount' => 'Nominal',
            'notes' => 'Catatan',
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
            'order_id' => $request->order_id,
            'driver_id' => $request->driver_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ];

        $transaction = Transaction::create($data);
        $transaction->save();
        $transaction->driver;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $transaction
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show(Request $request, $id)
    {
        $transaction = Transaction::query()
                ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                    $query->where('driver_id', $request->user()->id);
                })
                ->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $transaction
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required:exists,orders,id',
            'driver_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'notes' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'order_id' => 'Order',
            'driver_id' => 'Driver',
            'amount' => 'Nominal',
            'notes' => 'Catatan',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Data yang di input tidak valid',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $transaction = Transaction::findOrFail($id);

        $data = [
            'order_id' => $request->order_id,
            'driver_id' => $request->driver_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ];

        $transaction->update($data);
        $transaction->driver;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $transaction
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
