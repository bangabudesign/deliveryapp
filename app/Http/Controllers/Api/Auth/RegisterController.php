<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\RegisterSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'required_without:phone', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'required_without:email', 'string', 'numeric', 'unique:users,phone'],
            'term' => ['required'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'required' => ':attribute tidak boleh kosong.',
            'min' => ':attribute minimal :min karakter.',
            'username' => ':attribute tidak valid.',
            'required_without' => ':attribute tidak boleh kosong.'
        ], [
            'name' => 'Nama',
            'email' => 'E-Mail',
            'phone' => 'WhatsApp',
            'term' => 'Syarat & Ketentuan',
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        $user->save();

        Notification::send($user, new RegisterSuccess());

        $response = [
            'status' => Response::HTTP_OK,
            'message' => 'Berhasil Register. Silakan Log In',
        ];

        return response()->json($response, Response::HTTP_OK);
    }
}
