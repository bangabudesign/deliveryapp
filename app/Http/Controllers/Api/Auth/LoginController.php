<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string|min:8',
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'min' => ':attribute minimal :min karakter.',
            'username' => ':attribute tidak valid'
        ], [
            'username' => 'E-Mail atau WhatsApp',
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

        $login = $request->username;
        
        if (is_numeric($login)) {
            $user = User::where('phone', $login)->first();
        } else {
            $user = User::where('email', $login)->first();
        }

        if (!$user) {
            $response = [
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Akun tidak ditemukan',
            ];
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Password tidak sesuai.'],
            ]);
        }
        $token = $user->createToken('authToken')->plainTextToken;

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Log In',
            'token' => $token,
            'role' => $user->role
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
