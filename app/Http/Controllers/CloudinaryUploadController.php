<?php

namespace App\Http\Controllers;

use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class CloudinaryUploadController extends Controller
{
    protected $cloudinaryService;


    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function showForm()
    {
        return view('admin.cloudinary.upload');
    }

    public function upload(Request $request)
    {
    $request->validate([
        'image' => 'required|file|image|max:2048',
    ]);

    $file = $request->file('image');
    $folder = 'ao'; // Tên thư mục bạn muốn upload vào

    $uploadResponse = $this->cloudinaryService->uploadImage($file->getPathname(), $folder);

    return response()->json($uploadResponse);
    }

}
