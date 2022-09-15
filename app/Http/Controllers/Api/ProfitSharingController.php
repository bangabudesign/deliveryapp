<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProfitSharing;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProfitSharingController extends Controller
{
    public function index(Request $request)
    {
        $sharings = ProfitSharing::query()
            ->with('user')
            ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $sharings
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'notes' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'user_id' => 'User',
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
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ];

        $sharing = ProfitSharing::create($data);
        $sharing->save();
        $sharing->user;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $sharing
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function show(Request $request, $id)
    {
        $sharings = ProfitSharing::query()
                ->when($request->user()->role != 'ADMIN', function ($query) use ($request) {
                    $query->where('user_id', $request->user()->id);
                })
                ->findOrFail($id);

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $sharings
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required:exists,users,id',
            'amount' => 'required|numeric|min:50000',
            'notes' => 'required',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'numeric' => ':attribute harus berupa angka.',
            'min' => ':attribute minimal :min',
        ], [
            'user_id' => 'User',
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

        $sharing = ProfitSharing::findOrFail($id);

        $data = [
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'notes' => $request->notes,
        ];

        $sharing->update($data);
        $sharing->user;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Success',
            'data' => $sharing
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function stats(Request $request)
    {
        $trx_count = Order::paid()->whereMonth('created_at', now()->month)->count();
        $user_premium = User::premium()->count();
        $user = User::premium()->withCount([
                        'orders', 
                        'orders as order_paid_count' => function ($query) {
                            $query->paid()->whereMonth('created_at', now()->month);
                    }])->get();
        $user_achieve = $user->where('order_paid_count', '>', 4)->count();
        
        $data = [
            'trx_count' => $trx_count,
            'profit_sharing' => $trx_count * 1000,
            'user_premium' => $user_premium,
            'user_achieve' => $user_achieve,
        ];

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan statistik user premium.',
            'data' => $data
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function sendProfit(Request $request)
    {
        if ($request->user()->role != 'ADMIN') {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }
        
        $trx_count = Order::paid()->whereMonth('created_at', now()->month)->count();
        $user = User::premium()->withCount([
                    'orders', 
                    'orders as order_paid_count' => function ($query) {
                        $query->paid()->whereMonth('created_at', now()->month);
                }])->get();
        $user_achieve = $user->where('order_paid_count', '>', 4);

        $amount = ($trx_count * 1000) / $user_achieve->count();

        foreach ($user_achieve as $achieve) {
            $data = [
                'user_id' => $achieve->id,
                'amount' => $amount,
                'notes' => 'Profit Sharing',
            ];
    
            ProfitSharing::create($data);
        }

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Profit sharing berhasil dibagikan.',
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
