<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $deposits = Deposit::query()
            ->with('user')
            ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $deposits
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required:exists,users,id',
            'bank_id' => 'required:exists,banks,id',
            'amount' => 'required|numeric|min:50000',
            'admin_fee' => 'required|numeric|min:0',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'user_id' => 'User',
            'bank_id' => 'Tujuan Pembayaran',
            'amount' => 'Nominal',
            'admin_fee' => 'Biaya Admin',
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
            'bank_id' => $request->bank_id,
            'amount' => $request->amount,
            'admin_fee' => $request->admin_fee,
            'status' => 'PENDING',
        ];

        if ($request->user()->role == 'ADMIN') {
            $data['user_id'] = $request->user_id;
        } else {
            $data['user_id'] = $request->user()->id;
        }

        $deposit = Deposit::create($data);
        $deposit->save();
        $deposit->user;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $deposit
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show(Request $request, $id)
    {
        $deposit = Deposit::query()->with('bank')
                ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                })
                ->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $deposit
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $deposit = Deposit::findOrFail($id);

        if ($request->user()->id != $deposit->user_id) {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required:exists,users,id',
            'bank_id' => 'required:exists,banks,id',
            'amount' => 'required|numeric|min:50000',
            'admin_fee' => 'required|numeric|min:0',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'user_id' => 'User',
            'bank_id' => 'Tujuan Pembayaran',
            'amount' => 'Nominal',
            'admin_fee' => 'Biaya Admin',
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
            'bank_id' => $request->bank_id,
            'amount' => $request->amount,
            'admin_fee' => $request->admin_fee,
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

        $deposit->update($data);
        $deposit->user;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $deposit
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function uploadReceipt(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image',
        ], [
            'image' => ':attribute harus berupa foto.',
        ], [
            'image' => 'Gambar',
        ]);

        if ($validator->fails()) {
            $response = [
                'message' => 'Gagal mengupload bukti transfer.',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $deposit = Deposit::findOrFail($id);

        $fileName = $deposit->id . time() . '.' . $request->image->extension();
        $path = public_path('images/receipt');

        $data = [ 'receipt' => $fileName ];
        $request->image->move($path, $fileName);

        $deposit->update($data);
        $deposit->bank;

        $response = [
            'message' => 'Berhasil mengupload bukti transfer.',
            'data' => $deposit
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
