<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\MerchantWithdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = Withdrawal::query()
            ->with('user')
            ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $withdrawals
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if ($request->user()->role == 'ADMIN') {
            $user = User::findOrFail($request->user_id);
        } else {
            $user = $request->user();
        }

        if ($request->amount > $user->total_balance) {
            $response = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Saldo tidak cukup',
                'errors' => ['saldo' => 'Saldo tidak cukup']
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'admin_fee' => 'required|numeric|min:0',
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required'
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'user_id' => 'User',
            'amount' => 'Nominal',
            'admin_fee' => 'Biaya Admin',
            'bank_name' => 'Bank Tujuan',
            'account_number' => 'Nomor Rekening',
            'account_name' => 'Atas Nama'
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
            'amount' => $request->amount,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'status' => 'PENDING',
        ];

        if ($request->user()->role == 'ADMIN') {
            $data['user_id'] = $request->user_id;
            $data['admin_fee'] = $request->admin_fee;
        } else {
            $data['user_id'] = $request->user()->id;
            $data['admin_fee'] = 5000;
        }

        $withdrawal = Withdrawal::create($data);
        $withdrawal->save();
        $withdrawal->user;

        Notification::send($withdrawal->user, new MerchantWithdrawal($withdrawal));

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $withdrawal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show(Request $request, $id)
    {
        $withdrawal = Withdrawal::query()
                ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                })
                ->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $withdrawal
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($request->user()->role != 'ADMIN') {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'admin_fee' => 'required|numeric|min:0',
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required'
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'user_id' => 'User',
            'amount' => 'Nominal',
            'admin_fee' => 'Biaya Admin',
            'bank_name' => 'Bank Tujuan',
            'account_number' => 'Nomor Rekening',
            'account_name' => 'Atas Nama'
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
            'amount' => $request->amount,
            'admin_fee' => $request->admin_fee,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'confirmed_at' => Carbon::now(),
        ];

        if ($request->user()->role == 'ADMIN') {
            $data['user_id'] = $request->user_id;
        } else {
            $data['user_id'] = $request->user()->id;
        }

        if ($request->user()->role == 'ADMIN') {
            $data['status'] = $request->status;
        }

        $withdrawal->update($data);
        $withdrawal->user;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $withdrawal
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
