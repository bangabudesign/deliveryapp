<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::when($request->get('status') == 'active', function ($query) use ($request) {
            $query->active();
        })->orderBy('order','ASC')->get();

        $response = [
            'message' => 'success',
            'data' => $banners,
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        if ($request->user()->role != 'ADMIN') {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'image'         => 'nullable|image',
        ], [
            'image'         => ':attribute harus berupa foto.',
        ], [
            'image'         => 'Gambar',
        ]);

        if ($validator->fails()) {
            $response = [
                'message' => 'Gagal mengupload banner.',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $fileName = 'banner-' . time() . '.' . $request->image->extension();
        $path = public_path('images/banner');

        $data = [
            'name'          => $fileName,
            'image'         => $fileName,
            'link'          => '-',
            'order'         => 0,
            'target_blank'  => 0,
            'is_active'     => 0,
        ];
        $request->image->move($path, $fileName);

        $banner = Banner::create($data);

        $response = [
            'message' => 'Berhasil mengupload banner.',
            'data' => $banner
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function update(Request $request, Int $id)
    {
        if ($request->user()->role != 'ADMIN') {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(), [
            'name'          => 'nullable',
            'link'          => 'nullable',
            'order'         => 'numeric',
            'target_blank'  => 'numeric',
            'is_active'     => 'numeric',
        ], [
            'numeric'         => ':attribute harus berupa angka.',
        ], [
            'name'          => 'Nama',
            'link'          => 'Link',
            'order'         => 'Urutan',
            'target_blank'  => 'Target',
            'is_active'     => 'Status',
        ]);

        if ($validator->fails()) {
            $response = [
                'message' => 'Gagal memperbarui data.',
                'errors' => $validator->messages()
            ];
            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = [
            'name'          => $request->name,
            'link'          => $request->link,
            'order'         => $request->order,
            'target_blank'  => $request->target_blank,
            'is_active'     => $request->is_active,
        ];

        $banner = Banner::findOrFail($id);
        $banner->update($data);

        $response = [
            'message' => 'Berhasil memperbarui data.',
            'data' => $banner
        ];
        return response()->json($response, Response::HTTP_OK);
    }

    public function destroy(Request $request,Int $id)
    {
        if ($request->user()->role != 'ADMIN') {
            $response = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'message' => 'Unauthorized',
            ];
            return response()->json($response, Response::HTTP_UNAUTHORIZED);
        }

        $banner = Banner::findOrFail($id);
        $path = public_path('images/banner');

        if (!empty($banner->image) && file_exists($path . '/' . $banner->image)) {
            unlink($path . '/' . $banner->image);
        }

        $banner->delete();

        $response = [
            'message' => 'Berhasil menghapus banner.'
        ];
        return response()->json($response, Response::HTTP_OK);
    }
}
