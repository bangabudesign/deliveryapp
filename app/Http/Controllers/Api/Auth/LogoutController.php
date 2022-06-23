<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Log Out',
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function logoutAll(Request $request)
    {
        $request->user()->tokens()->delete();

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Log Out',
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
