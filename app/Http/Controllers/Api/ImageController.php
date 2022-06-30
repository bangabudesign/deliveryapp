<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $images = Image::where('user_id', $request->user()->id)->get();

        $response = [
            'message' => 'success',
            'data' => $images,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
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
                'message' => 'Gagal mengupload gambar.',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $fileName = $request->user()->id . '-media-' . time() . '.' . $request->image->extension();
        $path = public_path('images/media');

        $data = [
            'user_id' => $request->user()->id,
            'image' => $fileName
        ];
        $request->image->move($path, $fileName);

        $images = Image::create($data);

        $response = [
            'message' => 'Berhasil mengupload gambar.',
            'data' => $images
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function destroy(Int $id)
    {
        $image = Image::findOrFail($id);
        $path = public_path('images/media');

        if (!empty($image->image) && file_exists($path . '/' . $image->image)) {
            unlink($path . '/' . $image->image);
        }

        $image->delete();

        $response = [
            'message' => 'Berhasil menghapus gambar.'
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
