<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $bonuses = Bonus::query()
            ->with('merchant', 'order')
            ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                $query->where('merchant_id', $request->user()->id);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $bonuses
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required:exists,orders,id',
            'merchant_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'notes' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'order_id' => 'Order',
            'merchant_id' => 'Driver',
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
            'merchant_id' => $request->merchant_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ];

        $bonus = Bonus::create($data);
        $bonus->save();
        $bonus->merchant;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $bonus
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show(Request $request, $id)
    {
        $bonus = Bonus::query()
                ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                    $query->where('merchant_id', $request->user()->id);
                })
                ->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $bonus
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required:exists,orders,id',
            'merchant_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'notes' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'order_id' => 'Order',
            'merchant_id' => 'Driver',
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

        $bonus = Bonus::findOrFail($id);

        $data = [
            'order_id' => $request->order_id,
            'merchant_id' => $request->merchant_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ];

        $bonus->update($data);
        $bonus->merchant;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $bonus
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
