<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $drivers = User::query()
            ->where('role', 'DRIVER')
            ->when($request->get('limit'), function ($query) use ($request) {
                $query->limit($request->get('limit'));
            })
            ->when($request->get('is_working'), function ($query) use ($request) {
                $query->where('is_working', $request->get('is_working'));
            })
            ->get();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Menampilkan Data Driver',
            'data' => $drivers
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
